<?php

class Demarche extends \App\Override\Model\CridonMvcModel
{

    public $display_field = 'date';
    public $table = '{prefix}demarche';
    public $belongs_to = array(
        'Session' => array(
            'foreign_key' => 'session_id'
        ),
        'Notaire' => array(
            'foreign_key' => 'notaire_id'
        ),
        'Formation' => array(
            'foreign_key' => 'formation_id'
        )
    );

    public function createFromFormulaire($type, $currentUser, $content, $formationCommentaire, $element)
    {
        $date = new DateTime();
        $data = array(
            'type' => $type,
            'notaire_id' => $currentUser->id,
            'session_id' => $type === CONST_FORMATION_PREINSCRIPTION ? $element->id : 0,
            'formation_id' => $type === CONST_FORMATION_DEMANDE ? $element->id : ($type === CONST_FORMATION_PREINSCRIPTION ? $element->id_formation : 0),
            'details' => $content,
            'commentaire_client' => $formationCommentaire,
            'commentaire_cridon' => '',
            'date' => $date->format('Y-m-d'),
        );
        $this->create($data);
    }
}
