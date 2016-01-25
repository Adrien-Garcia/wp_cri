<?php

class Support extends \App\Override\Model\CridonMvcModel {

    var $display_field = 'label';
    var $table         = '{prefix}support';
    var $has_many       = array(
        'Question' => array(
            'foreign_key' => 'id'
        )
    );
}

?>