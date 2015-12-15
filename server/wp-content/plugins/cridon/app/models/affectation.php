<?php

require_once 'base_model.php';

class Affectation extends BaseModel {

    var $display_field = 'label';
    var $table         = '{prefix}affectation';
    var $has_many       = array(
        'Question' => array(
            'foreign_key' => 'id_affectation'
        )
    );
}

?>