<?php

/**
 * Class AdminCompetencesController
 *
 * @author Etech
 * @contributor Joeli
 * @version 1.0
 */

// base admin ctrl
require_once 'base_admin_controller.php';

class AdminCompetencesController extends BaseAdminController
{
    var $default_searchable_fields = array(
        'id', 
        'label'
    );
    public $default_columns = array(
        'id',
        'label'         => array('label' => 'Libellé'),
        'short_label'   => array('label' => 'Libellé court'),
        'matiere'       => array('label' => 'Matière','value_method' => 'matiere_edit_link')
    );
    
    public function index() {
        $this->init_default_columns();
        $this->process_params_for_search();
        $this->params['order'] = 'label ASC';
        $collection = $this->model->paginate($this->params);
        $this->set('objects', $collection['objects']);
        $this->set_pagination($collection);
        //Load custom helper
        $this->load_helper('AdminCustom');
    }
    
    public function add()
    {
        $this->setMatieres();
        $this->create_or_save();
    }

    public function edit()
    {
        $this->set_object();
        $this->setMatieres();
        $this->create_or_save();
    }

    private function setMatieres()
    {
        $this->load_model('Matiere');
        $aMatiere = $this->Matiere->find(array('selects' => array('id', 'code', 'label')));

        // set __id and __name value to the option dropdown selects
        $aOptionList = array(
            '__id'      => 'code',
            '__name'    => 'label'
        );
        $this->prepareData($aOptionList, $aMatiere);

        $this->set('aMatiere', $aMatiere);
    }

    private function prepareData($aOptionList, $aData)
    {
        if (is_array($aData) && count($aData) > 0) {
            foreach ($aData as $oData) {
                foreach ($aOptionList as $sKey => $sVal) {
                    $oData->$sKey = $oData->$sVal;
                }
            }
        } elseif(is_object($aData)) {
	        foreach ($aOptionList as $sKey => $sVal) {
		        $aData->$sKey = $aData->$sVal;
	        }
        }
    }

	public function matiere_edit_link($object)
	{
		if (empty($object->matiere)) {
			$this->load_model('Matiere');
			$object->matiere = $this->Matiere->find_one_by_code($object->code_matiere);
		}
		$aOptionList = array(
			'__name'    => 'label'
		);
		$this->prepareData($aOptionList, $object->matiere);
		return empty($object->matiere) ? null : HtmlHelper::admin_object_link($object->matiere, array('action' => 'edit'));
	}
}