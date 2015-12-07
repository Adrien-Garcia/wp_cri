<?php

/**
 *
 * This file is part of project 
 *
 * File name : EntityManager.php
 * Project   : wp_cridon
 *
 * @author Etech
 * @contributor Fabrice MILA
 *
 */

class EntityManager {
    
    private $entities = array();
    private $dbAdapter;
    public  $per_page = 10;
    
    public function __construct() {
        //Interagir avec la base de données
        $this->dbAdapter = new EntityDatabaseAdapter();
    }
    /**
     * Add entity name
     * 
     * @param string $entity
     */
    public function addEntity( $entity ){
        $this->entities[] = $entity.'Entity';
    }
    
    /**
     * Save instance of entity in registry
     */
    public function create(){
        if( !empty( $this->entities ) ){
            foreach( $this->entities as $entity ){
                //create entity
                $object = EntityFactory::get($entity);
                if( $object != null ){
                    //Save this in registry
                    EntityRegistry::addEntity($object,$entity);                            
                }
            }
        }
    }
    
    /**
     * Execute query
     * 
     * @param string $sql
     * @return mixed
     */
    public function query( $sql ){
        //Execution d'une requête SQL
        return $this->dbAdapter->query($sql);
    }
    
    /**
     * Get result of query 
     * @param array|string $options
     * @return mixed
     */
    public function getResults($options) {
        //Obtention des résultats d'une requête
        $objects = $this->dbAdapter->getResults($options);
        //Associer les différents objets(modèles associés au requête) au résultat
        $objects = $this->processObjects($objects,$options);
        return $objects;
    }
    
    /**
     * Find and setup pagination
     * 
     * @param array $options
     * @return mixed
     */
    public function paginate($options=array()) {
        $options['page'] = empty($options['page']) ? 1 : intval($options['page']);
        $options['per_page'] = empty($options['per_page']) ? $this->per_page : intval($options['per_page']);
        //Fetch in database
        $objects = $this->dbAdapter->getResults($options);
        //Construct array of object
        $objects = $this->processObjects($objects,$options);
        //Obtenir le nombre total d'élément pour la requête sans la contrainte du nombre d'élément par liste
        $total_count = $this->count($options);
        $response = array(
            'objects' => $objects,
            'total_pages' => ceil($total_count/$options['per_page']),
            'page' => $options['page']
        );
        return $response;
    }
    
    /**
     * Count results
     * 
     * @param type $options
     * @return integer
     */
    protected function count($options=array()) {
        $clauses = $this->dbAdapter->getSqlSelectClauses($options);
        $clauses['select'] = 'SELECT COUNT(*) AS count';
        if( isset( $clauses['limit'] ) ){
            unset( $clauses['limit'] );            
        }
        $sql = implode(' ', $clauses);
        $result = $this->dbAdapter->getVar($sql);
        return $result;
    }
    
    /**
     * Construct array of object Entity for final result
     * 
     * @param array $objects Result of query
     * @param array $options Options of query
     * @return array
     */
    protected function  processObjects( $objects,$options ){
        $results = array();
        //Parcours du résultat de la requête
        foreach ($objects as $object ){
            //To store result
            $obj = new Entity();
            unset( $obj->mvc_model );
            //Fetch select object to construct array 
            //Voir dans l'option de la requête les modèles séléctionnés
            foreach( $options['select'] as $select ){
                //Attribut du résultat correspondant au nom du modèle associé au SELECT
                $attr = strtolower($select);
                //Create object for data in fields of result query
                $newObj = $this->newObject($object, $select);
                $this->appendLink($newObj,$attr);//Ajouter le lien 
                $obj->{$attr} = $newObj; 
            }
            $results[] = $obj;
        }
        return $results;
    }
    
    /**
     * Ajouter le lien de l'objet
     * 
     * @param object $object
     * @param string $model
     */
    protected function appendLink( &$object,$model ){
        //Seulement pour le modèle matière
        if( !is_null( $object ) && isset( $object->id ) && ( $model == 'matiere' ) ){
            $option = array(
                'controller' => $model.'s',
                'action' => 'show',
                'id' => $object->id
            );
            $object->url = MvcRouter::public_url( $option );
        }
    }
    /**
     * Create new object
     * 
     * @param object $data Result of query
     * @param string $entity Name of entity
     * @return object
     */
    protected function newObject($data,$entity) {
        //$data est un objet stdClass
        //Construct object
        $instance = EntityFactory::get( $entity.'Entity' ); 
        $isNull = true;
        foreach( $instance->fields as $key => $field ){
            //setup attributes
            /*
             * Associer les données propres au modèle concerné
             * Ex: nous avons $data->Matiere1 = 'A' et $data->Support1 = 'Urgent'
             * alors pour la matière nous aurons $instance->code = 'A'
             * et pour le support $instance->label = 'Urgent'
             * $entity dépend du modèle courant dans le tableau $options['select']
             */
            $instance->{$field} = $data->{$entity.$key};
            if( !empty( $data->{$entity.$key} ) ){
                $isNull = false;
            }
        }
        if( isset( $instance->mvc_model ) ){
            unset( $instance->mvc_model );
        }
        //Si aucun résultat n'est associé au model courant
        if( $isNull ){
            //we not have object in result
            return null;
        }
        return $instance;
    }
}
