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
        return $this->dbAdapter->query($sql);
    }
    
    /**
     * Get result of query 
     * @param array|string $options
     * @return mixed
     */
    public function getResults($options) {
        $objects = $this->dbAdapter->getResults($options);
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
        foreach ($objects as $object ){
            //To store result
            $obj = new Entity();
            unset( $obj->mvc_model );
            //Fetch select object to construct array 
            foreach( $options['select'] as $select ){
                $attr = strtolower($select);
                //Create object for data in fields of result query
                $obj->{$attr} = $this->newObject($object, $select);                
            }
            $results[] = $obj;
        }
        return $results;
    }
    /**
     * Create new object
     * 
     * @param array $data Result of query
     * @param string $entity Name of entity
     * @return object
     */
    protected function newObject($data,$entity) {
        //Construct object
        $instance = EntityFactory::get( $entity.'Entity' ); 
        $isNull = true;
        foreach( $instance->fields as $key => $field ){
            //setup attributes
            $instance->{$field} = $data->{$entity.$key};
            if( !empty( $data->{$entity.$key} ) ){
                $isNull = false;
            }
        }
        if( isset( $instance->mvc_model ) ){
            unset( $instance->mvc_model );
        }
        if( $isNull ){
            //we not have object in result
            return null;
        }
        return $instance;
    }
}
