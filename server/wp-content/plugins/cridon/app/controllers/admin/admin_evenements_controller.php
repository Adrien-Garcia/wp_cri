<?php

/**
 *
 * This file is part of project 
 *
 * File name : admin_evenements_controller.php
 * Project   : wp_cridon
 *
 */
// base admin ctrl
require_once 'base_admin_controller.php';

class AdminEvenementsController extends BaseAdminController
{
    /**
     * Default searchable field
     * @var array
     */
    var $default_searchable_fields = array(
        'id',
        'name',
        'date',
    );

    /**
     * Default columns list
     * @var array
     */
    var $default_columns = array(
        'id',
        'name' => array(
            'label' => 'Évènement',
        ),
        'date' => array(
            'label' => 'Jour de l\'évènement',
            'value_method' => 'eventDate',
        ),
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
        $this->prepareInputDate();
        $this->create_or_save();
        $this->load_helper('CustomForm');
    }

    public function edit() {
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

        wp_register_script('datepicker-js', plugins_url('cridon/app/public/js/bo/datepicker.js'), array('jquery') );
        wp_enqueue_script('datepicker-js');
        wp_enqueue_script('jquery-ui-i18n-fr', plugins_url('cridon/app/public/js/jquery.ui.datepicker-fr.js'), array('jquery-ui-datepicker'));
        wp_enqueue_style('jquery-ui-css', plugins_url('cridon/app/public/css/jquery-ui.css'));
    }

    public function eventDate($object) {
        return strftime('%d %B %G',strtotime($object->date));
    }

    public function create_or_save()
    {
        if (!empty($this->params['data'])) {
            $this->load_helper('AdminCustom');
            $this->params['data']['Evenement']['date'] = $this->admin_custom->dateToDbFormat($this->params['data']['Evenement']['date']);
        }
        parent::create_or_save();
    }
}
