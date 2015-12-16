<?php

require_once 'base_model.php';

class Civilite extends BaseModel {

    var $display_field  = 'label';
    var $table          = '{prefix}civilite';
    var $has_many       = array(
        'Notaire' => array(
            'foreign_key' => 'id_civilite'
        )
    );
    
}

?>