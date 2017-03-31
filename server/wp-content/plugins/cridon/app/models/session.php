<?php

class Session extends \App\Override\Model\CridonMvcModel
{
    use MultiMatieresTrait;

    var $table = "{prefix}session";
    var $includes = array('Formation','Organisme');
    var $belongs_to = array(
        'Formation' => array('foreign_key' => 'id_formation'),
        'Entite' => array(
            'foreign_key' => 'id_organisme',
            'referenced_key' => 'id'
        )
    );
    var $display_field = 'date';

    public function getDuration($session)
    {
        return $session->time_unit_nb . ' ' . $session->time_unit . ($session->time_unit_nb > 1 ? 's' : '');
    }
    
}


