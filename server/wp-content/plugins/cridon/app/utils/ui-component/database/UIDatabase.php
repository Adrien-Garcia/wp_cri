<?php

/**
 *
 * This file is part of project 
 *
 * File name : UIDatabase.php
 * Project   : wp_cridon
 *
 *
 */

abstract class UIDatabase {
    protected $model;

    /**
     * Find a model
     * @param array $options
     * @return mixed
     */
    public function find( $options = array() ){
        return $this->model->find( $options );
    }
    
    /**
     * Save model
     * 
     * @param mixed $data
     */
    abstract public function save( $data );
    
    /**
     * Delete object
     * 
     * @param integer $id
     */
    public function delete( $id ){
        $this->model->delete( $id );
    }

    /**
     * Delete all data of model for current object
     * 
     * @param object $data
     * @return boolean
     */
    abstract public function deleteAll( $data );
}
