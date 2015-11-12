<?php

/**
 *
 * This file is part of project 
 *
 * File name : Entity.php
 * Project   : wp_cridon
 *
 * @author Etech
 * @contributor Fabrice MILA
 *
 */

class Entity implements EntityInterface {
    public $mvc_model = null;
    /**
     * Set model
     * 
     * @param string $model
     */
    public function setMvcModel( $model ){
        //get model from MvcModel
        $this->mvc_model = mvc_model( $model );
    }
    
    /**
     * Get model
     * 
     * @return object
     */
    public function getMvcModel(){
        return $this->mvc_model;
    }
}
