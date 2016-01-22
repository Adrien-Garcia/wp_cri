<?php

/**
 *
 * This file is part of project 
 *
 * File name : CridonMvcModel.php
 * Project   : wp_cridon
 *
 * @author Etech
 * @contributor Fabrice MILA
 *
 */
namespace App\Override\Model;

class CridonMvcModel extends \MvcModel{
    
    protected static $schema_db = array();//description of table

    /**
     * @var string : default join type
     */
    protected $defaultJoin = 'LEFT';


    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Get schema of table
     */
    protected function init_schema() {
        if( isset(CridonMvcModel::$schema_db[$this->table_reference]) ){
            $this->schema = CridonMvcModel::$schema_db[$this->table_reference];
            $this->db_adapter->schema = CridonMvcModel::$schema_db[$this->table_reference];            
        }else{
            parent::init_schema();
            CridonMvcModel::$schema_db[$this->table_reference] = $this->schema;
        }
    }
    
    public function convertSchemaToString($model,$alias,$index = -1){
        $schema = CridonMvcModel::$schema_db['`'.$model->table.'`'];
        $fields = array();
        foreach( $schema as $k => $v ){
            $field = $alias.'.'.$k;
            if( $index !== -1 ){
                $field .= ' AS '.$k.$index;
            }
            $fields[] = $field;
        }
        return implode(',',$fields);
    }
    
    public function getSchemaByTable($table){
        return isset(CridonMvcModel::$schema_db['`'.$table.'`']) ? CridonMvcModel::$schema_db['`'.$table.'`'] : false;
    }
    
    /**
     * Get shema by model
     * 
     * @param objet $model
     * @return array
     */
    public function getSchemaByModel($model){
        return (isset(CridonMvcModel::$schema_db['`'.$model->table.'`'])) ? CridonMvcModel::$schema_db['`'.$model->table.'`'] : null;
    }
       
    /**
     * Find one by options (condition, join,..)
     *
     * Option where with one condition : $options['where'] = 'ModelName.field = 4'
     * Option where with multiple conditions : $options['where'] = array(
     *                                              'ModelName.filed1 = 4',
     *                                              'ModelName.filed2 = "string"',
     *                                         )
     *
     * Option join :  $options['joins'] = array(
     *                                          'Fonction' => array(
     *                                              'fields' => array(
     *                                                  'id',
     *                                                  'label'
     *                                              ),
     *                                              'foreign_key' => 'id_fonction'
     *                                          ),
     *                                          'Matiere' => array(
     *                                              'fields' => array(
     *                                                  'code',
     *                                                  'label'
     *                                                  'short_label',
     *                                                  'picto',
     *                                              ),
     *                                              'foreign_key' => 'id_matiere'
     *                                          )
     *                                      )
     * 'Fonction', 'Matiere' : model name
     * 'foreign_key' : must be a fereign_key in the associated model
     *
     * @param array $options
     * @return array|null|object|void
     */
    public function findOneBy($options = array())
    {
        global $wpdb;

        // prepare fields list
        $fieldsModels = $this->convertSchemaToString($this, $this->name);

        // init fields
        $fields = array($fieldsModels);

        // process options
        $processOptions = $this->processOptions($options);
        $fields = array_merge($fields, $processOptions['fields']);

        // fields
        $field = implode(', ', $fields);

        // init query
        $query   = " SELECT {$field} ";

        // from block
        $query  .= " FROM {$this->table} AS {$this->name} ";

        // joins block
        if (count($processOptions['joins']) > 0) {
            $join = implode(', ', $processOptions['joins']);
            $query .= " {$join} ";
        }

        // init where clause (may be completed with options parameters)
        $query .= " WHERE 1 = 1 ";

        if(isset($options['where'])){
            if (is_string($options['where']) && !empty($options['where'])) {
                $query .= " AND {$options['where']} ";
            } elseif(is_array($options['where'])) {
                $query .= ' AND ' . implode(' AND ', $options['where']);
            }
        }

        if ($results = $wpdb->get_row($query)) {
            $results = $this->processObjects($results, $options);
        }

        return $results;
    }

    /**
     * Process Join Options
     *
     * @param array $options
     * @return array
     * @throws Exception
     */
    protected function processOptions($options)
    {
        $processOptions = array(
            'joins' => array(),
            'fields' => array()
        );
        if (isset($options['joins']) && is_array($options['joins'])) {
            foreach ($options['joins'] as $model => $join) {
                if (isset($join['foreign_key'])) { // join condition required
                    $modelTable = mvc_model($model)->table;
                    $joinType   = (isset($join['type'])) ? $join['type'] : $this->defaultJoin;
                    if (isset($join['fields'])) { // check join fields list
                        if (is_string($join['fields'])) { // one field
                            $fields[] = $model . '.' . $join['fields'] . ' AS ' . $model . $join['fields']; // use $model as default alias
                        } elseif (is_array($join['fields'])) { // more fields
                            foreach ($join['fields'] as $field) {
                                $processOptions['fields'][] = $model . '.' . $field . ' AS ' . $model . $field;
                            }
                        }
                    }
                    $on = " {$model}.id = {$this->name}.{$join['foreign_key']}";
                    $processOptions['joins'][] = $joinType . " JOIN {$modelTable} AS {$model} ON  {$on}";
                }
            }
        }

        return $processOptions;
    }

    /**
     * Append associations to the object as properties
     *
     * @param mixed $objects
     * @param array $options
     * @return mixed
     */
    protected function processObjects($objects, $options)
    {
        if (isset($options['joins']) && is_array($options['joins'])) {
            foreach ($options['joins'] as $model => $join) {
                if (isset($join['fields'])) { // check join fields list
                    $assocModel = strtolower($model);
                    $objects->$assocModel = new \MvcModelObject(new $model);
                    if (is_string($join['fields'])) { // one field
                        $assocField = $model . $join['fields']; // get association property
                        if (property_exists($objects, $assocField)) {
                            $objects->$assocModel->$join['fields'] = $objects->$assocField;
                            unset($objects->$assocField);
                        }
                    } elseif (is_array($join['fields'])) { // more fields
                        foreach ($join['fields'] as $field) {
                            $assocField = $model . $field;
                            if (property_exists($objects, $assocField)) {
                                $objects->$assocModel->$field = $objects->$assocField;
                                unset($objects->$assocField);
                            }
                        }
                    }
                }
            }
        }

        return $objects;
    }
    
    public function paginate($options = array()){
        $options['page'] = empty($options['page']) ? 1 : intval($options['page']);
        $options['per_page'] = empty($options['per_page']) ? $this->per_page : intval($options['per_page']);
        $q =  new \App\Override\Model\QueryConstructorModel($this,$options);
        $total_count = $q->count();
        $response = array(
            'objects' => $q->getResults(),
            'total_objects' => $total_count,
            'total_pages' => ceil($total_count/$options['per_page']),
            'page' => $options['page']
        );
        return $response;
    }
    
    public function find($options = array()){
        $q =  new \App\Override\Model\QueryConstructorModel($this,$options);
        return $q->getResults();
    }
    
    protected function convertToDateSql($d,$format = 'd/m/Y'){
        $d = urldecode($d);
        $dt = DateTime::createFromFormat($format, $d);
        return ($dt) ? $dt->format('Y-m-d') : false;
    }
    
    public function find_by_id($id, $options=array()) {
        $options['conditions'] = array($this->name.'.'.$this->primary_key => $id);
        $q =  new \App\Override\Model\QueryConstructorModel($this,$options);
        $objects =  $q->getResults();
        $objects = $this->splitArray($objects,$this->primary_key);
        $object = isset($objects[0]) ? $objects[0] : null;
        return $object;
    }
    
    public function find_one($options=array()) {
        $options['limit'] = 1;
        $q =  new \App\Override\Model\QueryConstructorModel($this,$options);
        $objects =  $q->getResults();
        $object = isset($objects[0]) ? $objects[0] : null;
        return $object;
    }
    
    /**
     * Retreive all association object with the documents
     * 
     * @param integer id
     * @return \MvcModelObject
     */
    public function associatePostWithDocumentById($id){
        $id = esc_sql($id);
        $type = strtolower($this->name);
        $select  = "SELECT c,p,d";
        if( !empty($this->belongs_to) && array_key_exists('Matiere',$this->belongs_to) ){
            $select  .= ",m";
        }
        $query = $select."            
            FROM (
                SELECT c1.*
                FROM {$this->name} AS c1 
                JOIN Post AS p
                ON c1.post_id = p.ID 
                WHERE p.post_status = 'publish' AND c1.id = {$id}
            ) [{$this->name}] c
            JOIN Post p
            ON c.post_id = p.ID
            LEFT JOIN Document d ON (d.id_externe = c.id AND d.type = '{$type}' ) 
                ";
        if( !empty($this->belongs_to) && array_key_exists('Matiere',$this->belongs_to) ){
            $query  .= '
                LEFT JOIN Matiere m
                ON c.id_matiere = m.id
                    ';
        }
        $qs = new \App\Override\Model\QueryStringModel($query);
        $objects = $qs->getResults();
        $objects = $this->processAppendDocuments($objects);
        return (!empty($objects)) ? $objects[0] : null;
    }
    
    /**
    * Cut the result tables each containing the same objects.
    *  
    * @param mixed $data
    * @param string $comp Comparator
    * @param array $many Many-to-many relation
    * @return mixed
    */
    protected function splitArray( $data,$comp,$many = array( 'documents' => 'document') ){
        if( empty($data) ){
            return array();//initial
        }
        // start point
        reset($data);
        $tmpQ = current($data);//initialization
        $aSplit = array();
        $tmp = array();
        //Grouping the same objects
        while (!empty($data)) {
            $value = array_shift($data);
            if( $tmpQ->{$comp} == $value->{$comp} ){//If the object is still the same then store the value
                $tmp[] = $value;
                $tmpQ = $value;
            }else{
                $aSplit[] = $tmp;
                $tmp = array();
                $tmp[] = $value;
                $tmpQ = $value;// current
            }
            if( count( $data )  === 0  ){ // ending
                $aSplit[] = $tmp;
            }
        }
        return $this->appendOneToMany( $aSplit,$many );
    }
    
    /**
    * Add "oneTomany" relationships on each object
    * 
    * @param mixed $data
    * @param array $many Many-to-many relation
    * @return mixed
    */
    protected function appendOneToMany( $data,$many ){
        if( empty($data) ){
            return null;
        }
        $results = array();//results
        foreach( $data as $value ){
            /**
             * L'indice 0 correspond à au premier car nous pouvons avoir 3 résultats par exemple
             * pour le même objet avec 3 documents différents (du fait de l'association des documents à requête).
             */
            reset($value);
            $newData = current($value);
            //Contenant la liste des relations "manyToOne" associées à un objet
            $manyToOne = array();
            foreach( $many as $k0 => $v0 ){
                $manyToOne[$k0] = array();//Initialisation du tableau contenant les relations
            }
            //Parcourir les relations "manyToOne"
            foreach( $many as $k0 => $v0 ){
                $lists = array();//stockage
                foreach( $value as $v ){
                    //Vérification de la présence de la relation "manyTomany"
                    if( empty( $v->$v0 ) ){
                        continue;
                    }else{
                        //Eviter les doublons
                        $primary = $this->primary_key;
                        if( !in_array($v->$v0->$primary,$lists) ){
                            $manyToOne[$k0][] = $v->$v0;
                            $lists[] = $v->$v0->$primary;
                        }
                    }
                }                
            }
            //Associer les relations "manyToOne" s'il y en a
            foreach( $many as $k0 => $v0 ){
                $newData->$k0 = $manyToOne[$k0];
            }
            foreach( $many as $k0 => $v0 ){
                if( empty( $newData->$v0 ) || isset( $newData->$v0 ) ){
                    //Supprimer l'attribut "manyToOne" associé au premier élément de la liste
                    unset( $newData->$v0 );
                }
            }
            $results[] = $newData;
        } 
        return $results;
    }    
    
    /**
     * Get WHERE
     */
    public function getWhere($options=array()) {
        if (empty($options['conditions'])) {
            return '';
        }
        $conditions = $options['conditions'];
        if (is_array($conditions)) {
            $sql_clauses = $this->prepareWhere($conditions, $options);
            return 'WHERE '.implode(' AND ', $sql_clauses);
        }
        //e.g: Notaire.id => n.id
        if(preg_match_all('/([\w]+[.]{1}[\w]+)/',$conditions,$matches)){
            foreach($matches[1] as $v ){
                $tmp = explode('.',$v);
                $new = substr(strtolower($tmp[0]),0,1 ).'.'.$tmp[1];
                $conditions = preg_replace("/(\b{$v}\b)/",$new,$conditions);
            }                                
        }
        return 'WHERE '.$conditions;
    }
    
    /**
     * Prepare clause
     * 
     * @param string|array $conditions
     * @param array $options
     * @return string|array
     */
    protected function prepareWhere($conditions = null,$options){
        $use_table_alias = isset($options['use_table_alias']) ? $options['use_table_alias'] : true;
        $sql_clauses = array();
        if (!empty($conditions)) {
            if (is_array($conditions)) {
                foreach ($conditions as $key => $value) {
                    if (is_array($value)) {
                        if (is_string($key) && !in_array($key, array('OR', 'AND'))) {
                            $values = array();
                            foreach ($value as $val) {
                                $values[] = '"'.esc_sql($val).'"';
                            }
                            $values = implode(',', $values);
                            $sql_clauses[] = esc_sql($key).' IN ('.$values.')';
                        } else {
                            $clauses = $this->prepareWhere($value,$options);
                            $logical_operator = $key == 'OR' ? ' OR ' : ' AND ';
                            $sql_clauses[] = '('.implode($logical_operator, $clauses).')';
                        }
                        continue;
                    }
                    if (strpos($key, '.') === false && $use_table_alias) {
                        $key = substr(strtolower($this->model->name),0,1 ).'.'.$key;
                    }else{
                        //e.g: Notaire.id = n.id
                        if(preg_match_all('/([\w]+[.]{1}[\w]+)/',$key,$matches)){
                            foreach($matches[1] as $v ){
                                $tmp = explode('.',$v);
                                $new = substr(strtolower($tmp[0]),0,1 ).'.'.$tmp[1];
                                $key = preg_replace("/(\b{$v}\b)/",$new,$key);
                            }                                
                        }
                    }
                    if (!is_null($value)) {
                        $operator = preg_match('/\s+(<|>|<=|>=|<>|\!=|[\w\s]+)/', $key) ? ' ' : ' = ';
                        $sql_clauses[] = esc_sql($key).$operator.'"'.esc_sql($value).'"';
                    } else {
                        $sql_clauses[] = esc_sql($key).' IS NULL';
                    }
                }
            }
        } 
        return $sql_clauses;
    }
    
    /**
     * Append list of associated documents
     *
     * @param mixed $datas
     * @return array
     */
    protected function processAppendDocuments($datas)
    {
        if (is_array($datas) && count($datas) > 0) {
            reset($datas);
            $tmp    = current($datas);//initialisation
            $name   = \MvcInflector::tableize('document');
            $tItems = array();

            while (!empty($datas)) {
                $data = array_shift($datas);
//                var_dump($name);
                if ($tmp->id == $data->id) {// Si lobjet est toujours le même alors stocker la valeur
                    if (property_exists($data, $name)) { // list des doc deja existant
                        $tItems = $data->$name;
                    }

                    // document exist
                    if ($data->document->id) {
                        $tItems[] = $data->document;
                        unset($data->document);
                    }
                    $data->$name = $tItems;

                    $tmp = $data;
                } else {
                    $many[] = $tmp;
                    $tItems = array();
                    if ($data->document->id) {
                        $tItems = array($data->document);
                    }
                    $data->$name = $tItems;
                    $tmp    = $data;// l'itération courante
                }
                if (count($datas) === 0) { // Si nous arrivons déjà à la fin
                    $many[] = $tmp;
                }
            }
        }

        return (count ($many) > 0) ? $many : $datas;
    }
}
