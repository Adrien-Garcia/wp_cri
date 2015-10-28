<?php

/**
 * Class AdminMatieresController
 *
 * @author etech
 * @contributor Joelio
 * @verison 1.0
 */
class AdminMatieresController extends MvcAdminController
{

    public $default_columns = array(
        'id', 
        'label', 
        'code', 
        'short_label',
        'picto' => array( 'value_method' => 'show_picto')
    );

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
}