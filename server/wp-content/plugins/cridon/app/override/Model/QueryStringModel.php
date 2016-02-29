<?php

/**
 *
 * This file is part of project 
 *
 * File name : QueryStringModel.php
 * Project   : wp_cridon
 *
 * @author Etech
 * @contributor Fabrice MILA
 *
 */

namespace App\Override\Model;

use App\Override\Model\QueryStringParser;

/**
 * e.g for query:
 * 
 * 1- custom fields
 * 
 * SELECT DISTINCT v.id,v.id_matiere,p.ID,p.post_title,m.label,p.post_date,v.post_id
 * FROM Veille v
 * JOIN Post p ON p.ID = v.post_id
 * JOIN Matiere m   ON m.id = v.id_matiere
 * GROUP by p.ID
 * 
 * 2- all
 * 
 * SELECT v,p,m
 * FROM Veille v
 * JOIN Post p ON p.ID = v.post_id
 * JOIN Matiere m   ON m.id = v.id_matiere
 * 
 */
class QueryStringModel {
    
    private $query_parser;
    protected $wpdb;
    
    public function __construct($query) {
        global $wpdb;
        $this->wpdb = $wpdb;//WP_Query
        //Create parser of query
        $this->query_parser = new QueryStringParser($query);
    }
    
    /**
     * Get result of query
     * 
     * @return mixed
     */
    public function getResults(){
        $datas = $this->wpdb->get_results( $this->query_parser->getQuery() );
        $results = $this->processObjects($datas);        
        return $results;
    }
    
    /**
     * Construct model object from result of query
     * 
     * @param mixed $datas
     * @return mixed
     */
    protected function processObjects($datas){ 
        $new_datas = array();
        //browse result
        foreach( $datas as $data ){
            $new_datas[] = $this->newObject($data);
        }
        return $new_datas;
    }
    
    /**
     * Construct new list of object (MvcModelObject)
     * 
     * @param \stdClass $data
     * @return \stdClass
     */
    protected function newObject( $data ){
        $from = $this->query_parser->getQueryFrom();
        $pObj = new \MvcModelObject( $from );//Model in FROM
        $pObj->mvc_model = clone $from;//clone to get exactly same object
        //get selected model query
        $models = $this->query_parser->getSelectedModel();
        //Browse
        foreach( $models as $model ) {
            $iterator = 0;//iterator for alias

            $cObj            = new \MvcModelObject($model['model']);//construct model object
            $cObj->mvc_model = clone $model['model'];//clone to get exactly same object

            //retreive from $data field of the table (model)
            foreach ($model['model']->schema as $field => $val) {
                //only for current model
                if ($from->name === $model['model']->name) {
                    //Put in parent object (in FROM) his field
                    if (property_exists($pObj, $field)) {
                        $cObj->$field = isset($data->{$model['alias'] . $iterator}) ? $data->{$model['alias'] . $iterator} : null;
                        $cObj->mvc_model->$field = isset($data->{$model['alias'] . $iterator}) ? $data->{$model['alias'] . $iterator} : null;
                    } else {
                        $pObj->$field            = isset($data->{$model['alias'] . $iterator}) ? $data->{$model['alias'] . $iterator} : null;
                        $pObj->mvc_model->$field = isset($data->{$model['alias'] . $iterator}) ? $data->{$model['alias'] . $iterator} : null;
                    }

                    if( $field === $model['model']->primary_key){
                        $pObj->__id = $data->{$model['alias'].$iterator};
                    }
                    if( $field === $model['model']->display_field){
                        $pObj->__name = $data->{$model['alias'].$iterator};
                    }
                } else {
                    $cObj->mvc_model->$field = isset($data->{$model['alias'] . $iterator}) ? $data->{$model['alias'] . $iterator} : null;
                    $cObj->$field            = isset($data->{$model['alias'] . $iterator}) ? $data->{$model['alias'] . $iterator} : null;
                    $cObj->__model_alias     = $model['alias'];

                    if( $field === $model['model']->primary_key){
                        $cObj->__id = $data->{$model['alias'].$iterator};
                    }
                    if( $field === $model['model']->display_field){
                        $cObj->__name = $data->{$model['alias'].$iterator};
                    }
                }
                $iterator++;
            }
            if ($from->name !== $model['model']->name) { // normal join
                //name used for attribute
                $name = strtolower($model['model']->name);
                if (property_exists($pObj, $name)) {
                    $oItems = $pObj->$name;
                    if (is_array($oItems)) {
                        $oItems[$model['alias']] = $cObj;
                        $pObj->$name             = $oItems;
                    } else {
                        $pObj->$name = array(
                            $pObj->$name->__model_alias => $oItems,
                            $model['alias']             => $cObj
                        );
                    }
                } else {
                    $pObj->$name = $cObj;
                }
            } else { // self join
                $name = \MvcInflector::tableize($model['model']->name);
                 if (property_exists($cObj, '__id')) {
                    $pObj->$name = array($cObj);
                 }
            }
        }

        return $pObj;
    }

    public function processAppendChild($datas, $options)
    {
        // recuperation structure model pricipal
        $from = $this->query_parser->getQueryFrom();

        // init retour
        $many = array();
        // verification condition "has_many"
        $primary = $from->primary_key;
        if (is_array($options)) {
            reset($datas);
            $tmp = current($datas);//initialisation
            foreach ($options as $model => $associations) {
                if (is_array($associations) && isset($associations['foreign_key'])) {
                    $name = \MvcInflector::tableize($model);
                    $tItems = array();

                    while (!empty($datas)) {
                        $data = array_shift($datas);
                        if( $tmp->$primary == $data->$primary ){//Si lobjet est toujours le même alors stocker la valeur
                            $items = $data->$name;

                            if (is_array($items)
                                && isset($items[0])
                                && $child = $items[0]
                            ) {
                                if (isset($child->$associations['foreign_key']) && $child->$associations['foreign_key'] == $data->$primary) {
                                    $tItems[] = $child;

                                    $data->$name = $tItems;
                                }
                            }
                            $tmp = $data;
                        } else {
                            $many[] = $tmp;
                            $tItems = array();
                            $tmp = $data;// l'itération courante
                        }
                        if( count( $datas )  === 0  ){ // Si nous arrivons déjà à la fin
                            $many[] = $tmp;
                        }
                    }
                }
            }
        }

        return (count ($many) > 0) ? $many : $datas;
    }

    public function count(){
        return $this->wpdb->get_var( $this->query_parser->getQueryCount() );
    }

    /**
     * @return \App\Override\Model\QueryStringParser
     */
    public function getQueryParser()
    {
        return $this->query_parser;
    }

    /**
     * @param \App\Override\Model\QueryStringParser $query_parser
     */
    public function setQueryParser($query_parser)
    {
        $this->query_parser = $query_parser;
    }
}
