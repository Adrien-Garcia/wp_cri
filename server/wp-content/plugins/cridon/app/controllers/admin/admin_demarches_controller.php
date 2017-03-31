<?php
/*
 * This file is part of the JETPULP wp_cridon project.
 *
 * Copyright (C) JETPULP
 */
require_once 'base_admin_controller.php';

class AdminDemarchesController extends BaseAdminController
{
    /**
     * Search join
     * @var array
     */
    var $default_search_joins = array('Notaire','Session');

    /**
     * Default searchable field
     * @var array
     */
    var $default_searchable_fields = array(
        'date',
        'Notaire.email_adress',
        'Notaire.last_name',
        //'Session.Formation.Post.post_title' TODO
    );

    /**
     * Default columns list
     * @var array
     */
    var $default_columns = array(
        'id',
        'type' => array(
            'label' => 'Type de démarche',
            'value_method' => 'workflowDisplay'
        ),
        'date' => array(
            'label' => 'Date de la démarche',
            'value_method' => 'demarcheDate'
        ),
        'email' => array(
            'label' => 'Adresse e-mail du demandeur',
            'value_method' => 'sendMailLink'
        ),
        'name' => array(
            'label'=>'Nom du demandeur',
            'value_method' => 'notaireDisplayname'
        ),
        'etude' => array(
            'label'=>'CPRCEN',
            'value_method' => 'crpcenDispay'
        ),
        'formation' => array(
            'label' => 'Formation',
            'value_method' => 'formationLink'
        ),
        'organisme' => array(
            'label' => 'Organisme',
            'value_method' => 'organismeDisplay'
        ),
        'date_session' => array(
            'label' => 'Date de la session',
            'value_method' => 'sessionDisplay'
        ),
    );

    public function index() {
        $this->init_default_columns();
        $this->process_params_for_search();
        $this->load_helper('CustomForm');
        if (!empty($this->params['data']) && !empty($this->params['data']['Demarche'])) {
            $filename = $this->exportCsvDemarches($this->params['data']['Demarche']);
            $filename = wp_upload_dir()['baseurl'].'/demarches/'.$filename;
            $this->set('exportUrl', $filename);
        }
        $this->set('exportedFiles',$this->_exportedFiles());
        if (!empty($_GET['option'])) {
            $this->params['conditions'][] = array('type ' => $_GET['option']);
        }
        $this->params['order'] = 'ID DESC';
        $collection = $this->model->paginate($this->params);
        $this->set('objects', $collection['objects']);
        $this->set_pagination($collection);
        //Load custom helper
        $this->load_helper('AdminView');
        // load scripts
        $this->loadScripts();
    }

    public function edit() {
        $this->verify_id_param();
        $this->create_or_save();
        $this->set_object();
        $this->load_helper('CustomForm');
    }

    public function workflowDisplay($object) {
        return Config::$labelWorflowFormation[$object->type];
    }

    public function demarcheDate($object) {
        return strftime('%d %B %G',strtotime($object->date));
    }

    private function loadNotaire(& $object) {
        if (empty($object->notaire)) {
            $this->load_model('Notaire');
            $object->notaire = $this->Notaire->find_one_by_id($object->notaire_id);
        }
    }

    private function loadSession(& $object) {
        if (empty($object->session)) {
            $this->load_model('Session');
            $object->session = $this->Session->find_one_by_id($object->session_id);
        }
    }

    private function loadFormation(& $object) {
        $this->loadSession($object);
        if (empty($object->formation)) {
            if (!empty($object->session->formation)) {
                $object->formation = $object->session->formation;
            } else {
                $this->load_model('Formation');
                $object->formation = !empty($object->id_formation) ? $this->Formation->find_one_by_id($object->id_formation) : $this->Formation->find_one_by_id($object->session->id_formation);
            }
        }
    }

    private function loadOrganisme(& $object) {
        $this->loadSession($object);
        if (empty($object->session->entite)) {
            $this->load_model('Entite');
            $object->session->entite = $this->Entite->find_one_by_id($object->session->id_organisme);
        }
    }

    public function formationLink($object){
        $this->loadFormation($object);
        $controllerFormations = new AdminFormationsController();
        return empty($object->formation) ? null : $controllerFormations->post_edit_link($object->formation);
    }

    public function organismeDisplay($object) {
        $this->loadOrganisme($object);
        return empty($object->session->entite) ? null : $object->session->entite->office_name ;
    }

    public function sessionDisplay($object) {
        $this->loadSession($object);
        return empty($object->session) ? null : $object->session->date;
    }

    public function sendMailLink($object){
        $this->loadNotaire($object);
        return empty($object->notaire) ? null : '<a href="mailto:'.$object->notaire->email_adress.'" title="Contacter par mail" >'.$object->notaire->email_adress.'</a>';
    }

    public function notaireDisplayname($object){
        $this->loadNotaire($object);
        return empty($object->notaire) ? null : $object->notaire->first_name . ' ' . $object->notaire->last_name;
    }

    public function crpcenDispay($object){
        $this->loadNotaire($object);
        return empty($object->notaire) ? null : $object->notaire->crpcen;
    }

    /**
     * Generate csv from POST params
     * @return string
     */
    public function exportCsvDemarches($data)
    {
        $start_date = $data['export_start_date'];
        $end_date = $data['export_end_date'];
        $start_date = date_create_from_format('d-m-Y', $start_date);
        $end_date = date_create_from_format('d-m-Y', $end_date);
        if ($start_date && $end_date) { // Si vrai date
            $start_date = $start_date->format('Y-m-d');
            $end_date = $end_date->format('Y-m-d');
        }
        if ($data['export_complet']) {
            $start_date = $end_date = false;
        }
        $return = $this->_exportCsvDemarches($start_date, $end_date);
        return $return;
    }

    /**
     * export Demarches by dates
     * @param string|bool $start_date
     * @param string|bool $end_date
     *
     * @return string filename
     */
    private function _exportCsvDemarches($start_date = false, $end_date = false) {
        $filename = 'demarches_';
        if (!empty($start_date) && !empty($end_date)) {
            $filename .= str_replace('-', '', $start_date).'_'.str_replace('-', '', $end_date);
        } else {
            $filename .= 'complet';
        }
        $filename .= '.csv';
        $this->model->exportCsvDemarchesToFile(CONST_EXPORT_CSV_DEMARCHE_FILE_PATH . $filename, true, $start_date, $end_date);
        return $filename;
    }

    /**
     * @return array List of csv files
     */
    private function _exportedFiles() {
        $files = array();
        if (file_exists(CONST_EXPORT_CSV_DEMARCHE_FILE_PATH) && is_dir(CONST_EXPORT_CSV_DEMARCHE_FILE_PATH)) {
            if ($dir = opendir(CONST_EXPORT_CSV_DEMARCHE_FILE_PATH)) {
                while (false !== ($file = readdir($dir))) {
                    if ($file != "." && $file != "..") {
                        if (is_readable(CONST_EXPORT_CSV_DEMARCHE_FILE_PATH.DIRECTORY_SEPARATOR.$file) && substr($file, -4, 4) == ".csv") {
                            $f = array(
                                'url'   => wp_upload_dir()['baseurl'].'/demarches/'.$file,
                            );
                            if (substr($file, 0, 10) == "demarches_") {
                                if ($file == "demarches_complet.csv") {
                                    $f['label'] = 'Toutes les démarches';
                                } else {
                                    preg_match('/^demarches_(\d{8})_(\d{8}).csv$/', $file, $matches);
                                    $start_date = date_create_from_format('Ymd' ,$matches[1]);
                                    $end_date = date_create_from_format('Ymd', $matches[2]);
                                    $f['label'] = 'Démarches du <b>'.$start_date->format('d/m/Y').'</b> au <b>'.$end_date->format('d/m/Y').'</b>';
                                }
                            } else {
                                $f['label'] = 'Fichier inconnu';
                            }
                            $f['label'] .= ' <small>('.$file.')</small>';

                            $files[] = $f;
                        }
                    }
                }
            }
        }
        return $files;
    }

    /**
     * Load script
     */
    protected function loadScripts()
    {
        wp_register_style('ui-component-css', plugins_url('cridon/app/public/css/style.css'), false);
        wp_enqueue_style('ui-component-css');

        wp_register_script('formation-js', plugins_url('cridon/app/public/js/bo/filter.js'), array('jquery'));
        wp_enqueue_script('formation-js');

        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-datepicker');

        wp_register_script('datepicker-js', plugins_url('cridon/app/public/js/bo/datepicker.js'), array('jquery') );
        wp_enqueue_script('datepicker-js');
        wp_enqueue_script('jquery-ui-i18n-fr', plugins_url('cridon/app/public/js/jquery.ui.datepicker-fr.js'), array('jquery-ui-datepicker'));
        wp_enqueue_style('jquery-ui-css', plugins_url('cridon/app/public/css/jquery-ui.css'));
    }
}
