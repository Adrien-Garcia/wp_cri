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

// base admin ctrl
require_once 'base_admin_controller.php';

class AdminDocumentsController extends BaseAdminController {
    var $default_searchable_fields = array(
        'name', 
        'type'
    );
    var $default_columns = array(
        'name' => array('label' => 'Nom', 'value_method' => 'pdf_name'),
        'file_path' => array('label' => 'Chemin du fichier'),
        'download_url' => array('label' => 'Lien de téléchargement','value_method' => 'download_link'),
        'date_modified' => array('label' => 'Date de modification')
        ,'type');
    
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
        //Load custom helper
        $this->load_helper('AdminDocument');
    }
    public function add() {
        $this->create_or_save();
        $this->load_helper('Select');
        $this->load_helper('CustomForm');
        $this->set( 'options' , $this->model->optionDocumentType );
    }
    public function edit() {
        $this->verify_id_param();
        $this->create_or_save();
        $this->set_object();
        $this->load_helper('Select');
        $this->load_helper('CustomForm');
        
        $this->set( 'options' , $this->model->optionDocumentType );
    }
    
    //Ajax search
    public function search(){
        $search = $this->params['search'];
        $type = $this->params['type'];
        $options = array(
            'conditions' => ' Document.name LIKE "%'.$search.'%" AND Document.type = "'.$type.'"'
        );        
        $data = $this->model->find( $options );
        $this->set('data', $data);
        $this->render_view('search', array('layout' => 'json'));
    }
    
    /**
     * Link of documents
     * 
     * @param object $object
     * @return string
     */
    public function download_link($object)
    {   
        return ltrim($this->model->generatePublicUrl($object->id),'/');
    }
    /**
     * Pdf name of the document
     *
     * @param object $object
     * @return string
     */
    public function pdf_name($objects){
        return substr(strrchr($objects->file_path,'/'), 1);
    }
}

?>