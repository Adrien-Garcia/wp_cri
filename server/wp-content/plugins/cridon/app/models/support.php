<?php

class Support extends MvcModel {

    var $display_field = 'label';
    var $table         = '{prefix}support';
    var $has_many       = array(
        'Question' => array(
            'foreign_key' => 'id_support'
        )
    );
}

?>