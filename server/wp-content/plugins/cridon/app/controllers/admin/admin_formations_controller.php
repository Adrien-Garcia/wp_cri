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

class AdminFormationsController extends BaseAdminController
{

    /**
     * Search join
     * @var array
     */
    var $default_search_joins = array('Post');

    /**
     * Default searchable field
     * @var array
     */
    var $default_searchable_fields = array(
        'id',
        'Post.post_title'
    );

    /**
     * Default columns list
     * @var array
     */
    var $default_columns = array(
        'id',
        'post' => array(
            'label' => 'Titre',
            'value_method' => 'post_edit_link'
        ),
        'date' => array(
            'label' => 'Date',
            'value_method' => 'post_date'
        )
    );

    public function __construct()
    {
        parent::__construct();

        $this->model->per_page = CONST_ADMIN_NB_FORMATIONS_PERPAGE;
    }

    public function index()
    {
        $this->init_default_columns();
        $this->process_params_for_search();

        // params
        $params = $this->params;

        if (isset($_GET['option']) && $_GET['option'] != 'all') {
            $params['joins'] = array('Post');

            // Formations passées : triées de la plus récente à la plus ancienne
            if ($_GET['option'] == 'old') {
                $params['order'] = 'Post.post_date DESC';
                $params['conditions'] = array('Post.post_date < ' => date('Y-m-d'));
            } elseif($_GET['option'] == 'new') { // Formations a venir : triées de la plus proche à la plus éloignée
                $params['order'] = 'Post.post_date ASC';
                $params['conditions'] = array('Post.post_date >= ' => date('Y-m-d'));
            }
        }

        // get collection
        $collection = $this->model->paginate($params);

        // set object to template
        $this->set('objects', $collection['objects']);
        $this->set_pagination($collection);
        // Load custom helper
        $this->load_helper('AdminPost');

        // load scripts
        $this->loadScripts();
    }

    public function add()
    {
        //Default
        $this->redirect('post-new.php?cridon_type=' . $this->trim($this->name), 301);
    }

    public function edit()
    {
        //Default
        $object = MvcObjectRegistry::get_object($this->model->name);
        $this->redirect($this->samplePostEditUrl($object, $this), 301);
    }

    public function post_edit_link($object)
    {
        $aOptionList = array(
            '__name' => 'post_title'
        );
        $this->prepareData($aOptionList, $object->post);

        return empty($object->post) ? null : '<a href="' . $this->postEditUrl($object,
                $this) . '" title="Edit">' . $object->post->__name . '</a>';
    }

    public function post_date($object)
    {
        return date('d/m/Y', strtotime($object->post->post_date));
    }

    private function trim($str)
    {
        return str_replace('admin_', '', $str);
    }

    private function postEditUrl($object, $controller)
    {
        return admin_url('post.php?post=' . $object->post_id . '&action=edit&cridon_type=' . $this->trim($controller->name));
    }

    private function samplePostEditUrl($object, $controller)
    {
        return 'post.php?post=' . $object->post_id . '&action=edit&cridon_type=' . $this->trim($controller->name);
    }

    private function prepareData($aOptionList, $aData)
    {
        if (is_array($aData) && count($aData) > 0) {
            foreach ($aData as $oData) {
                foreach ($aOptionList as $sKey => $sVal) {
                    $oData->$sKey = $oData->$sVal;
                }
            }
        } elseif (is_object($aData)) {
            foreach ($aOptionList as $sKey => $sVal) {
                $aData->$sKey = $aData->$sVal;
            }
        }
    }

    /**
     * Load script
     */
    protected function loadScripts()
    {
        wp_register_style( 'ui-component-css', plugins_url('cridon/app/public/css/style.css'), false );
        wp_enqueue_style( 'ui-component-css' );

        wp_register_script( 'formation-js', plugins_url('cridon/app/public/js/bo/formation.js'), array('jquery') );
        wp_enqueue_script('formation-js');
    }

}