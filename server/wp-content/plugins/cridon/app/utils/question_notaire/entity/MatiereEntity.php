<?php

/**
 *
 * This file is part of project 
 *
 * File name : MatiereEntity.php
 * Project   : wp_cridon
 *
 * @author Etech
 * @contributor Fabrice MILA
 *
 */

class MatiereEntity extends Entity {
    
    public $fields = array(
        'id','code','label','short_label','displayed','picto'
    );
    
    public function __construct() {
        //Modèle lié au modèle Matiere de WP_MVC
        $this->setMvcModel('matiere');
    }
}
