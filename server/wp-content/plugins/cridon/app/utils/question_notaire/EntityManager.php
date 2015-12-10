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
        //Si l'option contient une requête SQL alors il faut le parser pour en extraire les modèles utilisés
        if( isset( $options['query'] ) ){
            /**
             * Obtention pour la requête de: $options['select']
             * nécessaire pour reconstruire le résultat par un collection d'objet modèle             
             */
            $options = $this->extractSelectQuery($options);
        }
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
        //Les options de pagination
        $options['page'] = empty($options['page']) ? 1 : intval($options['page']);
        $options['per_page'] = empty($options['per_page']) ? $this->per_page : intval($options['per_page']);
        $_options = $options;//Faire un copie de l'option avant le traitement dans le bloc suivant
        //Si l'option contient une requête SQL alors il faut le parser pour en extraire les modèles utilisés
        if( isset( $options['query'] ) ){
            /**
             * Obtention pour la requête de: $options['select']
             * nécessaire pour reconstruire le résultat par un collection d'objet modèle             
             */
            $options = $this->extractSelectQuery($options);
        }
        //Fetch in database
        $objects = $this->dbAdapter->getResults($options);        
        //Construct array of object
        $objects = $this->processObjects($objects,$options);
        //Obtenir le nombre total d'élément pour la requête sans la contrainte du nombre d'élément par liste
        $total_count = $this->count($_options);
        $response = array(
            'objects' => $objects,
            'total_pages' => ceil($total_count/$options['per_page']),
            'page' => $options['page']
        );
        return $response;
    }
    
    /**
     * Permet de supprimer une clé de tableau
     * 
     * @param array $array Tableau à traiter
     * @param string $key Clé du tableau
     */
    protected function eraseKeyArray( &$array,$key ){
        if( isset($array[$key]) ){
            unset($array[$key]);
        }
    }
    /**
     * Count results
     * 
     * @param type $options
     * @return integer
     */
    protected function count($options=array()) {
        if( isset( $options['query'] ) ){
            //Supprimer les options de pagination pour avoir le résultat exacte de la requête
            $this->eraseKeyArray($options,'page');
            $this->eraseKeyArray($options, 'per_page');
            //Si l'option possède une requête spécial pour faire le comptage
            if( isset( $options['query_count'] ) ){
                $query = $options['query_count'];
            }else{
                //Faire le néttoyage nécessaire pour enlever les limites dans la requête
                $options = $this->extractSelectQuery($options,true);
                $query = $options['query'];
            }
            //Obtention du résultat
            return $this->dbAdapter->getVar($query);
        }
        //Les options ne possèdent pas de requête spécial ( cas des requêtes complexes )
        $clauses = $this->dbAdapter->getSqlSelectClauses($options);
        $clauses['select'] = 'SELECT COUNT(*) AS count';
        if( isset( $clauses['limit'] ) ){
            unset( $clauses['limit'] );            
        }
        $sql = implode(' ', $clauses);
        //Obtention du résultat
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
        $results = $this->splitArray($results);
        return $results;
    }
    
    /**
     * Remplacer le [LIMIT] dans la chaine de caractère correspondant à la requête SQL
     * 
     * @param string $query
     * @param array $options
     * @return string
     */
    protected function replaceLimit( $query,$options ){
        $limit = '';//Par défaut, aucune limite
        //Vérifier la présence des options de pagination
        if( isset($options['page']) && isset( $options['per_page'] ) ){
            //Obtention de la valeur du LIMIT 
            //En retour nous aurons par ex : LIMIT 0,10
            $limit = $this->dbAdapter->getLimitSql($options);                
        }
        //Trouver [LIMIT] dans la chaine et le remplacer par valeur du LIMIT
        return preg_replace('/\[LIMIT]/', $limit, $query);
    }
    
    /**
     * Ajouter les champs dans le SELECT
     *  
     * @param array $select contient le nom des modèles
     * @param string $query
     * @return string
     * @throws \RuntimeException
     */
    protected function constructFieldsFromQuery( $select,$query ){        
        $ptn = "/SELECT([a-zA-Z,-_0-9\s]+)FROM/";
        $new = array();
        foreach( $select as $v ){
            $new[] = trim($v,'.');
        }
        $fields = array();
        foreach( $new as $v ){
            //Obtention du modèle
            if( !$instance = EntityRegistry::get( $v.'Entity' ) ){
                throw new \RuntimeException('Unknown model :'.$v);
            }
            //Parcours des champs de la table du modèle
            foreach( $instance->fields as $k1 => $v1 ){
                $fields[] = $v.'.'.$v1.' AS '.$v.$k1;
            }
        }
        $f = implode(',', $fields);
        //Remplacement par au niveau du SELECT
        return preg_replace($ptn, 'SELECT '.$f.' FROM', $query);
    }
    /**
     * Reconstruire le SELECT pour avoir dans les différents des modèles
     * 
     * @param array $options
     * @param boolean $count Si la requête est utlisée pour faire un décompte de résultat
     * @throws \RuntimeException
     */
    protected function extractSelectQuery( $options,$count = false ){
        $query = $options['query'];
        $ptn1 = "/SELECT([a-zA-Z,-_0-9\s]+)FROM/";
        if( preg_match($ptn1, $query, $matches) ){
            if( $count ){                
                $query = preg_replace($ptn1, 'SELECT COUNT(*) AS count FROM', $query); 
            }
            else{
                $select = $matches[1]; 
                $ptn = "/([a-zA-Z]+)/";
                if( preg_match_all($ptn, $select, $matches) ){
                    $aSelect = array_unique($matches[1]);
                    //Ajout des champs au niveau du SELECT
                    $query = $this->constructFieldsFromQuery($aSelect,$query); 
                }else{
                    throw new \RuntimeException('Failed to extract the model.');
                }
            }
            $query = $this->replaceLimit($query, $options);
            $_options = array(
                'query' => $query,
                'select' => $aSelect
            );
            return array_merge($options, $_options);
        }else{
            throw new \RuntimeException('No model found');
        }
    }
    /**
    * Découper le résultat en des tableaux contenant chacun les mêmes questions.
    *  
    * @param mixed $data
    * @return mixed
    */
    protected function splitArray( $data ){
        if( empty($data) ){
            return array();//Erreur au niveau de l'utilisation du foreach
        }
        // on remet le pointeur au début
        reset($data);
        $tmpQ = current($data);//initialisation
        if( !isset( $tmpQ->question ) || empty( $tmpQ->question ) ){
            return $data;//Ce n'est pas la peine d'aller plus loin si le résulat ne contient aucune question
        }
        $aSplit = array();
        $tmp = array();
        //Regroupage des mêmes questions
        while (!empty($data)) {
            $value = array_shift($data);
            if( $tmpQ->question->id == $value->question->id ){//Si la question est toujours la même alors stocker la valeur
                $tmp[] = $value;
                $tmpQ = $value;
            }else{
                $aSplit[] = $tmp;
                $tmp = array();
                $tmp[] = $value;
                $tmpQ = $value;// l'itération courante
            }
            if( count( $data )  === 1  ){ // Si nous arrivons déjà à la fin
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
            reset($value);
            $newData = current($value);
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
            $newData->documents = $documents;
            if( empty( $newData->document ) || isset( $newData->document ) ){
                //Supprimer l'attribut document associé au premier élément de la liste
                unset( $newData->document );
            }
            $results[] = $newData;
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
