<?php

/**
 *
 * This file is part of project 
 *
 * File name : CridonPostUrl.php
 * Project   : wp_cridon
 *
 * @author Etech
 * @contributor Fabrice MILA
 *
 */

class CridonPostUrl {
    
    /**
     * Model Flash is an example of exception because the suffix is not an sample 's', it's 'es'.
     * 
     * @var array 
     */
    private static $nameCtrlException = array(
        'flash' => 'flashes'
    );
    
    /**
     * Get custom url from WP_MVC for Post
     * 
     * @param string $model
     * @param integer $id
     */
    public static function generatePostUrl( $model, $id ){
        $controller = $model.'s';
        if( isset( CridonPostUrl::$nameCtrlException[$model] ) ){
            $controller = CridonPostUrl::$nameCtrlException[$model];
        }
        $option = array(
            'controller' => $controller,
            'action'     => 'show',
            'id'         => $id
        );
        return MvcRouter::public_url( $option );
    }
}
