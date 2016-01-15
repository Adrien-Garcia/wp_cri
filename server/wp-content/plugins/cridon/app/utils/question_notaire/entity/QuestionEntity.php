<?php

/**
 *
 * This file is part of project 
 *
 * File name : QuestionEntity.php
 * Project   : wp_cridon
 *
 * @author Etech
 * @contributor Fabrice MILA
 *
 */

class QuestionEntity extends Entity {
    
    public $fields = array(
        'id','client_number','srenum','id_support','id_competence_1','id_affectation','real_date','wish_date','date_modif',
        'resume','content','juriste','confidential','creation_date'
    );
    
    public function __construct() {
        //Modèle lié au modèle Question de WP_MVC
        $this->setMvcModel('question');
    }

    /**
     * @return array
     */
    public function getJuristeAndAssistant() {
        global $wpdb;
        $sql = "
    SELECT
        q.juriste as juriste_code,
        u.display_name as juriste_name,
        uc.profil as juriste_profil,
        q.yuser as assistant_code,
        u2.display_name as assistant_name,
        uc2.profil as assistant_profil,
        q.id as id
    FROM
        ".$wpdb->prefix."question AS q
            LEFT JOIN
        ".$wpdb->prefix."user_cridon AS uc ON q.juriste = uc.id_erp
            LEFT JOIN
        ".$wpdb->prefix."user_cridon AS uc2 ON q.yuser = uc2.id_erp
            LEFT JOIN
        ".$wpdb->prefix."users AS u ON uc.id_wp_user = u.id
            LEFT JOIN
        ".$wpdb->prefix."users AS u2 ON uc2.id_wp_user = u2.id
    WHERE q.id = ".$this->id.";
        ";
        $r = $wpdb->get_results($sql);
        $result = array();
        foreach($r as $data) {
            $result[$data->id] = $data;
        }
        return $result;
    }

    /**
     * @param $questions
     * @return mixed
     */
    public static function getJuristeAndAssistantFromQuestions($questions) {
        global $wpdb;
        $id_array = array();
        if (is_object($questions) && get_class($questions) == "QuestionEntity") {
            $id_array[] = $questions->id;
        } else if (is_array($questions)
            && is_object($questions[0])
            && get_class($questions[0]) == "Entity"
            && is_object($questions[0]->question)
            && get_class($questions[0]->question) == "QuestionEntity"
        ) {
            foreach ($questions as $q ) {
                $id_array[] = $q->question->id;
            }
        } else if (is_array($questions) && is_int($questions[0])) {
            $id_array = $questions;
        } else if (is_int($questions)) {
            $id_array[] = $questions;
        } else {
            return false;
        }
        $sql = "
    SELECT
        q.juriste as juriste_code,
        u.display_name as juriste_name,
        uc.profil as juriste_profil,
        q.yuser as assistant_code,
        u2.display_name as assistant_name,
        uc2.profil as assistant_profil,
        q.id as id
    FROM
        ".$wpdb->prefix."question AS q
            LEFT JOIN
        ".$wpdb->prefix."user_cridon AS uc ON q.juriste = uc.id_erp
            LEFT JOIN
        ".$wpdb->prefix."user_cridon AS uc2 ON q.yuser = uc2.id_erp
            LEFT JOIN
        ".$wpdb->prefix."users AS u ON uc.id_wp_user = u.id
            LEFT JOIN
        ".$wpdb->prefix."users AS u2 ON uc2.id_wp_user = u2.id
    WHERE q.id IN (".implode(",",$id_array).");
        ";

        $r = $wpdb->get_results($sql);
        $result = array();
        foreach($r as $data) {
            $result[$data->id] = $data;
        }
        return $result;
    }

    
}