<?php

/**
 *
 * This file is part of project 
 *
 * File name : CridonObjectFactory.php
 * Project   : wp_cridon
 *
 * @author Etech
 * @contributor Fabrice MILA
 *
 */
/**
 * Simple class used to create new object stdClass
 */
class CridonObjectFactory {
    /**
     * Create new object stdClass 
     * 
     * @param object $object Current object
     * @param string $model Model name
     * @param array $fields Contain all attributes of object
     * @return \stdClass
     */
    public static function create( $object,$model,$fields ){
        $std = new stdClass();//new object
        foreach ( get_object_vars( $object ) as $k => $v ){
            //If current attribute belong to object to create 
            if( in_array( $k, $fields ) ){
                $std->$k = $v;
                //If object contain an attribute ID so generate URL with WP_MVC Plugins
                if( $k === 'id' ){
                    $std->link = CridonPostUrl::generatePostUrl( $model , $v );
                }
            }
        }
        return $std;
    }
}
