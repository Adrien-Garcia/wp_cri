<?php

/**
 *
 * This file is part of project 
 *
 * File name : QueryStringParser.php
 * Project   : wp_cridon
 *
 * @author Etech
 * @contributor Fabrice MILA
 *
 */
namespace App\Override\Model;
/**
 * SQL Query Uppercase
 */
class QueryStringParser {
    private $query;//sql query
    private $select = array();//contains selected fields,alias
    private $hasDistinct = false;//determine if query has DISTINCT
    private $models = array();//contains list of model in query
    private $selectedModels = array();//contains list of model in SELECT
    private $hasCustomField = false;//determine if query has specified field to fetch
    private $from;//model in FROM
    private $modelFrom;//object in FROM
    private $query_count;//sql count
    
    public function __construct($query) {
        if( empty( $query ) ){
            throw new \RuntimeException('Empty query.');
        }
        if( !is_string($query) ){
            throw new \RuntimeException('Query must be a string.');
        }
        $this->query = $query;
        $this->start();//begin parsing query
    }
    
    /**
     * Call methods to parse query
     */
    protected function start(){
        $this->getSelect();
        $this->getFrom();
        $this->alterToAppendTable();
        $this->alterSelectToAppendFields();
    }
    
    /**
     * Extract selected fields, alias
     * 
     * @throws \RuntimeException
     */
    protected function getSelect(){
        //pattern to use 
        $ptn = "/SELECT[\s]+(DISTINCT)*([a-zA-Z,-_0-9\s.]+)FROM/i";
        if( preg_match($ptn, $this->query, $matches) ){
            //if query contains DISTINCT
            if( $matches[1] === 'DISTINCT' ){
                $this->hasDistinct = true;
            }
            //clean and get an array of selected fields
            $fields = $this->trimArray(explode(',',$matches[2]));
            $this->select['f'] = $fields;//selected fields
            //get alias from selected fields
            $this->select['a'] = $this->getAliasFromSelect( $fields );
            //get original selected fields 
            $this->select['s'] = trim($matches[2]);
        }
        if( empty( $this->select ) ){
            throw new \RuntimeException('No fields found.');
        }
    }
    
    /**
     * Get model in FROM query
     * 
     * @throws \RuntimeException
     */
    protected function getFrom(){
        $error = false;
        $ptn = "/FROM[\s]*[\(\s]+\)?(.*?)[\s]+/i";
        if( preg_match($ptn, $this->query, $matches) ){
            $this->from = trim($matches[1]);
            $registry = \MvcModelRegistry::get_instance();
            if (!$registry->get_model($this->from)) {
                $ptn = "/[A-Za-z0-9_-]*\)[\s]*(.*?)[\s]+/i";
                if( preg_match_all($ptn, $this->query, $matches) ){
                    $this->from = trim($matches[1][0]);
                    if(count($matches[1]) > 2 ){
                        $error = true;
                        foreach($matches[1] as $v){
                            if( preg_match('/(\[[\w]+\])/i',$v, $m) ){
                                $this->from = $m[1];$error = false;break;
                            }
                        }
                    }
                } else {
                    $error = true;
                }
            }
        } else {
            $ptn = "/[A-Za-z0-9_-]*\)[\s]*(.*?)[\s]+/i";
            if( preg_match($ptn, $this->query, $matches) ){
                $this->from = trim($matches[1]);

                $registry = \MvcModelRegistry::get_instance();
                if (!$registry->get_model($this->from)) {
                    $error = true;
                }
            } else {
                $error = true;
            }
        }

        if ($error) {
            throw new \RuntimeException('No from found.');
        }
    }
    /**
     * Get alias in SELECT
     * 
     * @param array $fields
     * @return array
     */
    protected function getAliasFromSelect( $fields ){
        $alias = array();
        foreach( $fields as $field ){
            //if field is custom
            //e.g: m.id
            if(strpos($field,'.') !== false ){
                $a = explode('.',$field);
                $alias[$a[0]] = $a[0];//no duplicate
                $this->hasCustomField = true;
            }else{
                $alias[$field] = $field;
            }
        }
        return $alias;
    }
    
    /**
     * Change model name to table name in query
     * 
     * @throws \RuntimeException
     */
    protected function alterToAppendTable(){
        //Get instance of registry
        //When plugin start, WP_MVC load all model in the directory
        $registry = \MvcModelRegistry::get_instance();
        //Browse list of model
        $query = str_replace(array('[', ']'), array('', ''), $this->query);
        $this->from = str_replace(array('[', ']'), array('', ''), $this->from);
        foreach( $registry->__models as $model ){
            if( !empty($model->name) && !empty($model->table) ){
                //Pattern to use to find model used in query and his alias
                $ptn = "/(FROM|JOIN|LEFT|RIGHT|INNER|\(|\))[\s]+(\b{$model->name}\b)([A-Za-z0-9\s]+)/";
                if( preg_match_all($ptn, $query,$matches) ){
                    //Model must be contain alias
                    if( !isset($matches[3]) || empty($matches[3])){
                        throw new \RuntimeException('No alias found.');
                    }
                    $a_alias = array();
                    foreach ($matches[3] as $s_alias) {
                        $s_alias = trim($s_alias);
                        $a_items = explode(' ',$s_alias);
                        if(!in_array(sanitize_title($a_items[0]), $a_alias)){
                            $a_alias[] = sanitize_title($a_items[0]);                            
                        }
                    }
                    $this->query = preg_replace("/\b({$matches[2][0]})\b/", $model->table, $this->query);
                    $this->models[$model->name] = array(
                        'model' => $model,
                        'alias' => $a_alias
                    );
                    //If model is in the FROM
                    if( $model->name == $this->from ){
                        $this->modelFrom = $model;
                    }
                }
            }
        }
        $this->query = str_replace('[' . $this->modelFrom->table . ']', '', $this->query);

        if( empty( $this->modelFrom ) ){
            throw new \RuntimeException('No object found in FROM.');
        }

    }
    
    /**
     * Put in query the fields to retreive
     * 
     * @throws \RuntimeException
     */
    protected function alterSelectToAppendFields(){
        $new = '';//contains fields
        $alias = array();
        //Browse list of model in query
        foreach ( $this->models as $v ){
            
            //if alias of model is in select
            foreach ($v['alias'] as $s_alias) {
                if (!in_array($s_alias,$alias) && in_array($s_alias, $this->select['a'])) {
                    $alias[] = $s_alias;//store alias
                    $options = array(
                        'model' => $v['model'],
                        'alias' => sanitize_title($s_alias)
                    );
                    //store model and alias
                    $this->selectedModels[] = $options;
                    $iterator               = 0;//iterator for alias

                    //Check if primary key is in query select
                    $hasPrimaryKeyAlias   = false;
                    $rangePrimaryKeyAlias = -1;
                    //Schema not found
                    if( empty($v['model']->schema) ){
                        throw new \RuntimeException('Schema table corrupted for '.$v['model']->name);
                    }
                    //Browse list of fields in schema of table
                    foreach ($v['model']->schema as $field => $val) {
                        //for custom fields to retreive
                        if ($this->hasCustomField) {
                            //if it's the field to retreive
                            if (in_array($s_alias . '.' . $field, $this->select['f'])) {
                                //the field with his alias
                                $new .= ',' . $s_alias . '.' . $field . ' AS ' . $s_alias . $iterator;
                                if ($v['model']->primary_key == $field) {
                                    $hasPrimaryKeyAlias = true;
                                }
                            }
                        } else {
                            $new .= ',' . $s_alias . '.' . $field . ' AS ' . $s_alias . $iterator;
                            $hasPrimaryKeyAlias = true;
                        }
                        if ($v['model']->primary_key == $field) {
                            $rangePrimaryKeyAlias = $iterator;
                        }
                        $iterator++;
                    }
                    //Append in Select primary key if it's not found in orginal query
                    if (!$hasPrimaryKeyAlias) {
                        $new .= ',' . $s_alias . '.' . $v['model']->primary_key . ' AS ' . $s_alias . $rangePrimaryKeyAlias;
                    }
                }
            }
        }
        $new = trim($new,',');
        if( empty($new) ){
            throw new \RuntimeException('Can not append fields.');
        }
        //construct pattern
        $ptn = '/SELECT[\s]+';
        $replace = 'SELECT ';
        if( $this->hasDistinct ){
            $ptn .= 'DISTINCT[\s]+';
            $replace .= 'DISTINCT ';
        }
        $replace_count = $replace;
        $replace .= $new.' FROM';
        $replace_count .= ' COUNT(*) c FROM';
        $ptn .= "({$this->select['s']})[\s]+FROM/i";
        //query for count result
        $this->query_count = preg_replace($ptn, $replace_count, $this->query);
        //erase clause LIMIT
        $this->query_count = preg_replace('/(LIMIT[0-9,\s]+)*/', '', $this->query_count);
        //replace fields in SELECT with new
        $this->query = preg_replace($ptn, $replace, $this->query);
    }
    
    /**
     * Strip whitespace (or other characters) in array
     * 
     * @param array $array
     * @param string $mask optional
     * @return array
     */
    protected function trimArray($array,$mask = ''){
        $new = array();
        foreach ( $array as $v ){
            $new[] = ($mask) ? trim($v,$mask) : trim($v);
        }
        return $new;
    }
    
    /**
     * Get selected model (model and alias)
     * 
     * @return array
     */
    public function getSelectedModel(){
        return $this->selectedModels;
    }
    
    /**
     * Get clean query to use
     * 
     * @return string
     */
    public function getQuery(){
        return $this->query;
    }
    
    /**
     * Get model if FROM
     * 
     * @return \MvcModel
     */
    public function getQueryFrom(){
        return $this->modelFrom;
    }
    
    /**
     * Get query for count result
     * 
     * @return string
     */
    public function getQueryCount(){
        return $this->query_count;
    }
}
