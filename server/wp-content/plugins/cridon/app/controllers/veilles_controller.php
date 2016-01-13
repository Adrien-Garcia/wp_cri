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
        $this->params['per_page'] = !empty($this->params['per_page']) ? $this->params['per_page'] : DEFAULT_POST_PER_PAGE;
        //Set explicit join
        $this->params['joins'] = array(
            'Post','Matiere'
        );
        //Set conditions
        $this->params['conditions'] = array(
            'Post.post_status'=>'publish'            
        );
        //All Matiere
        $matieres = mvc_model('Matiere')->find();
        //Filter by Matiere
        if( isset($_GET['matiere']) && !empty($_GET['matiere']) ){
            $q = esc_sql(strip_tags(urldecode($_GET['matiere'])));
            $virtual_names = explode(',',$q);
            $this->params['conditions'] = array(
                'Post.post_status'=>'publish',
                'Matiere.virtual_name'=> $virtual_names         
            );
            foreach($matieres as $matiere){
                if( in_array($matiere->virtual_name,$virtual_names) ){
                    $matiere->filtered = true;
                }else{
                    $matiere->filtered = false;
                }
            }
        }else{
            foreach($matieres as $matiere){
                $matiere->filtered = false;
            }
        }
        //Order by date publish
        $this->params['order'] = 'Post.post_date DESC' ;
        $collection = $this->model->paginate($this->params);
        
        $this->set('objects', $collection['objects']);
        $this->set('matieres',$matieres);//All matieres
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
    
    /**
     * @override
     * @return boolean
     */
    public function set_object() {
        if (!empty($this->model->invalid_data)) {
            if (!empty($this->params['id']) && empty($this->model->invalid_data[$this->model->primary_key])) {
                $this->model->invalid_data[$this->model->primary_key] = $this->params['id'];
            }
            $object = $this->model->new_object($this->model->invalid_data);
        } else if (!empty($this->params['id'])) {
            $aObject = $this->model->find(
               array(
                   'joins' => array('Post'),
                   'conditions' => array(
                       'Post.post_name' => $this->params['id']
                   )
               )
            );
            if(!empty($aObject)){
                $object = $aObject[0];
            }else{
                $object = null;
            }
        }
        if (!empty($object)) {
            $this->set('object', $object);
            MvcObjectRegistry::add_object($this->model->name, $this->object);
            return true;
        }
        MvcError::warning('Object not found.');
        return false;
    }
}

?>