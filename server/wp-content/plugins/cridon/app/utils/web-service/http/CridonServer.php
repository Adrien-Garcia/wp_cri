<?php

/**
 *
 * This file is part of project 
 *
 * File name : CridonServer.php
 * Project   : wp_cridon
 *
 * @author Etech
 * @contributor Fabrice MILA
 *
 */

class CridonServer {
    public $method;
    public $data;
    public $error;
    
    public function __construct() {
        $this->data = $_SERVER;
        $this->init( $this->data );        
    }
    
    /**
     * Check method of query
     * 
     * @param array $server
     */
    private function init( $server ){
        $this->method = $server['REQUEST_METHOD'];
        if ( $this->method == 'POST' && array_key_exists( 'HTTP_X_HTTP_METHOD', $server ) ) {
            if ($server['HTTP_X_HTTP_METHOD'] == 'DELETE') {
                $this->method = 'DELETE';
            } else if ($server['HTTP_X_HTTP_METHOD'] == 'PUT') {
                $this->method = 'PUT';
            } else {
                $this->error = array( 'message' => 'Unexpected Header' );
            }
        }
    }
    
    /**
     * Get data in $_SERVER
     * 
     * @param string $key
     * @return mixed
     */
    public function get( $key ){
        return isset( $this->data[$key] ) ? $this->data[$key] : null;
    }
    
    /**
     * Get Ip adress
     * 
     * @return string
     */
    public function getIpAdress() {
	//Just get the headers if we can or else use the SERVER global
	if ( function_exists( 'apache_request_headers' ) ) {
	     $headers = apache_request_headers();
	} else {
	     $headers = $_SERVER;
	}
	//Get the forwarded IP if it exists
	if ( array_key_exists( 'X-Forwarded-For', $headers ) && filter_var( $headers['X-Forwarded-For'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {
	    $ip = $headers['X-Forwarded-For'];
	} elseif ( array_key_exists( 'HTTP_X_FORWARDED_FOR', $headers ) && filter_var( $headers['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 )
	) {
	    $ip = $headers['HTTP_X_FORWARDED_FOR'];
	} else {
		
	    $ip = filter_var( $_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 );
	}
	return $ip;
    }
}
