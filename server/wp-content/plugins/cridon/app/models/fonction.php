<?php

class Fonction extends \App\Override\Model\CridonMvcModel {

    var $display_field  = 'label';
    var $table          = '{prefix}fonction';
    var $has_many       = array(
        'Notaire' => array(
            'foreign_key' => 'id'
        )
    );
    
}

?>