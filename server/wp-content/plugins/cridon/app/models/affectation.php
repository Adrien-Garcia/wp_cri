<?php

class Affectation extends MvcModel {

    var $display_field = 'label';
    var $table         = '{prefix}affectation';
    var $has_many       = array(
        'Question' => array(
            'foreign_key' => 'id_affectation'
        )
    );
}

?>