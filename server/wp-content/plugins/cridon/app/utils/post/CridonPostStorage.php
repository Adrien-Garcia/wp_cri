<?php

/**
 *
 * This file is part of project 
 *
 * File name : CridonPostStorage.php
 * Project   : wp_cridon
 *
 * @author Etech
 * @contributor Fabrice MILA
 *
 */

/**
 * Simple class used to store all results ( link of model ).
 * It's used to get the current post link in frontend
 */
class CridonPostStorage {
    
    private static $aResults;// Array contain all link
    
    /**
     * Store the link of model
     * 
     * @param array $results
     */
    public function set( $results ){
        self::$aResults = array();
        if( is_array( $results ) && !empty( $results ) ){//Is array?
            foreach ( $results as $value ){
                // check if object contain an instance of WP_Post and has a link
                if( is_object( $value ) && isset( $value->link ) && isset( $value->post ) && ( $value->post instanceof WP_Post ) ){
                    self::$aResults[$value->post->ID] = $value->link;
                }
            }
        }else{// It's an object
            if( is_object( $results ) && isset( $results->link ) && isset( $results->post ) && ( $results->post instanceof WP_Post ) ){
                self::$aResults[$results->post->ID] = $results->link;
            }
        }
    }
    
    /**
     * Output the current link
     * 
     * @param integer $post_ID Current post ID
     * @return string|null
     */
    public function get( $post_ID ){
        return isset( self::$aResults[$post_ID] ) ? self::$aResults[$post_ID] : null;
    }
}
