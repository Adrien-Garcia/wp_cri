<?php

/**
 *
 * This file is part of project 
 *
 * File name : UIDocumentDatabase.php
 * Project   : wp_cridon
 *
 * @author Etech
 * @contributor Fabrice MILA
 *
 */

class UIDocumentDatabase {
    private $model;
    
    public function __construct(){
        $this->model = mvc_model('Document');
    }
    
    /**
     * Find document
     * @param array $options
     * @return mixed
     */
    public function find( $options = array() ){
        return $this->model->find( $options );
    }
    
    /**
     * Update document
     * 
     * @param mixed $data
     */
    public function save( $data ){
        $this->compare($data);
        foreach( $data as $v ){
            $options = array(
                $this->model->name => array(
                    'id' => $v->id,
                    'id_externe' => $v->id_externe,
                    'type' => $v->type
                )
            );
            //update
            $this->model->save( $options );
        }
    }
    
    /**
     * Delete document
     * 
     * @param integer $id
     */
    public function delete( $id ){
        $this->model->delete( $id );
    }
    
    /**
     * Compare new document and older document for current object
     * @param mixed $data
     * @return boolean
     */
    protected function compare( $data ){
        if( !empty( $data ) ){
            $options = array(
                'conditions' => array(
                    'type' => $data[0]->type,
                    'id_externe'=>  $data[0]->id_externe,
                ) 
            );
            //All documents
            $all = $this->model->find( $options );
            if( empty( $all ) ){
                return true;
            }
            $aToComp = array();
            foreach( $data as $v ){
                $aToComp[] = $v->id;
            }
            foreach( $all as $val ){
                if( !in_array( $val->id, $aToComp) ){
                    $this->delete( $val->id );
                    $uploadDir = wp_upload_dir();
                    $file = $uploadDir['basedir'].$val->file_path;
                    if( file_exists( $file ) ){
                        unlink($file);//unlink file
                    }
                }
            }
        }
    }
    /**
     * Delete all document for current object
     * 
     * @param object $data
     * @return boolean
     */
    public function deleteAll( $data ){
        $options = array(
            'conditions' => array(
                'type' => $data->type,
                'id_externe'=>  $data->id_externe,
            ) 
        );
        $all = $this->model->find( $options );
        if( empty($all) ){
            return true;
        }
            
        foreach( $all as $val ){
            $this->delete( $val->id );
            $uploadDir = wp_upload_dir();
            $file = $uploadDir['basedir'].$val->file_path;
            if( file_exists( $file ) ){
                unlink($file);//unlink file
            }
        }
    }
}
