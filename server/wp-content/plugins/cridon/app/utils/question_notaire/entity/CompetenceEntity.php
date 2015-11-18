<?php

/**
 *
 * This file is part of project 
 *
 * File name : CompetenceEntity.php
 * Project   : wp_cridon
 *
 * @author Etech
 * @contributor Fabrice MILA
 *
 */

class CompetenceEntity extends Entity{
    
    public $fields = array(
        'id','label','displayed','code_matiere'
    );
    
    public function __construct() {
        //Modèle lié au modèle Competence de WP_MVC
        $this->setMvcModel('competence');
    }
}
