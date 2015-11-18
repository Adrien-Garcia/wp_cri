<?php

/**
 *
 * This file is part of project 
 *
 * File name : CridonDispatcher.php
 * Project   : wp_cridon
 *
 * @author Etech
 * @contributor Fabrice MILA
 *
 */

class RestServer {
    
    public  $container;
    private $request;
    private $security;
    private $user;
    
    public function __construct() {
        global $cri_container;
        $this->container = $cri_container;
        $this->request = $this->container->get('request');
        $this->security = $this->container->get('security');
    }
    
    /**
     * Check login and password
     */
    public function checkLogin(){       
        if( !$this->request->isMethod( 'POST' ) ){//Unauthorized            
            $this->security->unauthorized( array( 'message' => 'Only request method POST accepted'));
        }
        $method = $this->request->getMethod();
        $user = $this->security->login( $this->request->get( $method, 'login' ),$this->request->get( $method, 'password' ) );
        //No user found
        if( !$user ){
            $this->security->unauthorized( array('success'=>'false','message' => 'Login or password error') );
        }
        //output token
        $this->request->response->output( array( 'success'=>'true','token'=>$user->getToken() ) );
    }
    
    /**
     * Start Rest Server and verify token given
     */
    public function start(){
        $this->user = $this->security->checkToken();
    }
    
    /**
     * Get user with the token associated
     * 
     * @return object
     */
    public function getUser(){
        return $this->user;
    }
    
    /**
     * Return object Request
     * 
     * @return object
     */
    public function getRequest(){
        return $this->request;
    }
    
    /**
     * Return security context
     * 
     * @return object
     */
    public function getSecurity(){
        return $this->security;
    }
    /**
     * Output result
     * 
     * @param array $data
     * @param integer $status
     */
    public function out( $data, $status = 200 ){
        $this->request->response->output( $data,$status );
    }
}
