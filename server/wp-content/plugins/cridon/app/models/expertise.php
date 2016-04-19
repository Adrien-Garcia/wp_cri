<?php

class Expertise extends \App\Override\Model\CridonMvcModel {

    var $display_field = 'label';
    var $table         = '{prefix}expertise';
    var $has_many       = array(
        'Support' => array(
            'foreign_key' => 'id'
        )
    );
}

?>