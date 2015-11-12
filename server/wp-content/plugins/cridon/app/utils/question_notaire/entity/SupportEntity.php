<?php

/**
 *
 * This file is part of project 
 *
 * File name : SupportEntity.php
 * Project   : wp_cridon
 *
 * @author Etech
 * @contributor Fabrice MILA
 *
 */

class SupportEntity extends Entity{
   
    public $fields = array(
        'id','label','value'
    );
    
    public function __construct() {
        $this->setMvcModel('support');
    }
}
