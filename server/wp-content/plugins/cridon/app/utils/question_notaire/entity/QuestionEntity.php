<?php

/**
 *
 * This file is part of project 
 *
 * File name : QuestionEntity.php
 * Project   : wp_cridon
 *
 * @author Etech
 * @contributor Fabrice MILA
 *
 */

class QuestionEntity extends Entity {
    
    public $fields = array(
        'id','client_number','srenum','id_support','id_competence_1','id_affectation','real_date','date_modif',
        'resume','content','juriste','confidential'
    );
    
    public function __construct() {
        //Modèle lié au modèle Question de WP_MVC
        $this->setMvcModel('question');
    }
    
}