<?php

/**
 *
 * This file is part of project 
 *
 * File name : AffectationEntity.php
 * Project   : wp_cridon
 *
 * @author Etech
 * @contributor Fabrice MILA
 *
 */

class AffectationEntity extends Entity {
   
    public $fields = array(
        'id','label'
    );
    public function __construct() {
        $this->setMvcModel('affectation');
    }
}
