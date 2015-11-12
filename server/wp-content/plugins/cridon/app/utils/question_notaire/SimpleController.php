<?php

/**
 *
 * This file is part of project 
 *
 * File name : SimpleController.php
 * Project   : wp_cridon
 *
 * @author Etech
 * @contributor Fabrice MILA
 *
 */

class SimpleController {
    public $entityManager;
    public $pagination;
    public $params = null;
    public $user;
    
    public function __construct() {
        //Set entity manager
        $this->entityManager = new EntityManager();
        $this->setEntities();
        $this->params = self::escapeParams($_REQUEST);
        if ( CriIsNotaire() ) {//if logged
            $this->user = CriNotaireData();//get Notaire
        }
    }
    
    /**
     * Intialize entities
     */
    protected function setEntities(){
        foreach ( $this->entities as $entity ){
            $this->entityManager->addEntity( $entity );
        }
        //Add entities in registry
        $this->entityManager->create();
    }
    
    /**
     * Clean URL for $_REQUEST
     * @return string
     */
    protected function getUrl(){
        $url = $_SERVER['REQUEST_URI'];
        $regex = '/(\?[a-zA-Z=0-9]+|&[a-zA-Z=0-9]+)/';
        return preg_replace($regex, '', $url);
    }
    /**
     * Setup pagination
     * 
     * @param mixed $collection
     */
    public function setPagination($collection) {
        $params = $this->params;
        if( isset( $params['page'] ) ){
            unset($params['page']);            
        }
        if( isset( $params['conditions'] ) ){
            unset($params['conditions']);
        } 
        $url = home_url().$this->getUrl();
        $this->pagination = array(
            'base' => $url.'%_%',
            'format' => '?page=%#%',
            'total' => $collection['total_pages'],
            'current' => $collection['page'],
            'add_args' => $params
        );
        $this->cleanWpQuery();
    }
    
    /**
     * Clean global var wp_query
     * @global \WP_Query $wp_query
     */
    protected function cleanWpQuery() {
        global $wp_query;
        $wp_query->is_single = false;
        $wp_query->is_page = false;
        $wp_query->queried_object = null;
        $wp_query->is_home = false;
    }
    
    /**
     * Clean data in URL
     * @param array $params
     * @return array
     */
    public static function escapeParams($params) {
        if (is_array($params)) {
            foreach ($params as $key => $value) {
                if (is_string($value)) {
                    $params[$key] = stripslashes($value);
                } else if (is_array($value)) {
                    $params[$key] = self::escapeParams($value);
                }
            }
        }
        return $params;
    }
    /**
     * Get pagination in front
     * 
     * @return string
     */
    public function getPagination(){
        if( empty($this->pagination ) ){
            return '';
        }
        return paginate_links($this->pagination);
    }
}
