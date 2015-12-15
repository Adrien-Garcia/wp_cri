<?php

require_once 'base_model.php';

class Support extends BaseModel {

    var $display_field = 'label';
    var $table         = '{prefix}support';
    var $has_many       = array(
        'Question' => array(
            'foreign_key' => 'id_support'
        )
    );
}

?>