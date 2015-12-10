<?php
/**
 *
 * This file is part of project 
 *
 * File name : admin_questions_controller.php
 * Project   : wp_cridon
 *
 * @author Etech
 * @contributor Fabrice MILA
 *
 */

// base admin ctrl
require_once 'base_admin_controller.php';

class AdminQuestionsController extends BaseAdminController {
    
    /**
     *
     * @var array
     */
    var $default_searchable_fields = array(
        'srenum'
    );
    var $default_columns = array(
        'id', 
        'srenum',
        'notaire' => array( 'label' => 'Notaire','value_method' => 'notaire_link'),
        'Documents' => array('value_method' => 'question_download_link'),
        'Suite/complément' => array('value_method' => 'other_download_link')
    );
    
    private $documentModel;
    
    public function __construct() {
        $this->documentModel = mvc_model('Document');
        parent::__construct();
    }
    
    public function index() {
        $this->init_default_columns();
        $this->process_params_for_search();
        $collection = $this->model->paginate($this->params);
        $this->set('objects', $collection['objects']);
        $this->set_pagination($collection);
        //Load custom helper
        $this->load_helper('AdminCustom');
    }
    
    /**
     * Get link of Notaire
     * @param object $object
     * @return string
     */
    public function notaire_link($object){
        if (empty($object->notaire)) {
            $this->load_model('Notaire');
            $object->notaire = $this->Notaire->find_one_by_client_number($object->client_number);
        }
        $aOptionList = array(
            '__name'    => array('last_name','first_name')
        );
        $this->prepareData($aOptionList, $object->notaire);
        return empty($object->notaire) ? null : HtmlHelper::admin_object_link($object->notaire, array('action' => 'edit'));
    }
    /**
     * Link of documents
     * 
     * @param object $object
     * @return string
     */
    public function question_download_link($object)
    {   
        $this->load_model('Document');
        $options = array(
            'conditions' => array(
                'id_externe' => $object->id,
                'type' => 'question',
                'OR' => array('label'=>'PJ','label '=>'question/reponse')
            )
        );
        //SQL Généré
        //SELECT `Document`.* FROM `cri_document` `Document` WHERE Document.id_externe = "60103" AND Document.type = "question" AND (Document.label = "PJ" OR Document.label = "question/reponse") 
        $documents = $this->Document->find( $options );
        return $this->getDocumentsLink($documents);
    }
    /**
     * Link of "suite/complément" documents
     * @param object $object
     * @return string
     */
    public function other_download_link($object)
    {      
        $this->load_model('Document');
        $options = array(
            'conditions' => array(
                'id_externe' => $object->id,
                'type' => 'question',
                'label <> ' => 'question/reponse',
                'label <>' => 'PJ'
            )
        );
        //SQL généré
        //SELECT `Document`.* FROM `cri_document` `Document` WHERE Document.id_externe = "60103" AND Document.type = "question" AND Document.label <> "question/reponse" AND Document.label <> "PJ" 
        //Find "suite/complément"
        $documents = $this->Document->find( $options );
        return $this->getDocumentsLink($documents);
    }
    public function add()
    {
        $this->setCompetences();
        $this->setSupports();
        $this->setAffectations();
        $this->create_or_save();
    }

    public function edit()
    {
        $this->set_object();
        $this->setCompetences();
        $this->setSupports();
        $this->setAffectations();
        $this->create_or_save();
    }

    private function setCompetences()
    {
        $this->load_model('Competence');
        $aCompetence = $this->Competence->find(array('id','label','code_matiere'));
        $this->set('aCompetence', $aCompetence);
    }
    private function setSupports()
    {
        $this->load_model('Support');
        $aSupport = $this->Support->find(array('id','label'));
        $this->set('aSupport', $aSupport);
    }
    private function setAffectations()
    {
        $this->load_model('Affectation');
        $aAffectation = $this->Support->find(array('id','label'));
        $this->set('aAffectation', $aAffectation);
    }
    
    /**
     * Generate link of documents
     * 
     * @param mixed $documents
     * @return string
     */
    private function getDocumentsLink( $documents ){
        if( empty( $documents ) ){
            return null;
        }
        $links = array();
        foreach( $documents as $document ){
            $links[] = '<a href="'.$this->documentModel->generatePublicUrl($document->id).'" title="Télécharger" target="_blank"><span class="dashicons dashicons-download"></span></a>';
        }
        return implode('|',$links);
    }
    
    /**
     * Get attributes from list
     * 
     * @param mixed $aOptionList
     * @param object $aData
     */
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
                if( is_array( $sVal ) ){
                    $val = '';
                    foreach( $sVal as $v ){
                        $val .= $aData->$v.' ';
                    }
                }else{
                    $val = $aData->$sVal;
                }
                $aData->$sKey = $val;
            }
        }
    }
}

?>