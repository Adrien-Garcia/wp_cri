<?php

/**
 * Cette classe utilise les méthodes offertes par la WP_Query de WP pour les requêtes 
 */

class EntityDatabaseAdapter {
    private $wpdb;

    function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }
    
    /**
     * Escape string value for query
     * 
     * @param string $value
     * @return string
     */
    public function escape($value) {
        return esc_sql($value);
    }
      
    /**
     * Execute query in database
     * 
     * @param string $sql
     * @return mixed
     */
    public function query($sql) {
        return $this->wpdb->query($sql);
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
        return $this->wpdb->get_results($sql);
    }
    
    /**
     * Retrieve one variable from the database
     * 
     * @param string $sql
     * @return mixed
     */
    public function getVar($sql) {
        return $this->wpdb->get_var($sql);
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
        if( isset( $options['query'] ) ){
            //Nous avons une requête SQL dans l'option
            return array($options['query']);
        }
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
            $sql = ' ';
            $sql .= ( isset( $join['type'] ) ) ? $join['type'] : 'JOIN';
            $sql .= ' '; 
            if(!is_string($join['on'])){
                $entityOnKey = array_keys($join['on']);
                $onEntity = $entityOnKey[0];
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
            }
            if(is_string($join['on'])){
                $instance = EntityRegistry::get( $joinEntity.'Entity' ); 
                $mvc_model = $instance->getMvcModel();
                $sql .= $mvc_model->table.' AS '.$joinEntity.' ON '.$join['on'];                
            }else{
                $sql .= $onEntity.'.'.$join['on'][$onEntity].' = '.$joinEntity.'.'.$join['entity'][$joinEntity];                
            }
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
}

?>