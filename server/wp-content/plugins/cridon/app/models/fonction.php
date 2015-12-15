<?php

require_once 'base_model.php';

class Fonction extends MvcModel {

    var $display_field  = 'label';
    var $table          = '{prefix}fonction';
    var $has_many       = array(
        'Notaire' => array(
            'foreign_key' => 'id_fonction'
        )
    );
    
}

?>