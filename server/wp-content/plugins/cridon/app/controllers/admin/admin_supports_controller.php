<?php

/**
 *
 * This file is part of project 
 *
 * File name : admin_supports_controller.php
 * Project   : wp_cridon
 *
 * @author Etech
 * @contributor Fabrice MILA
 *
 */

// base admin ctrl
require_once 'base_admin_controller.php';

class AdminSupportsController extends BaseAdminController {
    
    var $default_columns = array('id', 'label', 'value');
    public function index() {
        $this->init_default_columns();
        $this->process_params_for_search();
        $collection = $this->model->paginate($this->params);
        $this->set('objects', $collection['objects']);
        $this->set_pagination($collection);
        //Load custom helper
        $this->load_helper('AdminCustom');
    }
}

?>