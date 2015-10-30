<?php

/**
 *
 * This file is part of project 
 *
 * File name : veilles_controller.php
 * Project   : wp_cridon
 *
 * @author Etech
 * @contributor Fabrice MILA
 *
 */

class VeillesController extends MvcPublicController {
    
    public function show() {
        if( !intval($this->params['id']) ){
            $this->generateError();
        }
        global $cri_container;
        $tools = $cri_container->get( 'tools' );
        //All fields of table cri_matiere
        $fields = array('id','code','label','short_label','displayed','picto');
        $mFields = '';// fields of model Matiere
        foreach ( $fields as $v ){
            $mFields .= ',m.'.$v;
        }
        $options = array(
            'fields' => $tools->getFieldPost().'v.id as join_id'.$mFields,
            'join'  => array(
                'veille' => array(
                    'table' => 'veille v',
                    'column' => 'v.post_id = p.ID'
                ),//use join clause with table cri_matiere
                'matiere' => array(
                    'table' => 'matiere m',
                    'column' => 'm.id = '.'v.id_matiere'
                )
            ),
            'conditions' =>  'p.post_status = "publish" AND v.id = '.$this->params['id'],
            'order' => 'ASC'
        );
        $result = criQueryPosts( $options );//Get associated post
        if( empty( $result ) ){//If no result
            $this->generateError();
        }
        // The result is an array of object ( stdClass )
        $aFinal = array();// Final result
        foreach( $result as $value ){
            $std = new stdClass();
            //Dissociate current objet to get an object Matiere ( only an object stdClass with all attributes as in table cri_matiere )
            $std->matiere = CridonObjectFactory::create( $value, 'matiere', $fields);
            $std->link = CridonPostUrl::generatePostUrl( 'veille', $value->join_id );
            //Dissociate current object to get an object Post ( WP_Post )
            $std->post = $tools->createPost( $value ); // Create Object WP_Post
            $aFinal[] = $std;
        }
        $this->set( 'object',$aFinal );
    }
    public function index() {
        //do nothing
        //display view
    }
    /**
     * Generate Error 404
     * 
     * @global WP_query $wp_query
     */
    private function generateError(){
        global $wp_query;
        header("HTTP/1.0 404 Not Found - Archive Empty");
        $wp_query->set_404();
        if( file_exists( TEMPLATEPATH.'/404.php' ) ){
            require TEMPLATEPATH.'/404.php';            
        }
        exit;
    }
}

?>