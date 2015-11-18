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
/**
 * Cette classe sera hérité par tous les entités.
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
        //Associé l'entité au modèle WP_MVC pour avoir dynamiquement la table associèe
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
