<?php

class Session extends \App\Override\Model\CridonMvcModel
{
    var $table = "{prefix}session";
    var $includes = array('Formation','Organisme');
    var $belongs_to = array(
        'Formation' => array('foreign_key' => 'id_formation'),
        'Organisme' => array('foreign_key' => 'id_organisme')
    );
    var $display_field = 'date';
}
