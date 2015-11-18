<?php

/**
 *
 * This file is part of project 
 *
 * File name : EntityRegistry.php
 * Project   : wp_cridon
 *
 * @author Etech
 * @contributor Fabrice MILA
 *
 */

class EntityRegistry {
    
    private static $entities = array();
    
    /**
     * Store entity
     * 
     * @param \Entity $entity
     * @param string $key
     */
    public static function addEntity( $entity, $key ){
        self::$entities[$key] = $entity;
    }
    
    /**
     * Get all entities
     * 
     * @return mixed
     */
    public static  function getEntities(){
        return self::$entities;
    }
    
    /**
     * Get one entity
     * 
     * @param string $key
     * @return \Entity
     */
    public static function get( $key ){
        return ( isset( self::$entities[$key] ) ) ? self::$entities[$key] : null;
    }
}
