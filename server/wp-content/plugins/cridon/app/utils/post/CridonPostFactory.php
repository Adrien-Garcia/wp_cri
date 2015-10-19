<?php

/**
 *
 * This file is part of project 
 *
 * File name : CridonPostFactory.php
 * Project   : wp_cridon
 *
 * @author Etech
 * @contributor Fabrice MILA
 *
 */

class CridonPostFactory {
    private $oTools; // Object CridonTools
    private $postColumn; // Represent all column name of wp_posts table (array)
     
    public function __construct(){              
    }
    
    /**
     * Set object CridonTools and use his function to get all column of wp_posts table
     * 
     * @param objet $tools
     */
    public function setTools( $tools ){
        if( !$this->oTools ){
            $this->oTools = $tools; // Initialize object tools
            $this->postColumn = $tools->getPostColumn(); // Get column name of WP_Posts table from CridonTools            
        }
    }
    /**
     * Create clean object WP_Post with same attributes as in wp_posts table
     * 
     * @param object $object
     * @return \WP_Post
     */
    public function create( $object ){
        $obj = $this->cleanObject( $object );
        return new WP_Post( $obj );
    }
    
    /**
     * Erase all attributes which is not in WP_Post
     * 
     * @param object $object
     * @return object
     */
    private function cleanObject( $object ){
        foreach ( get_object_vars( $object ) as $key => $value ){
            if( !in_array( $key,$this->postColumn ) ){
                unset( $object->$key );
            }
        }
        return $object;
    }
}

