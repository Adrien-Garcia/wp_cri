<?php

/**
 *
 * This file is part of project 
 *
 * File name : DocumentEntity.php
 * Project   : wp_cridon
 *
 * @author Etech
 * @contributor Fabrice MILA
 *
 */

class DocumentEntity extends Entity {
    
    public $fields = array(
        'id','file_path','download_url','date_modified','type','type','id_externe'
    );
    
    public function __construct() {
        $this->setMvcModel('document');
    }
}
