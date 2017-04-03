<?php

/**
 *
 * This file is part of project 
 *
 * File name : UIMillesimeContainer.php
 * Project   : wp_cridon
 *
 */

class UIMatiereContainer extends UIContainer {

    private $database;
    protected $modelName = 'matiere';

    public function __construct(){
        $this->database = new UIMatiereDatabase();
        parent::__construct();
    }

    protected Function prepareLeftData(){
        // All matieres for current model
        $data = new stdClass();
        $data->__id = $this->currentObject->id;
        $data->__model_name = $this->currentModel->name;
        $data = $this->currentModel->getMatieres();
        return $this->createMatiereItems($data);
    }

    protected Function prepareRightData(){
        if( $this->currentObject != null ){
            $data = new stdClass();
            $data->__id = $this->currentObject->id;
            $data->__model_name = $this->currentModel->name;
            $data = $this->currentModel->getMatieres();
            return $this->createMatiereItems($data);
        }
    }

    protected function createMatiereItems($data) {
        $res = array();
        foreach( $data as $v ){
            $cls = new stdClass();
            $cls->id = $v->id;
            $cls->name = $v->label;
            $res[] = $cls;
        }
        return $res;
    }

    public function save(){
        if( isset( $_POST ) ) {
            // Remove all millesime for current formation in database
            $this->database->deleteAll($this->currentObject->id);
            if (!empty( $_POST['uimillesime'] ) ){
                $data = array();
                foreach( $_POST['uimillesime'] as $millesime ){
                    $cls = new stdClass();
                    $cls->year  = preg_replace("/ui_a/", '', $millesime);
                    $cls->id_formation = $this->currentObject->id;
                    $data[] = $cls;
                }
                $this->database->save($data);
            }
        }
    }
}
