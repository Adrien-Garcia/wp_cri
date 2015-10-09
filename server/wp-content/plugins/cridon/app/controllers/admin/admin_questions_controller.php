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

class AdminQuestionsController extends MvcAdminController {
    
    var $default_columns = array(
        'id', 
        'srenum', 
        'document réponse' => array('value_method' => 'answer_download_link'),
        'document question' => array('value_method' => 'question_download_link')
    );
    public function answer_download_link($object)
    {   
        $this->load_model('Document');
        $options = array(
            'conditions' => array(
                'id_externe' => $object->id,
                'type' => 'reponse'
            )
        );
        $aObject = $this->Document->find( $options );
        return ( empty( $aObject) ) ? null : '<a href="'.$aObject[0]->download_url.'" title="Télécharger" target="_blank"><span class="dashicons dashicons-download"></span></a>';
    }
    public function question_download_link($object)
    {      
        $this->load_model('Document');
        $options = array(
            'conditions' => array(
                'id_externe' => $object->id,
                'type' => 'question'
            )
        );
        $aObject = $this->Document->find( $options );
        return ( empty( $aObject) ) ? null : '<a href="'.$aObject[0]->download_url.'" title="Télécharger" target="_blank"><span class="dashicons dashicons-download"></span></a>';
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
        $this->load_model('Document');
        $options = array(
            'conditions' => array(
                'id_externe' => $this->object->id,
                'type' => 'question'
            )
        );
        $aObjectQuestion = $this->Document->find( $options );
        $this->set('aObjectQuestion', $aObjectQuestion );
        $options = array(
            'conditions' => array(
                'id_externe' => $this->object->id,
                'type' => 'reponse'
            )
        );
        $aObjectAnswer = $this->Document->find( $options );
        $this->set('aObjectAnswer', $aObjectAnswer );
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
}

?>