<?php

/**
 *
 * This file is part of project 
 *
 * File name : CridonUser.php
 * Project   : wp_cridon
 *
 * @author Etech
 * @contributor Fabrice MILA
 *
 */

class CridonUser {
    
    private $id;
    private $token;
    private $login;
    private $password;
    private $model;
    private $currentUser;
    
    public function __construct( $login = null,$password = null,$currentUser = null ) {
        $this->login = $login;
        $this->password = $password;
        $this->currentUser = $currentUser;
        if( $this->currentUser ){
            $this->id = $this->currentUser->id;
            $this->model = $this->currentUser->__model_name;
        }
    }
    
    /**
     * Set login
     * 
     * @param string $login
     */
    public function setLogin( $login ){
        $this->login = $login;
    }
    
    /**
     * Set password
     * 
     * @param string $password
     */
    public function setPassword( $password ){
        $this->password = $password;
    }
    
    /**
     * Set token
     * 
     * @param string $token
     */
    public function setToken( $token ){
        $this->token = $token;
    }
    
    /**
     * Set object Notaire
     * 
     * @param object $currentUser
     */
    public function setCurrentUser( $currentUser ){
        $this->currentUser = $currentUser;
        if( $this->currentUser ){
            $this->id = $this->currentUser->id;
            $this->model = $this->currentUser->__model_name;
        }
    }
    
    
    /**
     * Generate token
     */
    public function generateToken(){
        $this->token = $this->currentUser->id.'!'.self::encryption($this->login, $this->password).'~'.time();
    }
    
    /**
     * Return id of current user
     * 
     * @return integer
     */
    public function getId(){
        return $this->id;
    }
    
    public function getModel(){
        return mvc_model( strtolower( $this->model ) );
    }
    /**
     * Get token
     * 
     * @return string
     */
    public function getToken(){
        return $this->token;
    }
    
    /**
     * Get login
     * 
     * @return string
     */
    public function getLogin(){
        return $this->getLogin();
    }
    
    /**
     * Get Password
     * 
     * @return string
     */
    public function getPassword(){
        return $this->password;
    }
    
    
    /**
     * Return object Notaire
     * 
     * @return object
     */
    public function getCurrentUser(){
        return $this->currentUser;
    }
    
    /**
     * Construct encrypted value
     * 
     * @param string $login
     * @param string $password
     * @return string
     */
    public static function encryption( $login,$password ){
        $salt = wp_salt( 'secure_auth' );
        return sha1( $salt.$login.$password );
    }
    
    /**
     * Check if user Notaire exist with the id
     * 
     * @param integer $id
     * @return object|boolean
     */
    public static function checkUserById( $id ){
        return mvc_model('notaire')->find_one_by_id( $id );        
    }
    
    /**
     * Check if user Notaire exist with the login and password
     * 
     * @param string $login
     * @param string $password
     * @return object|boolean
     */
    public static function checkUserByLoginAndPwd( $login, $password ){
        // find the notaire by login and password (similarly action on ERP)
        return mvc_model('notaire')->findByLoginAndPassword( $login, $password );
    }
}
