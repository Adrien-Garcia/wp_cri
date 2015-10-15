<?php

class Question extends MvcModel {

    var $display_field = 'srenum';
    var $table         = '{prefix}question';
    var $includes      = array('Competence','Affectation','Support', 'Notaire');
    var $belongs_to    = array(
        'Competence' => array('foreign_key' => 'id_competence_1'),
        //'Competence_2' => array('foreign_key' => 'id_competence_2'),
        'Affectation' => array('foreign_key' => 'id_affectation'),
        'Support' => array('foreign_key' => 'id_support')
    );
    var $has_and_belongs_to_many = array(
        'Competence' => array(
            'foreign_key' => 'id_question',
            'association_foreign_key' => 'id_competence',
            'join_table' => '{prefix}question_competence',
            'fields' => array('id','label','code_matiere')
        )
    );

}

?>