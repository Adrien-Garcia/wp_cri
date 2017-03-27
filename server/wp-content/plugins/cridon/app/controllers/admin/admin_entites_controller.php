<?php

/**
 * Class AdminEntitesController
 *
 */
// base admin ctrl
require_once 'base_admin_controller.php';

class AdminEntitesController extends BaseAdminController
{
    /**
     * @var array
     */
    var $default_searchable_fields = array(
        'office_name',
        'crpcen',
        'office_email_adress_1',
        'office_email_adress_2',
        'office_email_adress_3'
    );
    /**
     * @var array
     */
    public $default_columns = array(
        'office_name'  => array( 'label' => 'Nom de l\'étude' ),
        'crpcen' => array( 'label' => 'CRPCEN' ),
        'office_email_adress_1' => array( 'label' => 'Email de l\'étude' ),
        'subscription_level' => array( 'label' => 'Niveau cridonline' ),
        'code_promo_offre_choc' => array( 'label' => 'Offre promo choc' ),
        'code_promo_offre_privilege' => array( 'label' => 'Offre promo privilège' )
    );

    public function index() {
        $this->init_default_columns();
        $this->process_params_for_search();
        $params = $this->params;
        $params['joins'] = array();
        $collection = $this->model->paginate($params);
        $this->set('objects', $collection['objects']);
        $this->set_pagination($collection);
        //Load custom helper
        $this->load_helper('AdminView');
    }

    public function edit() {
        $this->verify_id_param();
        $this->create_or_save();
        $this->set_object();
    }
}
