<?php

/**
 *
 * This file is part of project 
 *
 * File name : UIMillesimeDatabase.php
 * Project   : wp_cridon
 *
 */

class UIMatiereDatabase extends UIDatabase {

    public function __construct(){
        $this->model = mvc_model('Matiere');
    }

    /**
     * Save millesimes
     * 
     * @param mixed $data
     */
    public function save( $data ){
        foreach( $data as $v ){
            $options = array(
                'id_formation' => $v->id_formation,
                'year' => $v->year
            );
            $this->model->save( $options );
        }
    }

    /**
     * Delete all millesimes for current object
     * 
     * @param int $id_formation
     */
    public function deleteAll( $id_formation ){
        $options = array(
            'conditions' => array(
                'id_formation' => $id_formation
            ) 
        );
        $this->model->delete_all( $options );
    }
}
