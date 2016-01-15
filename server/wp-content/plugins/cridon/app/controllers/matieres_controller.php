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
        //Récupération de l'id dans l'url
        $id = $this->params['id'];
        if (!empty($id)) {
            //appel au controlleur des veilles en lui indiquant la matiere courante
            $this->params['conditions'] = array(
                    'Veille.id_matiere' => $id
            );
            $veille = new Veille;
            $collection = $veille->getList($this->params);
            if (!$collection['objects']){
                redirectTo404();
            } else {
                $this->set('objects', $collection['objects']);
                $this->set_pagination($collection);
            }
        } else {
            redirectTo404();
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