<?php

class Matiere extends MvcModel
{
    var $display_field  = 'label';
    var $table          = '{prefix}matiere';
    var $has_many       = array(
        'Competence' => array(
            'foreign_key' => 'code_matiere'
        )
    );
}