<?php

/**
 *
 * This file is part of project 
 *
 * File name : NotaireEntity.php
 * Project   : wp_cridon
 *
 * @author Etech
 * @contributor Fabrice MILA
 *
 */

class NotaireEntity extends Entity {
    
    public $fields = array(
        'id','client_number','crpcen'
    );
    
    public function __construct() {
        $this->setMvcModel('notaire');
    }
}
