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
        $collection = $this->model->getList($this->params);

        $this->set('objects', $collection['objects']);
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
    
}

?>