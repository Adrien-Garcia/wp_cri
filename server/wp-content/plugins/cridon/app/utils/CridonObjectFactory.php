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
    
    public static function create( $object,$model,$fields ){
        $std = new stdClass();
        foreach ( get_object_vars( $object ) as $k => $v ){
            if( in_array( $k, $fields ) ){
                $std->$k = $v;
                if( $k === 'id' ){
                    $std->link = CridonPostUrl::generatePostUrl( $model , $v );
                }
            }
        }
        return $std;
    }
}
