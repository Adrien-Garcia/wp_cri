<?php

class User extends MvcUser {
    var $has_many      = array(
        'UserCridon' => array(
            'foreign_key' => 'ID'
        )
    );
}

?>