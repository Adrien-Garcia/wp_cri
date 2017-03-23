<?php

/**
 *
 * This file is part of project 
 *
 * File name : admin_formtions_controller.php
 * Project   : wp_cridon
 *
 * @author Etech
 * @contributor Fabrice MILA
 *
 */
// base admin ctrl
require_once 'base_admin_controller.php';

class AdminSessionsController extends BaseAdminController
{
    /**
     * Default searchable field
     * @var array
     */
    var $default_searchable_fields = array(
        'id',
        'date',
    );

    /**
     * Default columns list
     * @var array
     */
    var $default_columns = array(
        'id',
        'date' => array(
            'label' => 'Date de la session',
            'value_method' => 'sessionDate'
        ),
        'timetable' => array(
            'label'=>'Informations horaires'
        ),
        'formation' => array(
            'label' => 'Formation',
            'value_method' => 'formationLink'
        ),
        'organisme' => array(
            'label' => 'Organisme',
            'value_method' => 'organismeLabel'
        )
    );

    public function index() {
        $this->init_default_columns();
        $this->process_params_for_search();
        $this->params['order'] = 'date DESC';
        $collection = $this->model->paginate($this->params);
        $this->set('objects', $collection['objects']);
        $this->set_pagination($collection);
        //Load custom helper
        $this->load_helper('AdminNoview');
    }

    public function add(){
        $this->setFormations();
        $this->setOrganismes();
        $this->prepareInputDate();
        $this->create_or_save();
        $this->load_helper('CustomForm');
    }

    public function edit() {
        $this->setFormations();
        $this->setOrganismes();
        $this->prepareInputDate();
        $this->verify_id_param();
        $this->create_or_save();
        $this->set_object();
        $this->load_helper('CustomForm');
    }

    private function prepareInputDate()
    {
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-datepicker');

        wp_enqueue_script('jquery-ui-i18n-fr', plugins_url('cridon/app/public/js/jquery.ui.datepicker-fr.js'), array('jquery-ui-datepicker'));
        wp_register_script('formation-js', plugins_url('cridon/app/public/js/bo/formation.js'), array('jquery') );
        wp_enqueue_script('formation-js');
        wp_register_script('datepicker-js', plugins_url('cridon/app/public/js/bo/datepicker.js'), array('jquery') );
        wp_enqueue_script('datepicker-js');
        wp_enqueue_style('jquery-ui-css', plugins_url('cridon/app/public/css/jquery-ui.css'));
    }

    private function setFormations()
    {
        $this->load_model('Formation');
        $formations = $this->Formation->find(array(
            'selects' => array('id', 'Post.post_title', 'Matiere.code', 'Matiere.label'),
            'joins' => array('Post', 'Matiere'),
            'order' => 'Matiere.code DESC'
        ));

        $options = array();
        if (is_array($formations) && count($formations) > 0) {
            foreach ($formations as $formation) {
                if (!isset($formation->post) || empty($formation->post->post_title)) {
                    continue;
                }
                $option = new StdClass();
                $option->__id = $formation->id;
                $option->__name = $formation->post->post_title;
                $option->__group = $formation->matiere->label;
                $options[$formation->id] =  $option;
            }
        }

        $this->set('formations', $options);
    }

    private function setOrganismes()
    {
        $this->load_model('Entite');
        $organismes = $this->Entite->find(array(
            'selects' => array('id', 'office_name'),
            'conditions' => array(
                'is_organisme' => 1
            ),
            'joins' => array(),//dummy join to avoid loading of all relations
            'order' => 'office_name'
        ));

        $options = array();
        if (is_array($organismes) && count($organismes) > 0) {
            foreach ($organismes as $organisme) {
                $options[$organisme->id] =  $organisme->office_name;
            }
        }

        $this->set('organismes', $options);
    }

    public function formationLink($object){
        if (empty($object->formation)) {
            $this->load_model('Formation');
            $object->formation = $this->Formation->find_one_by_id($object->id_formation);
        }

        $controllerFormations = new AdminFormationsController();
        return empty($object->formation) ? null : $controllerFormations->post_edit_link($object->formation);
    }

    public function sessionDate($object){
        $return = strftime('%d %B %G',strtotime($object->date));
        if ($object->is_full) {
            $return = '<span style="color: red;">Complet - ' . $return . '</span>';
        }
        return $return;
    }

    public function organismeLabel($object){
        if (empty($object->organisme)) {
            $this->load_model('Entite');
            $object->organisme = $this->Entite->find_one_by_id($object->id_organisme);
        }

        return empty($object->organisme) ? null : $object->organisme->office_name;
    }
}
