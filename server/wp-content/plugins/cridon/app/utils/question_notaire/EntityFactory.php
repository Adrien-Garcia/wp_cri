<?php

/**
 *
 * This file is part of project 
 *
 * File name : EntityFactory.php
 * Project   : wp_cridon
 *
 * @author Etech
 * @contributor Fabrice MILA
 *
 */

class EntityFactory {
    
    /**
     * Create new entity
     * 
     * @param string $entity
     * @return \entity
     */
    public static function get( $entity ){
        if( class_exists( $entity ) ){
            //Recréer l'objet
            return new $entity();
        }
        return null;
    }
}
