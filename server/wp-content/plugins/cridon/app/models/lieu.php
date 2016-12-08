<?php

class Lieu extends \App\Override\Model\CridonMvcModel
{
    var $table = "{prefix}lieu";
    var $includes = array('Session');
    var $has_many = array(
        'Session' => array('foreign_key' => 'id_lieu')
    );
    var $display_field = 'name';
}
