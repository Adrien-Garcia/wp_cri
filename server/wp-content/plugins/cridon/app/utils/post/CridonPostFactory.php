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
/**
 * Simple class used to create a instance of WP_Post
 */
class CridonPostFactory {
    
    private $postStructure; // Create clean object WP_Post 
     
    public function __construct( $postStructure ){    
        $this->postStructure = $postStructure;// Instance of CridonPostStructure
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
            if( !in_array( $key,$this->postStructure->getPostColumn() ) ){//If attribute isn't in post column name ( table wp_posts )
                unset( $object->$key );
            }
        }
        return $object;
    }
}

