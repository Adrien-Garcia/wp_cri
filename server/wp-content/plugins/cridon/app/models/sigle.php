<?php

class Sigle extends MvcModel {

    var $display_field  = 'label';
    var $table          = '{prefix}sigle';
    var $has_many       = array(
        'Etude' => array(
            'foreign_key' => 'id_sigle'
        )
    );
    
}

?>