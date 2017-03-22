<?php
/*
 * This file is part of the JETPULP wp_cridon project.
 *
 * Copyright (C) JETPULP
 */
require_once 'base_admin_controller.php';

class AdminDemarchesController extends BaseAdminController
{
    /**
     * Search join
     * @var array
     */
    var $default_search_joins = array('Notaire','Session');

    /**
     * Default searchable field
     * @var array
     */
    var $default_searchable_fields = array(
        'date',
        'Notaire.email_adress',
        'Notaire.last_name',
        //'Session.Formation.Post.post_title' TODO
    );

    /**
     * Default columns list
     * @var array
     */
    var $default_columns = array(
        'id',
        'type' => array(
            'label' => 'Type de démarche',
            'value_method' => 'workflowDisplay'
        ),
        'date' => array(
            'label' => 'Date de la démarche',
            'value_method' => 'demarcheDate'
        ),
        'email' => array(
            'label' => 'Adresse e-mail du demandeur',
            'value_method' => 'sendMailLink'
        ),
        'name' => array(
            'label'=>'Nom du demandeur',
            'value_method' => 'notaireDisplayname'
        ),
        'etude' => array(
            'label'=>'CPRCEN',
            'value_method' => 'crpcenDispay'
        ),
        'formation' => array(
            'label' => 'Formation',
            'value_method' => 'formationLink'
        ),
        'organisme' => array(
            'label' => 'Organisme',
            'value_method' => 'organismeDisplay'
        ),
        'date_session' => array(
            'label' => 'Date de la session',
            'value_method' => 'sessionDisplay'
        ),
    );

    public function index() {
        $this->init_default_columns();
        $this->process_params_for_search();
        $this->params['order'] = 'ID DESC';
        $collection = $this->model->paginate($this->params);
        $this->set('objects', $collection['objects']);
        $this->set_pagination($collection);
        //Load custom helper
        $this->load_helper('AdminView');
    }

    public function edit() {
        $this->verify_id_param();
        $this->create_or_save();
        $this->set_object();
        $this->load_helper('CustomForm');
    }

    public function workflowDisplay($object) {
        return Config::$labelWorflowFormation[$object->type];
    }

    public function demarcheDate($object) {
        return strftime('%d %B %G',strtotime($object->date));
    }

    private function loadNotaire(& $object) {
        if (empty($object->notaire)) {
            $this->load_model('Notaire');
            $object->notaire = $this->Notaire->find_one_by_id($object->notaire_id);
        }
    }

    private function loadSession(& $object) {
        if (empty($object->session)) {
            $this->load_model('Session');
            $object->session = $this->Session->find_one_by_id($object->session_id);
        }
    }

    private function loadFormation(& $object) {
        $this->loadSession($object);
        if (empty($object->formation)) {
            if (!empty($object->session->formation)) {
                $object->formation = $object->session->formation;
            } else {
                $this->load_model('Formation');
                $object->formation = !empty($object->id_formation) ? $this->Formation->find_one_by_id($object->id_formation) : $this->Formation->find_one_by_id($object->session->id_formation);
            }
        }
    }

    private function loadOrganisme(& $object) {
        $this->loadSession($object);
        if (empty($object->session->entite)) {
            $this->load_model('Entite');
            $object->session->entite = $this->Entite->find_one_by_id($object->session->id_organisme);
        }
    }

    public function formationLink($object){
        $this->loadFormation($object);
        $controllerFormations = new AdminFormationsController();
        return empty($object->formation) ? null : $controllerFormations->post_edit_link($object->formation);
    }

    public function organismeDisplay($object) {
        $this->loadOrganisme($object);
        return empty($object->session->entite) ? null : $object->session->entite->office_name ;
    }

    public function sessionDisplay($object) {
        $this->loadSession($object);
        return empty($object->session) ? null : $object->session->date;
    }

    public function sendMailLink($object){
        $this->loadNotaire($object);
        return empty($object->notaire) ? null : '<a href="mailto:'.$object->notaire->email_adress.'" title="Contacter par mail" >'.$object->notaire->email_adress.'</a>';
    }

    public function notaireDisplayname($object){
        $this->loadNotaire($object);
        return empty($object->notaire) ? null : $object->notaire->first_name . ' ' . $object->notaire->last_name;
    }

    public function crpcenDispay($object){
        $this->loadNotaire($object);
        return empty($object->notaire) ? null : $object->notaire->crpcen;
    }
}
