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
    var $default_search_joins = array('Matiere','Post', 'Session');

    /**
     * Default searchable field
     * @var array
     */
    var $default_searchable_fields = array(
        'id',
        'Post.post_title',
        'Matiere.label',
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
        'matiere' => array(
            'label'=>'Matière',
            'value_method' => 'matiere_edit_link'
        )
    );

    public function __construct()
    {
        parent::__construct();

        $this->model->per_page = CONST_ADMIN_NB_FORMATIONS_PERPAGE;
    }

    public function index()
    {
        if( isset( $this->params['flash'] ) ){
            if( $this->params['flash'] == 'success' ){
                $this->flash('notice', 'L\'article a été bien ajouté!');
            }
        }
        $this->init_default_columns();
        $this->process_params_for_search();

        if (!isset($this->params['joins'])) {
            $this->params['joins'] = array();
        }
        $this->params['order'] = 'ID DESC';

        $collection = $this->model->paginate($this->params);
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
     * Method to publish next year catalog - admin ajax
     * Redirects to new catalog
     */
    public function publishnextyearcatalog(){
        update_option('cridon_next_year_catalog_published',1);
        echo "<br><br>Le calendrier " . date('Y',strtotime('+1 Year')) . " a été correctement publié";
        die();
    }

    /**
     * Load script
     */
    protected function loadScripts()
    {
        wp_register_style('ui-component-css', plugins_url('cridon/app/public/css/style.css'), false);
        wp_enqueue_style('ui-component-css');
    }
}
