<?php

class Session extends \App\Override\Model\CridonMvcModel
{
    var $table = "{prefix}session";
    var $includes = array('Formation','Lieu');
    var $belongs_to = array(
        'Formation' => array('foreign_key' => 'id_formation'),
        'Lieu' => array('foreign_key' => 'id_lieu')
    );
    var $display_field = 'date';
}
