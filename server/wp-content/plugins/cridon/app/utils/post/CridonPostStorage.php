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
    public static function set( $results ){
        self::$aResults = array();
        if( is_array( $results ) && !empty( $results ) ){//Is array?
            foreach ( $results as $value ){
                // check if object contain an instance of WP_Post and has a link
                if( is_object( $value ) && isset( $value->link ) && isset( $value->post ) && ( $value->post instanceof WP_Post ) ){
                    $tmp = array(
                        'link' => $value->link,// current link of object
                        'all'  => $value // all data of object
                    );
                    self::$aResults[$value->post->ID] = $tmp;
                }
            }
        }else{// It's an object
            if( is_object( $results ) && isset( $results->link ) && isset( $results->post ) && ( $results->post instanceof WP_Post || $results->post instanceof MvcModelObject ) ){
                $tmp = array(
                    'link' => $results->link,
                    'all'  => $results
                );
                self::$aResults[$results->post->ID] = $tmp;
            }
        }
    }
    
    /**
     * Output the current link or current result
     * 
     * @param integer $post_ID Current post ID
     * @param string $index 'link' or 'all'
     * @return string|array|null
     */
    public static function get( $post_ID,$index = 'link' ){
        //What data do you want? link or all?
        return ( isset( self::$aResults[$post_ID] ) && isset( self::$aResults[$post_ID][$index] ) ) ? self::$aResults[$post_ID][$index] : null;
    }
}

