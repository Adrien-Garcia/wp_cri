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
                $obj->{$attr} = $this->newObject($object, $select);                
            }
            $results[] = $obj;
        }
        $results = $this->splitArray($results);
        return $results;
    }
    /**
    * Découper le résultat en des tableaux contenant chaqun les mêmes questions.
    *  
    * @param mixed $data
    * @return mixed
    */
    protected function splitArray( $data ){
        if( empty($data) ){
            return null;
        }
        $tmpQ = $data[0];//initialisation
        if( !isset( $tmpQ->question ) || empty( $tmpQ->question ) ){
            return $data;//Ce n'est pas la peine d'aller plus loin si le résulat ne contient aucune question
        }
        $aSplit = array();
        $tmp = array();
        //Regroupage des mêmes questions
        foreach ( $data as $key=>$value ){
            if( $tmpQ->question->id == $value->question->id ){//Si la question est toujours la même alors stocker la valeur
                $tmp[] = $value;
                $tmpQ = $value;
            }else{
                $aSplit[] = $tmp;
                $tmp = array();
                $tmp[] = $value;
                $tmpQ = $value;// l'itération courante
            }
            if( count( $data ) - 1 === $key ){ // Si nous arrivons déjà à la fin
                $aSplit[] = $tmp;
            }
        }
        return $this->appendDocuments( $aSplit );
    }
    /**
    * Ajouter les documents sur chaque question
    * 
    * @param mixed $data
    * @return mixed
    */
    protected function appendDocuments( $data ){
        if( empty($data) ){
            return null;
        }
        $results = array();//contenant le résultat final
        foreach( $data as $value ){
        /**
         * L'indice 0 correspond à la première question car nous pouvons avoir 3 résultats par exemple
         * pour la même question avec 3 documents différents (du fait de l'association des documents à requête).
         */
        $newData = $value[0];
        //Contenant la liste des documents associés à une question
            $documents = array();
            //Parcourir les documents
            foreach( $value as $v ){
                //Vérification de la présence de document
                if( empty( $v->document ) ){
                    continue;
                }
                $documents[] = $v->document;
            }
            //Associer les documents s'il y en a, à la question
            $newData->{documents} = $documents;
            if( empty( $newData->document ) || isset( $newData->document ) ){
                //Supprimer l'attribut document associé au premier élément de la liste
                unset( $newData->document );
            }
            $results[] = $newData;
        } 
        return $results;
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
