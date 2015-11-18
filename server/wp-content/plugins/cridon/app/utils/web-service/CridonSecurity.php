<?php

/**
 *
 * This file is part of project 
 *
 * File name : CridonSecurity.php
 * Project   : wp_cridon
 *
 * @author Etech
 * @contributor Fabrice MILA
 *
 */

class CridonSecurity {
    private $request;
    private $user;
    
    public function __construct( $request ) {
        $this->request = $request;
    }
    
    public function checkToken(){
        $token = null;
        if( isset( $this->request->query['token'] ) ){
            $token = $this->request->query['token'];
        }else{
            if( isset( $this->request->request['token'] ) ){
                $token = $this->request->request['token'];
            }
        }
        if( $token === null  ){            
            $this->unauthorized( array('message' => 'No token given') );
        }
        $lastConnect = $this->checkLastConnect( $token );
        if( $lastConnect ){
            return $lastConnect;
        }
    }
    
    /**
     * Return current user
     *   
     * @return object|null
     */
    public function getUser(){
        return $this->user;
    }
    
    /**
     * Attempt to authenticate
     * 
     * @param string $login
     * @param string $password
     * @return boolean|object
     */
    public function login( $login,$password ){
        //Check if Notaire exist with this login and password 
        $notaire = CridonUser::checkUserByLoginAndPwd($login, $password);
        if( empty( $notaire ) ){ 
            return false;
        }
        //Create user
        $this->user = new CridonUser( $login,$password,$notaire );
        $this->user->generateToken();
        return $this->user;
    }
    
    /**
     * Verify if token given is valid
     * 
     * @return object
     */
    protected function checkLastConnect( $token ){  
	$notaire = $this->verify($token);
        //No model find
        if( !$notaire ){
            $this->unauthorized();
        }
        $this->user = new CridonUser( $notaire->crpcen,$notaire->web_password,$notaire );
        $this->user->setToken( $token );
        return $this->user;
    }
    
    /**
     * Compare two values
     * 
     * @param mixed $v1
     * @param mixed $v2
     * @return boolean
     */
    private function compare( $v1,$v2 ){
        return ( $v1 == $v2);
    }
    
    /**
     * Compare two dates
     * 
     * @param integer $timestamp
     * @return boolean
     */
    private function compareDate( $timestamp ){
        $date = new DateTime();
        $date->setTimestamp( $timestamp );//given
        $now = new DateTime();//today
        $interval = $date->diff($now ,true )->days;
        return ( $interval < Config::$tokenDuration );
    }
    
    /**
     * Check if current token given is valid
     * Three steps to valid this
     * 
     * @param string $authToken
     * @return object|boolean
     */
    protected function verify( $authToken )
    {
        //Structure of token: [id]![encrypted value]~[timestamp]
        //encrypted value = salt + login + password encrypted in sha1 algorithm
        //Pattern regex to use 
        $pattern = "/([0-9]+)!([a-zA-Z0-9]+)~([0-9]+)/";
        if( preg_match_all( $pattern, $authToken, $matches ) ){
            //Check if id, encrypted value and timestamp exists in token given
            if( $this->checkMatched( $matches ) ){
                //Check if Notaire exist with this Id
                $notaire = CridonUser::checkUserById( $matches[1][0] );
                if( $notaire ){
                    //Get encrypted value with the Notaire
                    $encryption = CridonUser::encryption( $notaire->crpcen, $notaire->web_password );
                    //Check encrypted value given 
                    if( $this->compare($encryption, $matches[2][0] ) ){
                        //Check timestamp if duration exceeded
                        if( $this->compareDate($matches[3][0]) ){
                            return $notaire;
                        }                      
                    }
                }           
            }
        }
        return false;
    }
    
    /**
     * Determine values matched in preg_match
     * 
     * @param array $matches
     * @return boolean
     */
    protected function checkMatched( $matches ){
        if( count( $matches ) != 4 ){
            return false;
        }
        if( !isset( $matches[1][0] ) || empty( $matches[1][0] ) ){
            return false;
        }
        if( !isset( $matches[2][0] ) || empty( $matches[2][0] ) ){
            return false;
        }
        if( !isset( $matches[3][0] ) || empty( $matches[3][0] ) ){
            return false;
        }
        return true;
    }
    /**
     * Output message unauthorized access
     * 
     * @param mixed $data
     */
    public function unauthorized( $data = array() ){
        $response = new CridonResponse();
        $default = array(
           'success'  => false, 
           'message' => 'Access unauthorized'  
        );
        $data = array_merge( $default, $data );
        $response->output( $data, 401 );
    }    
    
}
