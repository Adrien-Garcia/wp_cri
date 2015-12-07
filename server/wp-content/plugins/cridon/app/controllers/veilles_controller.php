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
        //Order by date publish
        $this->params['order'] = 'Post.post_date DESC' ;
        $collection = $this->model->paginate($this->params);
        
        $this->set('objects', $collection['objects']);
        $this->set_pagination($collection);
    }


    public function show() {
        if ( !CriIsNotaire() ) {
            $referer = $_SERVER['HTTP_REFERER'];
            $redirect = $referer;

            if (preg_match("/.*?[\?\&]openLogin=1.*?/", $referer) === 1 && preg_match("/.*?[\?\&]messageLogin=PROTECTED_CONTENT.*?/", $referer) === 1 ) {
                wp_redirect($redirect);
                return;
            }

            if (preg_match("/.*\?.*/", $referer)) {
                $redirect .= "&";
            } else {
                $redirect .= "?";
            }

            $redirect .= "openLogin=1&messageLogin=PROTECTED_CONTENT";

            wp_redirect($redirect);
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
    
}

?>