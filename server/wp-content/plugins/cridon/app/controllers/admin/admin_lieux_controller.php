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

class AdminLieuxController extends BaseAdminController
{
    /**
     * Default searchable field
     * @var array
     */
    var $default_searchable_fields = array(
        'id',
        'name',
        'is_cridon',
        'address',
        'city',
        'postal_code'
    );

    /**
     * Default columns list
     * @var array
     */
    var $default_columns = array(
        'id',
        'name' => array(
            'label' => 'Nom du lieu',
        ),
        'is_cridon' => array(
            'label'=>'Le lieu est le cridon ?'
        ),
        'address' => array(
            'label'=>'Adresse'
        ),
        'postal_code' => array(
            'label'=>'Code Postal'
        ),
        'city' => array(
            'label'=>'Ville'
        ),
        'phone_number' => array(
            'label'=>'Numéro de téléphone'
        ),
        'email' => array(
            'label'=>'Email'
        )
    );

    public function index() {
        $this->init_default_columns();
        $this->process_params_for_search();
        $collection = $this->model->paginate($this->params);
        $this->set('objects', $collection['objects']);
        $this->set_pagination($collection);
        //Load custom helper
        $this->load_helper('AdminNoview');
    }

    public function add(){
        $this->create_or_save();
        $this->load_helper('CustomForm');
    }

    public function edit() {
        $this->verify_id_param();
        $this->create_or_save();
        $this->set_object();
        $this->load_helper('CustomForm');
    }

}
