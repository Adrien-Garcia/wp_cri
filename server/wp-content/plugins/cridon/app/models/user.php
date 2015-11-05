<?php

class User extends MvcUser {
    var $has_many      = array(
        'UserCridon' => array(
            'foreign_key' => 'id_wp_user'
        )
    );
}

?>