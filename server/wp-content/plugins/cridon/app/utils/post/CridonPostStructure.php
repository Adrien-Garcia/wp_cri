<?php

/**
 *
 * This file is part of project 
 *
 * File name : CridonPostStructure.php
 * Project   : wp_cridon
 *
 * @author Etech
 * @contributor Fabrice MILA
 *
 */

/**
 * This class is used to retreive column name in table wp_posts.
 */
class CridonPostStructure {
    private $wp_posts_column;//Contains Post table column
    private $fieldPost; //used in query
    
    public function __construct() {
        $this->wp_posts_column = array();
        $this->fieldPost = null;
        $this->getColumnNameOfPost();
    }
    
    /**
     * Return field of post for the query
     * 
     * @return string 
     */
    public function getFieldPost(){
        if( !$this->fieldPost ){
            $this->getColumnNameOfPost();
        }
        return $this->fieldPost;
    }
    /**
     * Get post column name
     * 
     * @return array
     */
    public function getPostColumn(){
        if( !$this->wp_posts_column ){
            $this->getColumnNameOfPost();
        }
        return $this->wp_posts_column;
    }
    /**
     * Get all column name of table cri_posts
     * @global type $wpdb
     */
    private function getColumnNameOfPost(){
        global $wpdb;       
        $this->fieldPost = '';
        $table_name = $wpdb->prefix . 'posts';
        foreach ( $wpdb->get_col( "DESC " . $table_name, 0 ) as $column_name ) {// Get column of table wp_posts
            $this->wp_posts_column[] = $column_name;
            $this->fieldPost .= 'p.'.$column_name.','; 
        }        
    }
}

