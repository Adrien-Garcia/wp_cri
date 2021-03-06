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
/**
 * This class is used to generate URL of model
 */
class CridonPostUrl {
    
    /**
     * Model Flash is an example of exception because the suffix is not a simple 's', it's 'es'.
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
     * @param string $controller Controller name (optional)
     * @return string return url
     */
    public static function generatePostUrl( $model, $id,$controller = null ){
        if( !empty( $controller ) ){
            $ctrl = $controller;
        }else{
            $ctrl = MvcInflector::pluralize($model);//get controller name            
        }
        //All options for WP_MVC plugins to generate the URL of model
        $option = array(
            'controller' => $ctrl,
            'action'     => 'show',
            'id'         => $id
        );
        return MvcRouter::public_url( $option );
    }
}
