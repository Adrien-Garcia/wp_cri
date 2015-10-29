<?php

class Civilite extends MvcModel {

    var $display_field  = 'label';
    var $table          = '{prefix}civilite';
    var $has_many       = array(
        'Notaire' => array(
            'foreign_key' => 'id_civilite'
        )
    );
    
}

?>