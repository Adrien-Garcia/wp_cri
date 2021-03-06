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
    var $default_search_joins = array('Matiere','Post');

    /**
     * Default searchable field
     * @var array
     */
    var $default_searchable_fields = array(
        'id',
        'Post.post_title',
        'Matiere.label',
        'Formation.address',
        'Formation.postal_code',
        'Formation.town'
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
        ),
        'matiere' => array(
            'label'=>'Matière',
            'value_method' => 'matiere_edit_link'
        ),
        'address' => array(
            'label'=>'Adresse'
        ),
        'postal_code' => array(
            'label'=>'Code Postal'
        ),
        'town' => array(
            'label'=>'Ville'
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

        $params = $this->params;

        if (isset($_GET['option']) && $_GET['option'] != 'all') {
            if ($_GET['option'] == 'old') { // Formations passées : triées de la plus récente à la plus ancienne
                $params['order']      = 'custom_post_date DESC';
                $params['conditions'] = array('custom_post_date < ' => date('Y-m-d'));
            } elseif ($_GET['option'] == 'new') { // Formations a venir : triées de la plus proche à la plus éloignée
                $params['order']      = 'custom_post_date ASC';
                $params['conditions'] = array('custom_post_date >= ' => date('Y-m-d'));
            }
        }

        $collection = $this->model->paginate($params);
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
        $custom_date = '';
        if (property_exists($object, 'custom_post_date') && $object->custom_post_date && $object->custom_post_date != '0000-00-00') {
            $custom_date = date('d/m/Y', strtotime($object->custom_post_date));
        }
        return $custom_date;
    }

    private function trim($str){
        return str_replace('admin_', '', $str);
    }
    private function postEditUrl($object, $controller){
        return admin_url('post.php?post=' . $object->post_id . '&action=edit&cridon_type=' . $this->trim($controller->name));
    }
    private function samplePostEditUrl($object, $controller){
        return 'post.php?post=' . $object->post_id . '&action=edit&cridon_type=' . $this->trim($controller->name);
    }
    private function prepareData($aOptionList, $aData){
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

    public function matiere_edit_link($object)
    {
        $aOptionList = array(
            '__name'    => 'label'
        );
        $this->prepareData($aOptionList, $object->matiere);
        return empty($object->matiere) ? Config::$defaultMatiere['name'] : HtmlHelper::admin_object_link($object->matiere, array('action' => 'edit'));
    }

    /**
     * Load script
     */
    protected function loadScripts()
    {
        wp_register_style('ui-component-css', plugins_url('cridon/app/public/css/style.css'), false);
        wp_enqueue_style('ui-component-css');

        wp_register_script('formation-js', plugins_url('cridon/app/public/js/bo/formation.js'), array('jquery'));
        wp_enqueue_script('formation-js');
    }
}