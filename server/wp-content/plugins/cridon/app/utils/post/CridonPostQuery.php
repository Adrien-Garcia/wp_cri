<?php

/**
 *
 * This file is part of project 
 *
 * File name : CridonPostQuery.php
 * Project   : wp_cridon
 *
 * @author Etech
 * @contributor Fabrice MILA
 *
 */

/**
 * This class is used to setup global variable post and wp_query.
 * WP use theirs when it's retreive post.
 */
class CridonPostQuery {
    
    private $wp_query;//It's used to set global variable WP_Query for WP
    
    public function __construct(){
        $this->createObjectWpQuery();//Initialize object WP_Query
    }
    /**
     * Create sample object WP_Query with no parameter
     */
    private function createObjectWpQuery(){
        //See in wp-includes\query.php line 3881
        $this->wp_query = new WP_Query();// Construct object WP_Query with no parameter, so no query will run
    }
    /**
     * Check if is an post instance of WP_Post
     * 
     * @param array|object $data WP_Post
     * @return string|boolean
     */
    private function isPostData( $data ){
        if( is_array( $data ) && !empty( $data ) ){//Is it an array?
            if( isset( $data[0]->post ) && ( $data[0]->post instanceof WP_Post ) ){//Is it an instance of WP_Post?
                return 'array';                
            }
        }else{//It's an object
            if( isset( $data->post ) && ( $data->post instanceof WP_Post ) ){
                return 'object';
            }
        }
        return false;
    }
    /**
     * Initialize WP_Query for post
     * 
     * @global object $post
     * @global object $cri_container
     * @param array|object $data
     * @return bool
     */
    public function init( $data ){
        $type = $this->isPostData( $data );//Check type of data
        if( $type ){
            CridonPostStorage::set( $data );//Store result. It's used to get link of model in Frontend ( same as the_permalink in WP )
            if( $type === 'object' ){ //If is it an object so set global variable $post which is necessary for more function in WP
                global $post,$pages,$page;
                $page = 1;// Current content to display
                $pages = array( $data->post->post_content ); // Initialize with post_content, the_content() require this to display content.
                $post = $data->post;
                return true;
            }
            //If the instance is not already create
            //Normally, if you use container the object is already create and you have an singleton pattern for this object
            if( $this->wp_query ){
                $this->createObjectWpQuery();
            }
            $this->wp_query->init(); // Initiates object properties and sets default values.
            global $cri_container;
            $oParser = $cri_container->get( 'post_parser' );
            //Construct an array of WP_Post
            $this->initializePostData( $oParser->generatePostArray( $data ) );
            $this->initializeWpQuery();
        }else{// Do not show default post if it's not an instance of WP_Post
            global $post;
            $post = null;
        }
    }
    /**
     * Setup WP_Query with all variable necessary for loop in WP
     * 
     * @param array $data
     */
    private function initializePostData( $data ){
        $this->wp_query->post_count = count( $data );
        //  Applies the callback to the elements of the given arrays
        $this->wp_query->posts = array_map( 'get_post', $data );
        //Return the first element in array
        $this->wp_query->post = reset( $this->wp_query->posts );
        $this->wp_query->current_post = -1;//It's use in WP increment loop
    }
    /**
     * Set global variable wp_query with current object WP_Query which is is necessary in loop
     *  
     * @global WP_Query $wp_query
     */
    private function initializeWpQuery(){
        global $wp_query;
        //See in wp-includes\query.php line 788, it's an example
        $wp_query = $this->wp_query;
    }
}