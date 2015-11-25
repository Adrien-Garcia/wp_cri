<?php

/**
 *
 * This file is part of project 
 *
 * File name : admin_documents_controller.php
 * Project   : wp_cridon
 *
 * @author Etech
 * @contributor Fabrice MILA
 *
 */

class AdminDocumentsController extends MvcAdminController {
    
    var $default_columns = array('file_path','download_url','date_modified','type');
    
    public function index() {
        $this->init_default_columns();
        $this->process_params_for_search();
        $collection = $this->model->paginate($this->params);
        if( ( count( $collection ) > 0 ) && isset( $collection["objects"] ) ){
            foreach( $collection["objects"] as $k => $document ){
                $date = new DateTime( $document->date_modified );
                $document->date_modified = $date->format('d-m-Y H:i');
            }
        }
        $this->set('objects', $collection['objects']);        
        $this->set_pagination($collection);
    }
    public function add() {
        $this->create_or_save();
        $this->load_helper('Select');
        $this->load_helper('CustomForm');
        $this->set( 'options' , Config::$optionDocumentType );
    }
    public function edit() {
        $this->verify_id_param();
        $this->create_or_save();
        $this->set_object();
        $this->load_helper('Select');
        $this->load_helper('CustomForm');
        
        $this->set( 'options' , Config::$optionDocumentType );
    }
    
    //Ajax search
    public function search(){
        $search = $this->params['search'];
        $options = array(
            'conditions' => ' Document.name LIKE "%'.$search.'%"'
        );
        $data = $this->model->find( $options );
        $this->set('data', $data);
        $this->render_view('search', array('layout' => 'json'));
    }
}

?>