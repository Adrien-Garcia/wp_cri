<?php

require_once 'base_model.php';

class Sigle extends BaseModel {

    var $display_field  = 'label';
    var $table          = '{prefix}sigle';
    var $has_many       = array(
        'Etude' => array(
            'foreign_key' => 'id_sigle'
        )
    );
    
}

?>