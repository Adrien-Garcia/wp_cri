<?php

class Civilite extends \App\Override\Model\CridonMvcModel {

    var $display_field  = 'label';
    var $table          = '{prefix}civilite';
    var $has_many       = array(
        'Notaire' => array(
            'foreign_key' => 'id'
        )
    );
    
}

?>