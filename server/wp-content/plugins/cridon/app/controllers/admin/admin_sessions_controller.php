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

class AdminSessionsController extends BaseAdminController
{
    /**
     * Default searchable field
     * @var array
     */
    var $default_searchable_fields = array(
        'id',
        'date',
    );

    const SESSION_COMPLETE_MESSAGE = 'Session indiquée comme étant complète';

    /**
     * Default columns list
     * @var array
     */
    var $default_columns = array(
        'id',
        'date' => array(
            'label' => 'Date de la session',
            'value_method' => 'sessionDate'
        ),
        'timetable' => array(
            'label'=>'Informations horaires'
        ),
        'formation' => array(
            'label' => 'Formation',
            'value_method' => 'formationLink'
        ),
        'organisme' => array(
            'label' => 'Organisme',
            'value_method' => 'organismeLabel'
        )
    );

    public function index() {
        $this->init_default_columns();
        $this->process_params_for_search();
        $this->params['order'] = 'date DESC';
        $collection = $this->model->paginate($this->params);
        $this->set('objects', $collection['objects']);
        $this->set_pagination($collection);
        //Load custom helper
        $this->load_helper('AdminSession');
    }

    public function edit(){
        $this->params['data']['is_full'] = 1;
        $this->params['data']['id'] = $this->params['id'];
        if ($this->model->save($this->params['data'])) {
            $this->flash('notice', self::SESSION_COMPLETE_MESSAGE);
            $url = MvcRouter::admin_url(array('controller' => $this->name, 'action' => 'index'));
            $this->redirect($url);
        } else {
            $this->flash('error', $this->model->validation_error_html);
        }
    }

    public function formationLink($object){
        if (empty($object->formation)) {
            $this->load_model('Formation');
            $object->formation = $this->Formation->find_one_by_id($object->id_formation);
        }

        $controllerFormations = new AdminFormationsController();
        return empty($object->formation) ? null : $controllerFormations->post_edit_link($object->formation);
    }

    public function sessionDate($object){
        $return = strftime('%d %B %G',strtotime($object->date));
        if ($object->is_full) {
            $return = '<span style="color: red;">Complet - ' . $return . '</span>';
        }
        return $return;
    }

    public function organismeLabel($object){
        if (empty($object->organisme)) {
            $this->load_model('Entite');
            $object->organisme = $this->Entite->find_one_by_id($object->id_organisme);
        }

        return empty($object->organisme) ? null : $object->organisme->office_name;
    }


}
