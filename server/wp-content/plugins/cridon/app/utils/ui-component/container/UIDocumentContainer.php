<?php

/**
 *
 * This file is part of project 
 *
 * File name : UIDocumentContainer.php
 * Project   : wp_cridon
 *
 */

class UIDocumentContainer extends UIContainer {

    private $database;
    protected $modelName = 'document';

    public function __construct(){
        $this->database  = new UIDocumentDatabase();
        parent::__construct();
    }

    protected function prepareLeftData(){
        //Fill with not associated documents
        $data = $this->database->find( array('conditions' => array(
            'type' => $this->type,
            'id_externe' => '',
        ) ) );
        return $this->createDocumentItems($data);
    }

    protected Function prepareRightData(){
        if( $this->currentObject != null ){
            //Get documents for current model
            $options = array(
                'conditions' => array(
                    'type' => $this->type,
                    'id_externe' => $this->currentObject->id,
                )
            );
            $data = $this->database->find($options);
            //Documents of current object ( model )
            return $this->createDocumentItems($data);
        }
    }

    protected function createDocumentItems($data) {
        $res = array();
        foreach( $data as $v ){
            $cls = new stdClass();
            $cls->id = $v->id;
            $fileinfo = explode('/', $v->file_path);
            $cls->name = array_pop($fileinfo);
            $res[] = $cls;
        }
        return $res;
    }
    
    public function save(){
        if( isset( $_POST ) ) {
            //Update documents in database
            if (!empty( $_POST['uidocument'] )  ){
                $data = array();
                foreach( $_POST['uidocument'] as $doc ){
                    $cls = new stdClass();
                    $cls->id = preg_replace("/ui_a/", '', $doc);
                    $cls->id_externe = $this->currentObject->id;
                    $cls->type = strtolower($this->currentModel->name);
                    $data[] = $cls;
                }
                $this->database->save($data);
            } else {
                if (!empty($this->currentObject)) {
                    $data = new stdClass();
                    $data->id_externe = $this->currentObject->id;
                    $data->type = strtolower($this->currentModel->name);
                    $this->database->deleteAll($data);
                }
            }
        }
    }
}
