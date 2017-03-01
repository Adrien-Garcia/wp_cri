<?php

/**
 * Class AdminMatieresController
 *
 * @author etech
 * @contributor Joelio
 * @verison 1.0
 */
// base admin ctrl
require_once 'base_admin_controller.php';

class AdminMatieresController extends BaseAdminController
{
    var $default_searchable_fields = array(
        'id', 
        'label',
        'code',
        'short_label'
    );
    public $default_columns = array(
        'id', 
        'label', 
        'code', 
        'short_label',
        'color' => array( 'value_method' => 'show_color'),
        'picto' => array( 'value_method' => 'show_picto')
    );
    
    public function index() {
        $this->init_default_columns();
        $this->process_params_for_search();
        $collection = $this->model->paginate($this->params);
        $this->set('objects', $collection['objects']);
        $this->set_pagination($collection);
        //Load custom helper
        $this->load_helper('AdminMatiere');
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
    
    public function show_picto( $object ){
        return ( empty( $object->picto ) ) ? null : '<img src="'.$object->picto.'" width="50" height="50" />';
    }

    public function show_color( $object ){
        return ( empty( $object->color ) ) ? 'Pas de couleur' : '<span style="background: '.$object->color.'; padding: 0 .5em;">&nbsp;</span><span>'.$object->color.'</span>';
    }
}