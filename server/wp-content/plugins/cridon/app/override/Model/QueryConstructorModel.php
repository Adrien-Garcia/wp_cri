<?php

/**
 *
 * This file is part of project 
 *
 * File name : QueryConstructorModel.php
 * Project   : wp_cridon
 *
 * @author Etech
 * @contributor Fabrice MILA
 *
 */

namespace App\Override\Model;

use App\Override\Model\QueryStringModel;

class QueryConstructorModel {
    
    private $model;//current model
    private $select;//SELECT statement
    private $from;//FROM statement
    private $join = '';//JOIN statement
    private $where = '';//WHERE statement
    private $group = '';//GROUP statement
    private $order = '';//ORDER statement
    private $limit = '';//LIMIT statement
    private $options;//Options for query
    private $alias;//Alias for Model in FROM statement
    private $query;//Query after process end
    private $queryStringModel;//Getting result after contruct query
    private $aModels = array();//All models used
    private $aAlias = array();//All alias used
    private $hasAndBelongsToMany = false;//for has_and_belongs_to_many relationship
    
    
    public function __construct($model,$options = array()) {
        if( !$model instanceof \MvcModel ){
            throw new \RuntimeException('Model must be an instance of MvcModel');
        }
        $this->options = $options;
        $this->model = $model;
        //alias by model used
        $this->alias = array(
            'alias'         => array(),
            'model_name'    => array()
        );
        $this->start();
    }
    
    /**
     * Begin precess
     */
    protected function start(){
        //step 1: Put current model in FROM with his alias
        $this->prepareFrom();
        //step 2: Get all joins and put theirs in query
        $this->prepareJoin();
        //step 3: Put in WHERE all conditions if exist
        if(isset($this->options['conditions']) ){
            $this->prepareWhere($this->options['conditions']);            
        }
        //step 4: Put GROUP
        $this->prepareGroup();
        //step 5: ORDER clause
        $this->prepareOrder();
        //step 6: Puts alias or fields in SELECT
        $this->prepareSelect();
        //step 7: LIMIT clause
        $this->prepareLimit();
        //step 8: Construct query
        $this->prepareQuery();
    }
    
    /**
     * Get FROM
     */
    protected function prepareFrom(){
        //Alias used for current model
        $this->alias = substr(strtolower($this->model->name),0,1 );
        //Put in array current model
        $this->aModels[] = $this->model;
        //Necessary for query
        $this->from = $this->model->name . ' ' . $this->alias;
        //Put alias in array
        $this->aAlias['alias'][] = $this->alias;
        $this->aAlias['model_name'][] = $this->model->name;
    }
    
    /**
     * Get JOIN
     * 
     * @return boolean 
     * @throws \RuntimeException
     */
    protected function prepareJoin(){
        $clauses = array();
        //Check if Join exist in options
        if( !empty($this->options) && isset($this->options['joins']) ){
            //browse
            foreach ($this->options['joins'] as $join) {
                if(is_string($join)){
                    if($this->hasAndBelongToMany()){
                        return true;
                    }
                    //Get model from registry
                    $joinModel = \MvcModelRegistry::get_model($join);
                    
                    if( !$joinModel || !isset($this->model->associations[$joinModel->name]) ){
                        throw new \RuntimeException('Model "'.$join.'" not found.');
                    }
                    $alias = substr(strtolower($joinModel->name),0,1 );
                    if( in_array($alias,$this->aAlias['alias']) ){
                        $alias = $this->incrementAlias(strtolower($joinModel->name));
                    }
                    if( isset($joinModel->belongs_to) && !empty($joinModel->belongs_to) && array_key_exists($this->model->name,$joinModel->belongs_to) ){
                        $primary = $joinModel->belongs_to[$this->model->name]['foreign_key'];
                        
                    }else{
                       
                        $primary = $joinModel->primary_key;
                    } 
                    //append in clauses
                    $clauses[] = ' LEFT JOIN '.$joinModel->name.' '.$alias. ' ON '.$this->alias.'.'.$this->model->associations[$joinModel->name]['foreign_key'].' = '.$alias.'.'.$primary;
                }else{
                    $type = empty($join['type']) ? 'JOIN' : $join['type'];
                    $joinModel = $this->getModelInRegistry($join['model']);
                    $alias = ($join['alias']) ? $join['alias'] : strtolower($joinModel->name);
                    if( in_array($alias,$this->aAlias['alias']) ){
                        $alias = $this->incrementAlias($alias);
                    }
                    $clauses[] = $type.' '.$joinModel->name.' '.$alias. ' ON '.$join['on'];                    
                }
                //registry all
                $this->aModels[] = $joinModel;
                $this->aAlias['alias'][] = $alias;
                $this->aAlias['model_name'][] = $joinModel->name;
            }
        }else if( empty($this->options) || !empty($this->model->associations) ){
            //Put associations in query
            foreach( $this->model->associations as $k => $v ){
                $joinModel = $this->getModelInRegistry($k);
                $alias = substr(strtolower($k),0,1 );
                if( in_array($alias,$this->aAlias['alias']) ){
                    $alias = $this->incrementAlias(strtolower($k));
                }
                $join  = 'LEFT JOIN ';//default               
                if( $v['type'] === 'has_many' ){
                    //for association "has_many", "belongs_to" must exist
                    if( empty($this->model->belongs_to) ){
                        continue;
                    }
                }
                //self join
                if( isset($this->model->belongs_to) && isset($this->model->belongs_to[$joinModel->name]) && isset($this->model->has_many) && isset($this->model->has_many[$joinModel->name]) ){
                    //e.g: cahiercridon.id_parent = cahiercridon.id
                    $foreign_key = $this->model->belongs_to[$joinModel->name]['foreign_key'];
                } else if( array_key_exists($v['foreign_key'], $joinModel->schema) ){
                    //e.g: question.client_number = notaire.client_number
                    $foreign_key = $v['foreign_key'];
                } else if( isset($joinModel->has_many) && isset($joinModel->has_many[$this->model->name]) && !isset($this->model->has_many[$this->model->name]) ){
                    //e.g: notaire.id_fonction = fonction.id
                    $foreign_key = $joinModel->has_many[$this->model->name]['foreign_key'];
                } else {
                    //default
                    $foreign_key = $joinModel->primary_key;
                }
               
                $join .= $k.' '.$alias;
                if( ($v['type'] === 'has_and_belongs_to_many') ){
                    /**
                     * @todo How append query and retreive data ?
                     */
                    continue;
                }else{
                    $join .= ' ON '.$this->alias. '.' .$v['foreign_key'];
                    $join .= ' = '.$alias.'.'.$foreign_key;                    
                }
                $this->aAlias['alias'][] = $alias;
                $this->aAlias['model_name'][] = $joinModel->name;
                $this->aModels[] = $joinModel;
                $clauses[] = $join;
            } 
        }      
        array_shift($this->aAlias['alias']);
        array_shift($this->aAlias['model_name']);
        $this->join = implode(' ', $clauses);
    }    
    
    /**
     * Get WHERE
     */
    protected function prepareWhere($conditions = null){
        $use_table_alias = isset($this->options['use_table_alias']) ? $this->options['use_table_alias'] : true;
        if (!empty($conditions)) {
            if (is_array($conditions)) {
                $sql_clauses = array();
                foreach ($conditions as $key => $value) {
                    if (strpos($key, '.') === false && $use_table_alias) {
                        $key = substr(strtolower($this->model->name),0,1 ).'.'.$key;
                    }else{
                        if(!$this->hasAndBelongsToMany){
                            //e.g: Notaire.id = n.id
                            if(preg_match_all('/([\w]+[.]{1}[\w]+)/',$key,$matches)){
                                foreach($matches[1] as $v ){
                                    $tmp = explode('.',$v);
                                    $new = substr(strtolower($tmp[0]),0,1 ).'.'.$tmp[1];
                                    $key = preg_replace("/(\b{$v}\b)/",$new,$key);
                                }
                            }
                        }
                    }
                    if (is_array($value)) {
                        if (is_string($key) && !in_array($key, array('OR', 'AND'))) {
                            $values = array();
                            foreach ($value as $val) {
                                $values[] = '"'.esc_sql($val).'"';
                            }
                            $values = implode(',', $values);
                            $sql_clauses[] = esc_sql($this->normalizeStringForAlias($key)).' IN ('.$values.')';
                        } else {
                            $clauses = $this->prepareWhere($value);
                            $logical_operator = $key == 'OR' ? ' OR ' : ' AND ';
                            $sql_clauses[] = '('.implode($logical_operator, $clauses).')';
                        }
                        continue;
                    }
                    if (!is_null($value)) {
                        $operator = preg_match('/\s+(<|>|<=|>=|<>|\!=|[\w\s]+)/', $key) ? ' ' : ' = ';
                        $sql_clauses[] = esc_sql($key).$operator.'"'.esc_sql($value).'"';
                    } else {
                        $sql_clauses[] = esc_sql($key).' IS NULL';
                    }
                }
                $this->where = 'WHERE '.implode(' AND ', $sql_clauses);
            }else{
                $this->where = 'WHERE '.$conditions;                
            }
            return $sql_clauses;
        }
    }
    
    /**
     * Get GROUP
     */
    protected function prepareGroup(){
        if (!empty($this->options['group'])) {
            //e.g: Notaire.id = n.ids
            $group = $this->normalizeStringForAlias($this->options['group']);
            $this->group =  'GROUP BY '.$group;
        }        
    }
    
    /**
     * Get ORDER
     */
    protected function prepareOrder(){
        $order = empty($this->options['order']) ? $this->alias.'.'.$this->model->primary_key.' ASC ' : $this->options['order'];
        $order = esc_sql($order);
        //Query used alias
        if(strpos($order,'.') === false ){
            $order = $this->alias.'.'.$order;
        }else{
            //e.g: Notaire.id = n.id
            $order = $this->normalizeStringForAlias($order);
        }
        $this->order = 'ORDER BY '.  $order;
    }
    
    /**
     * Get SELECT
     */
    protected function prepareSelect(){
        $associations = $this->model->associations;        
        $alias = array();        
        if( !empty($this->options) && isset($this->options['selects']) ){
            //e.g: Notaire.id = n.id
            $alias = $this->normalize($this->options['selects']);
        }else{
            $alias[] = $this->alias;
            foreach( $associations as $k => $v ){
                $assocAlias = strtolower($k);
                if( in_array($k,$this->aAlias['model_name']) ){
                    foreach($this->aAlias['model_name'] as $k1 => $v1 ){
                        if( $v1 === $k ){
                            $alias[] = $this->aAlias['alias'][$k1];
                            break;
                        }
                    }
                }else{
                    $alias[] = $assocAlias;
                }
            }
        }
        $this->select = implode(',',$alias);
    }
    
    
    /**
     * Get LIMIT
     * 
     * @return boolean
     */
    protected function prepareLimit(){
        if (!empty($this->options['page'])) {
            $per_page = empty($this->options['per_page']) ? $this->model->per_page : $this->options['per_page'];
            $page = $this->options['page'];
            $offset = ($page - 1) * $per_page;
            $this->limit = 'LIMIT '.esc_sql($offset).', '.esc_sql($per_page);
            return true;
        }
        if( !empty($this->options['limit'] ) ){
            $this->limit = 'LIMIT '.esc_sql($this->options['limit']);
        }
    }
    
    /**
     * Construct query
     */
    protected function prepareQuery(){
        $this->query  = 'SELECT '. $this->select . ' ';
        $this->query .= 'FROM '. $this->from . ' ';
        $this->query .= $this->join. ' ';
        $this->query .= $this->where. ' ';
        $this->query .= $this->group. ' ';
        $this->query .= $this->order. ' ';
        $this->query .= $this->limit. ' ';
        //convert query to sql query
        $this->queryStringModel = new QueryStringModel($this->query);
    }
    
    /**
     * Get results from database
     * 
     * @return mixed
     */
    public function getResults(){
        return $this->queryStringModel->getResults();
    }
    
    /**
     * Count for current query
     * 
     * @return int|null
     */
    public function count(){
        return $this->queryStringModel->count();
    }
    
    //utilities
    
    /**
     * Increment for do not have same alias in query
     * 
     * @param string $alias
     * @return string
     */
    protected function incrementAlias($alias){
        if( preg_match('/([0-9])+/',$alias,$matches) ){
            $interator = (int) $matches[1] + 1;
            return substr(strtolower($alias), 0,$interator + 1) . $interator;
        }
        return substr(strtolower($alias),0,2 ) . 1;
    }
    
    /**
     * Normalize alias in array (key or value)
     * 
     * @param array $datas
     * @return array
     */
    protected function normalize($datas){
        $newData = array();
        foreach($datas as $k=>$v ){
            $key = $k;
            if( is_string($k) && (strpos($k,'.') !== false) ){
                $key = $this->normalizeStringForAlias($k);
            }
            $data = $v;
            if( is_string($v) && (strpos($v,'.') !== false) ){
                $data = $this->normalizeStringForAlias($v);
            }else{
                $data = $this->alias.'.'.$v;
            }
            $newData[$key] = $data;
        }
        return $newData;
    }   
    
    /**
     * Get normalized string ,e.g: Notaire.id = n.id
     * 
     * @param string $str
     * @return string
     */
    protected function normalizeStringForAlias($str){
        if( strpos($str,'.') !== false ){            
            $tmp = explode('.',$str);
            if( $tmp[1] === '*' ){
                $model = $this->getModelInRegistry($tmp[0]);
                return $model->convertSchemaToString($model,substr(strtolower($model->name),0,1 ));
            } else if( strlen($tmp[0]) > 2 ){//For model name
                return substr(strtolower($tmp[0]),0,1 ).'.'.$tmp[1];
            }
        }
        return $str;
    }
    
    /**
     * Get model in registry
     * 
     * @param string $model_name
     * @return \MvcModel
     * @throws \RuntimeException
     */
    protected function getModelInRegistry($model_name){
        $registry = \MvcModelRegistry::get_instance();
        $model = $registry->get_model($model_name);
        if (!$model) {
            throw new \RuntimeException('Model "'.$model_name.'" not found in registry.');
        }
        return $model;
    }
    
    /**
     * Check relation and construct join
     * 
     * @return boolean
     */
    protected function hasAndBelongToMany(){
        foreach( $this->options['joins'] as $key => $join ){
            if( is_string($key) && ($key === 'table') ){
                $this->hasAndBelongsToMany = true;break;
            }
        }
        //Only for this relation
        if($this->hasAndBelongsToMany){
            $this->join = ' JOIN ';
            $this->join .= $this->options['joins']['table'] . ' ' . $this->options['joins']['alias'];
            $on = explode('=',$this->options['joins']['on']);
            $this->join .= ' ON '.$on[0] . ' = '.$this->normalizeStringForAlias(trim($on[1]));
        }
        return $this->hasAndBelongsToMany;
    }
}
