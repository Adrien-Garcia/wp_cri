<?php

class UserCridon extends MvcModel {

    var $display_field = 'id_erp';
    public $belongs_to = array(
        'User' => array(
            'class'       => 'MvcUser',
            'foreign_key' => 'ID'
        )
    );
    
}

?>