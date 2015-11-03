<?php

class User extends MvcModel {
    var $primary_key   = 'ID';
    var $display_field = 'user_nicename';
    var $table         = '{prefix}users';
    var $has_many      = array(
        'UserCridon' => array(
            'foreign_key' => 'id_wp_user'
        )
    );
}

?>