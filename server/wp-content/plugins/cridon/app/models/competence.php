<?php

class Competence extends MvcModel
{
    var $display_field  = 'label';
    var $table          = '{prefix}competence';
    var $includes       = array('Matiere');
    var $belongs_to     = array(
        'Matiere' => array('foreign_key' => 'code_matiere')
    );
}