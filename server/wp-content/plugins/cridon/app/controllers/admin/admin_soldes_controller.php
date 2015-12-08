<?php
// base admin ctrl
require_once 'base_admin_controller.php';

class AdminSoldesController extends BaseAdminController
{

    /**
     * @var array
     */
    public $default_columns = array(
            'id', 
            'client_number'=>array('label' => 'Numéro client'),
            'quota', 
            'points'
    );
    
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