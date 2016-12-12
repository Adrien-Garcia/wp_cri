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

    public function __construct()
    {
        $this->set_meta_lieu();
        $this->model->per_page = CONST_ADMIN_NB_ITEM_PERPAGE;
        $this->file_includer = new MvcFileIncluder();
    }

    public function index() {
        $this->init_default_columns();
        $this->process_params_for_search();
        $collection = $this->model->paginate($this->params);
        $this->set('objects', $collection['objects']);
        $this->set_pagination($collection);
        //Load custom helper
        $this->load_helper('AdminLieu');
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

    //MvcInflector::singularize && MvcInflector::tableize is not working with 'Lieux'
    protected function set_meta_lieu() {
        $model = get_class($this);
        $model = preg_replace('/Controller$/', '', $model);
        $this->name = MvcInflector::underscore($model);
        $this->views_path = '';
        if (preg_match('/^Admin[A-Z]/', $model)) {
            $this->views_path = 'admin/';
            $model = preg_replace('/^Admin/', '', $model);
        }

        //DEBUT JETPULP
        //$model = MvcInflector::singularize($model);
        $model = 'Lieu'; // Singulier de 'Lieux'
        //$this->views_path .= MvcInflector::tableize($model).'/';
        $this->views_path .= 'lieux/';
        //FIN JETPULP
        $this->model_name = $model;
        // To do: remove the necessity of this redundancy
        if (class_exists($model)) {
            $model_instance = new $model();
            $this->model = $model_instance;
            $this->{$model} = $model_instance;
        }
    }

}