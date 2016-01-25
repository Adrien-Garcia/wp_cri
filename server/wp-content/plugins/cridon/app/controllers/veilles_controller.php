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
    
    public function index() {
        $collection = $this->model->getVeilleFiltered($this->params);
        $matieres = $collection[1];

        $this->set('objects', $collection[0]['objects']);
        $this->set('matieres',$matieres);
        $this->set_pagination($collection);
    }


    public function show() {
        if ( !CriIsNotaire() ) {
            CriRefuseAccess();
        } else {
            parent::show();
        }
    }

    /**
     * @override
     */
    public function set_pagination($collection) {
        parent::set_pagination($collection);
        if( isset( $this->pagination['add_args'] ) ){
            if( isset( $this->pagination['add_args']['controller'] ) ){
                unset( $this->pagination['add_args']['controller'] );
            }
            if( isset( $this->pagination['add_args']['action'] ) ){
                unset( $this->pagination['add_args']['action'] );
            }
        }
    }
    
    /**
     * @override
     */
    public function set_object() {
        if (!empty($this->model->invalid_data)) {
            if (!empty($this->params['id']) && empty($this->model->invalid_data[$this->model->primary_key])) {
                $this->model->invalid_data[$this->model->primary_key] = $this->params['id'];
            }
            $object = $this->model->new_object($this->model->invalid_data);
        } else if (!empty($this->params['id'])) {
            //optimized query
            $object = $this->model->associatePostWithDocumentById($this->params['id']);
        }
        if (!empty($object)) {
            $this->set('object', $object);
            MvcObjectRegistry::add_object($this->model->name, $this->object);
            return true;
        }
        MvcError::warning('Object not found.');
        return false;
    }
    /**
     * Clean array
     *
     * @param array $data
     */
    protected function clean(&$data){
        $data = array_unique($data);
        foreach ( $data as $k => $v ){
            if( !is_numeric($v) ){
                unset($data[$k]);
            }
        }
    }
    //RSS feed
    public function feed(){
        $options = array();
        $options['joins'] = array(
            'Post','Matiere'
        );
        //Set conditions
        $options['conditions'] = array(
            'Post.post_status'=>'publish'
        );
        //Order by date publish
        $$options['order'] = 'Post.post_date DESC' ;
        $title = Config::$rss['title'];//Title of RSS
        $objects = $this->model->find($options);
        $this->set('title',$title);
        $this->set('objects',$objects);
        $this->set('description',Config::$rss['description']);
        $this->render_view('feed', array('layout' => 'feed'));
    }

    public function feedFilter(){
        $matiere = mvc_model('Matiere')->find_one_by_id($this->params['id']);
        if(!$matiere){//no matiere found
            redirectTo404();
        }
        $options = array();
        $options['joins'] = array(
            'Post','Matiere'
        );
        //Set conditions
        $options['conditions'] = array(
            'Post.post_status'=>'publish',
            'Matiere.id'=> $this->params['id']
        );
        //Order by date publish
        $options['order'] = 'Post.post_date ASC' ;
        $objects = $this->model->find($options);
        $title = sprintf(Config::$rss['title_mat'], $matiere->label );//Title of RSS
        $this->set('title',$title);
        $this->set('objects',$objects);
        $this->set('description',Config::$rss['description']);
        $this->render_view('feed', array('layout' => 'feed'));
    }

}

?>