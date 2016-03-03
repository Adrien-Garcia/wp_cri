<?php

/**
 *
 * This file is part of project 
 *
 * File name : CridonRequest.php
 * Project   : wp_cridon
 *
 * @author Etech
 * @contributor Fabrice MILA
 *
 */

class CridonRequest {
    public $request;
    public $requestUri;
    public $baseUri;
    public $query;
    public $server;
    public $response;
    
    public function __construct( $server,$response ) {
        $this->server = $server;
        if( !empty( $this->server->error ) ){
            $this->outputError( $this->server->error,400 );
        }
        $this->response = $response;
        $this->init();
        $this->requestUri = $this->server->get('REQUEST_URI');
        $this->baseUri = $this->server->get('HTTP_HOST');
    }
    
    /**
     * Check method
     * 
     * @param string $method
     * @return boolean
     */
    public function isMethod( $method ){
        return ( $this->getMethod() == $method );
    }
    /**
     * Get method for request
     * 
     * @return string
     */
    public function getMethod(){
        return $this->server->method;
    }
    
    /**
     * Check method and clean data
     */
    private function init(){
        if( !empty( $_GET ) ){
            $this->query = $this->clean( $_GET );            
        }
        switch( $this->server->method ) {
            case 'DELETE':
                // no action
            case 'POST':
                $this->request = $this->clean( $_POST );
                break;
            case 'GET':
                //do nothing here
                break;
            case 'PUT':
                $this->request = $this->clean( $_POST );
                $this->file = file_get_contents("php://input");
                break;
            default:
                $this->outputError();
                break;
        }
    }
    
    /**
     * Get data for method GET,POST or PUT
     * @param string $method
     * @param string $key
     * @return mixed
     */
    public function get( $method,$key = null ){
        switch ( $method ){
            case 'POST':
                return isset( $this->request[$key] ) ? $this->request[$key] : null ;
            case 'GET':
                return isset( $this->query[$key] ) ? $this->query[$key] : null ;
            case 'PUT':
                return $this->file;
            default:
                $this->outputError();
        }
        return null;
    }

    /**
     * Get data from JSON params
     * @param string $key
     * @return mixed
     */
    public function getParam( $key ) {
        $params = json_decode(file_get_contents('php://input'));

        return ( $params->$key ) ? $params->$key : null ;
    }
    
    /**
     * Clean all data
     * 
     * @param mixed $data
     * @return mixed
     */
    protected function clean($data) {
        $clean_input = Array();
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $clean_input[$k] = $this->clean($v);
            }
        } else {
            $clean_input = trim(strip_tags($data));
        }
        return $clean_input;
    }
    
    /**
     * Output error message for unrecognized method
     */
    protected function outputError( $data = array() ){
        $default = array(
           'success'  => false, 
           'message' => 'Invalid Method'  
        );
        $data = array_merge( $default, $data );
        $this->response->output( $data, 405 );
    }
}
