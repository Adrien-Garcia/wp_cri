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
        $collection = $this->model->paginate($this->params);
        
        $this->set('objects', $collection['objects']);
        $this->set_pagination($collection);
    }
    /**
     * @override
     */
    public function set_pagination($collection) {
        parent::set_pagination($collection);
        //cute url in pagination
        if( isset( $this->pagination['add_args'] ) ){
            unset( $this->pagination['add_args'] );
        }
    }
    
}

?>