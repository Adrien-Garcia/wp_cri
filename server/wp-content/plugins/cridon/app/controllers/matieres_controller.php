<?php

/**
 *
 * This file is part of project
 *
 * File name : matieres_controller.php
 * Project   : wp_cridon
 * @author   : JetPulp
 * @contributor : Renaud Amsellem
 *
 */
require_once 'base_public_controller.php';

class MatieresController extends BasePublicController
{
    function show(){
        $matiere = $this->params['id'];
        if (!empty($matiere)) {
            $virtual_name = array(str_replace('-',' ',esc_sql(strip_tags($matiere))));
            $this->params['conditions'] = array(
                'Matiere.label'=> $virtual_name
            );
        }

        $veille = new Veille;
        $collection = $veille->getList($this->params);
        if (!$collection['objects']){
            redirectTo404();
        } else {
            $this->set('objects', $collection['objects']);
            $this->set('matieres',$matieres);
            $this->set_pagination($collection);
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