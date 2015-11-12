<?php

/**
 * Most method in this class is same in wp-mvc\core\models\mvc_database_adapter.php, it's a simple overide.
 * So to prevent hard dependency, It's necessary to have a copy of this class.
 */
require_once 'EntityDatabase.php';

class EntityDatabaseAdapter {

    public $db;//database access

    function __construct() {
        $this->db = new EntityDatabase();
    }
    
    /**
     * Escape string value for query
     * 
     * @param string $value
     * @return string
     */
    public function escape($value) {
        return $this->db->escape($value);
    }
      
    /**
     * Execute query in database
     * 
     * @param string $sql
     * @return mixed
     */
    public function query($sql) {
        return $this->db->query($sql);
    }
    
    /**
     * Get result from query ( string or array options)
     * @param array|string $options_or_sql
     * @return mixed
     */
    public function getResults($options_or_sql) {
        if (is_array($options_or_sql)) {
            //Construct SQL from options given
            $clauses = $this->getSqlSelectClauses($options_or_sql);
            $sql = implode(' ', $clauses);
        } else {
            $sql = $options_or_sql;
        }
        return $this->db->get_results($sql);
    }
    
    /**
     * Retrieve one variable from the database
     * 
     * @param string $sql
     * @return mixed
     */
    public function getVar($sql) {
        return $this->db->get_var($sql);
    }
    
    /**
     * Return table for sql statement FROM
     * 
     * @param array $options
     * @return string
     */
    public function getTableReferenceSql($options=array()) {
        if( !isset( $options['from'] ) || empty( $options['from'] ) ){
            return null;
        }
        
        $from = '';
        $instance = EntityRegistry::get( $options['from'].'Entity' ); 
        if( $instance ){
            $mvc_model = $instance->getMvcModel();
            if( !empty( $mvc_model ) ){
                $table = $mvc_model->table;
                $from .= $table.' AS '.$mvc_model->name.' ';  
            }
        }
        return $from;
    }
    
    /**
     * Generate array where contains sql statement
     * @param array $options
     * @return array
     */
    public function getSqlSelectClauses($options=array()) {
        $clauses = array(
            'select' => 'SELECT '.$this->getSelectSql($options),
            'from' => 'FROM '.$this->getTableReferenceSql($options),
            'joins' => $this->getJoinsSql($options),
            'where' => $this->getWhereSql($options),
            'group' => $this->getGroupSql($options),
            'order' => $this->getOrderSql($options),
            'limit' => $this->getLimitSql($options),
        );
        
        return $clauses;
    }
    
    /**
     * return sql statement SELECT
     * 
     * @param array $options
     * @return string
     */
    public function getSelectSql($options=array()) {
        if( !isset( $options['select'] ) || empty( $options['select'] ) ){
            return '';
        }
        $select = '';
        foreach ( $options['select'] as $entity ){
            $instance = EntityRegistry::get( $entity.'Entity' ); 
            if( $instance ){
                $fields = $instance->fields;
                $mvc_model = $instance->getMvcModel();
                if( !empty( $mvc_model ) ){
                    $new = array(); 
                    foreach( $fields as $key => $field ){
                        $new[] = $mvc_model->name.'.'.$field.' AS '.$mvc_model->name.$key;
                    }
                    $select .= implode(',',$new).',';                         
                }
            }
        }
        return trim($select,',');
    }
    
    /**
     * Return sql statement JOIN|LEFT JOIN|RIGHT JOIN
     * 
     * @param array $options
     * @return string
     */
    public function getJoinsSql($options=array()) {
        if( !isset( $options['joins'] ) || empty( $options['joins'] ) ){
            return '';
        }
        $joins = array();
        foreach ( $options['joins'] as $join ){
            if( !isset( $join['entity'] ) || empty( $join['entity'] ) || !isset( $join['on'] ) || empty( $join['on'] ) ){
                return '';
            }
            $entityJoinKey = array_keys($join['entity']);
            $joinEntity = $entityJoinKey[0];
            $entityOnKey = array_keys($join['on']);
            $onEntity = $entityOnKey[0];
            $sql = ' ';
            $sql .= ( isset( $join['type'] ) ) ? $join['type'] : 'JOIN';
            $sql .= ' '; 
            $instanceOn = EntityRegistry::get( $onEntity.'Entity' ); 
            if( !$instanceOn ){
                return '';
            }
            $mvc_model_on = $instanceOn->getMvcModel();
            if( empty( $mvc_model_on ) ){
                return '';
            }
            $tableOn = $mvc_model_on->table;
            $sql .= $tableOn.' AS '.$onEntity;
            $sql .= ' ON ';
            $sql .= $onEntity.'.'.$join['on'][$onEntity].' = '.$joinEntity.'.'.$join['entity'][$joinEntity];
            $joins[] = $sql;
        }
        return implode(' ', $joins);
    }
    
    /**
     * Return sql statement WHERE
     * 
     * @param array $options
     * @return string
     */
    public function getWhereSql($options=array()) {
        if (empty($options['conditions'])) {
            return '';
        }
        $conditions = $options['conditions'];
        return 'WHERE '.implode( ' AND ',$conditions);
    }
    
    /**
     * Return sql statement GROUP BY
     * 
     * @param array $options
     * @return string
     */
    public function getGroupSql($options=array()) {
        if (empty($options['group'])) {
            return '';
        }
        return 'GROUP BY '.$options['group'];
    }
    
   /**
    * Return sql statement ORDER BY
    * 
    * @param array $options
    * @return string
    */   
    public function getOrderSql($options=array()) {
        $order = empty($options['order']) ? $this->defaults['order'] : $options['order'];
        return $order ? 'ORDER BY '.$this->escape($order) : '';
    }
    
    /**
     * Return sql statement LIMIT
     * 
     * @param array $options
     * @return string
     */
    public function getLimitSql($options=array()) {
        if (!empty($options['page'])) {
            $per_page = empty($options['per_page']) ? 10 : $options['per_page'];
            $page = $options['page'];
            $offset = ($page - 1) * $per_page;
            return 'LIMIT '.$this->escape($offset).', '.$this->escape($per_page);
        }
        $limit = empty($options['limit']) ? '' : $options['limit'];
        return $limit ? 'LIMIT '.$this->escape($limit) : '';
    }
    
    /**
     * Return sql statement for UPDATE SET
     * 
     * @param array $data
     * @return string
     */
    public function getSetSql($data) {
        $clauses = array();
        foreach ($data as $key => $value) {
            if (is_string($value) || is_numeric($value)) {
                $clauses[] = $key.' = "'.$this->escape($value).'"';
            }
        }
        $sql = implode(', ', $clauses);
        return $sql;
    }
    
    /**
     * Return sql statement INSERT
     * @param array $data
     * @return string
     */
    public function getInsertColumnsSql($data) {
        $columns = array_keys($data);
        $columns = $this->db->escape_array($columns);
        $sql = '('.implode(', ', $columns).')';
        return $sql;
    }
    
    /**
     * Return sql statement INSERT VALUES
     * @param array $data
     * @return string
     */
    public function getInsertValuesSql($data) {
        $values = array();
        foreach ($data as $value) {
            $values[] = '"'.$this->escape($value).'"';
        }
        $sql = '('.implode(', ', $values).')';
        return $sql;
    }
    
    /**
     * Insert into database
     * 
     * @param array $data
     * @param array $options
     * @return mixed
     */
    public function insert($data, $options=array()) {
        $options['table_alias'] = false;
        $options['use_table_alias'] = false;
        if (empty($options['table_reference'])) {
            // Filter out any data with a key that doesn't correspond to a column name in the table
            $data = array_intersect_key($data, $this->schema);
        }
        $clauses = array(
            'insert' => 'INSERT INTO '.$this->getTableReferenceSql($options),
            'insert_columns' => $this->getInsertColumnsSql($data),
            'insert_values' => 'VALUES '.$this->getInsertValuesSql($data)
        );
        $sql = implode(' ', $clauses);
        $this->query($sql);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }
    
    /**
     * Update table
     * 
     * @param array $data
     * @param array $options
     */
    public function updateAll($data, $options=array()) {
        $clauses = array(
            'update' => 'UPDATE '.$this->getTableReferenceSql($options),
            'set' => 'SET '.$this->getSetSql($data),
            'where' => $this->getWhereSql($options),
            'limit' => $this->getLimitSql($options)
        );
        $sql = implode(' ', $clauses);
        $this->query($sql);
    }
    
    /**
     * Delete data in table
     * 
     * @param array $options
     */
    public function deleteAll($options) {
        $options['table_alias'] = false;
        $options['use_table_alias'] = false;
        $clauses = array(
            'update' => 'DELETE FROM '.$this->getTableReferenceSql($options),
            'where' => $this->getWhereSql($options),
            'limit' => $this->getLimitSql($options)
        );
        $sql = implode(' ', $clauses);
        $this->query($sql);
    }

}

?>