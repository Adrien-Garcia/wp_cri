<?php

/**
 *
 * This file is part of project 
 *
 * File name : QuestionNotaire.php
 * Project   : wp_cridon
 *
 * @author Etech
 * @contributor Fabrice MILA
 *
 */

class QuestionNotaire extends SimpleController{
    
    public $entityManager;
    public $pagination;
    public $params = null;
    /**
     * All model necessary for result
     * @var array 
     */
    public $entities = array(
        'Document','Affectation','Competence','Matiere','Notaire','Support','Question'
    );
       
    /**
     * Get questions pending
     * @return mixed
     */
    public function getPending(){
        if( empty( $this->user ) ){
            return null;
        }
        $options = $this->generateOptionsQueries(array(0,1));
        $results = $this->entityManager->getResults($options);
        return $results;
    }
    
    /**
     * Get questions answered
     * 
     * @return mixed
     */
    public function getAnswered(){
        if( empty( $this->user ) ){
            return null;
        }
        $options = $this->generateOptionsQueries(2);
        $options['per_page'] = DEFAULT_QUESTION_PER_PAGE;//set number per page
        $options = array_merge($options, $this->params );
        $collection = $this->entityManager->paginate($options);
        $this->setPagination($collection);
        return $collection['objects'];
    }
       
    /**
     * Append document in result
     * 
     * @param mixed $data
     * @return mixed
     */
    protected function appendDocuments( $data ){
        $newData = array();
        foreach( $data as $v ){
            $documents = $this->getDocuments($v->question->id,'');
            $v->{documents} = $documents;
            $newData[] = $v;
        }
        return $newData;
    }
    /**
     * Get documents for question
     * 
     * @param integer $id
     * @param string|array $label
     * @param string type
     * @return mixed
     */
    public function getDocuments($id, $label = '', $type = 'question'){
        if( empty( $id ) ){
            return null;
        }
        $options = array(
            'select' => array(
                'Document'
            ),
            'from'   => 'Document',
            'conditions' => array(
                'Document.id_externe = '.$id,
                'Document.type = "'.$type.'"'      
            ),
            'order' => 'Document.id ASC',
        );
        if( !empty( $label ) ){
            if( is_array( $label ) ){
                $lab = array();
                foreach( $label as $v ){
                    $lab[] = 'Document.label = "'.$v.'"';
                }
                $opt = '('.implode(' OR ',$lab).')';
            }else{
                $opt = 'Document.label = "'.$label.'"';                
            }
            $options['conditions'][] = $opt;
        }
        return $this->entityManager->getResults($options);
    }
    
    /**
     * Generate options for query
     * 
     * @param integer $treated
     * @return array
     */
    protected function generateOptionsQueries( $treated ){
        $options = array(
            'select' => array(
                'Question','Support','Matiere','Competence','Affectation','Notaire'
            ),
            'from'   => 'Question',
            'joins'  => array(
                array(
                    'type'   => 'LEFT JOIN',
                    'entity' => array( 'Question' => 'id_affectation' ),
                    'on'     => array( 'Affectation' => 'id' )
                ),
                array(
                    'type'   => 'LEFT JOIN',
                    'entity' => array( 'Question' => 'id_support' ),
                    'on'     => array( 'Support' => 'id' )
                ),
                array(
                    'type'   => 'LEFT JOIN',
                    'entity' => array( 'Question' => 'id_competence_1' ),
                    'on'     => array( 'Competence' => 'id' )
                ),
                array(
                    'type'   => 'LEFT JOIN',
                    'entity' => array( 'Competence' => 'code_matiere' ),
                    'on'     => array( 'Matiere' => 'code' )
                ),
                array(
                    'entity' => array( 'Question' => 'client_number' ),
                    'on'     => array( 'Notaire' => 'client_number' )
                )
            ),
            'conditions' => array(
                (!is_array($treated)) ? 'Question.treated = '.$treated : 'Question.treated IN ('.implode(',',$treated).')',
                'Question.client_number = "'.$this->user->client_number.'"'
            ),
            'order' => 'Question.date_modif ASC',
        );
        return $options;
    }
}
