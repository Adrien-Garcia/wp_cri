<?php

class Sigle extends \App\Override\Model\CridonMvcModel {

    var $display_field  = 'label';
    var $table          = '{prefix}sigle';
    var $has_many       = array(
        'Entite' => array(
            'foreign_key' => 'id'
        )
    );
    
}

?>
