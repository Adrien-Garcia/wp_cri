<?php

class UserCridon extends MvcModel {
    var $table        = '{prefix}user_cridon';
    var $display_field = 'id_erp';
    var $includes  = array('User');

    public $belongs_to = array(
        'User' => array(
            'foreign_key' => 'id_wp_user'
        )
    );
    
}

?>