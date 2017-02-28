<?php

/**
 *
 * This file is part of project 
 *
 * File name : UIMillesimeContainer.php
 * Project   : wp_cridon
 *
 */

class UIMillesimeContainer extends UIContainer {

    private $database;
    protected $modelName = 'millesime';

    public function __construct(){
        $this->database = new UIMillesimeDatabase();
        parent::__construct();
    }

    protected Function prepareLeftData(){
        // We give the possibility to add millesime from Y-1 to Y+2
        $leftData = array();
        for ($i= -1 ;$i < 3 ;$i++){
            $left       = new stdClass();
            $left->id = $left->name = date("Y",strtotime($i." year"));
            $leftData [] = $left;
        }
        return $leftData;
    }

    protected Function prepareRightData(){
        if( $this->currentObject != null ){
            //Get millésime for current model
            $options = array(
                'conditions' => array(
                    'id_formation' => $this->currentObject->id,
                )
            );
            $data = $this->database->find($options);
            $rightData = array();
            foreach ($data as $item){
                $right = new stdClass();
                $right->id = $right->name = $item->year;
                $rightData [] = $right;
            }
            return $rightData;
        }
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
