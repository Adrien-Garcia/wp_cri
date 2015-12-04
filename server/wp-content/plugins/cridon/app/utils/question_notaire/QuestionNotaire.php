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

class QuestionNotaire{
    
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
    
    public $user;
    
    public function __construct() {
        //Set entity manager
        $this->entityManager = new EntityManager();
        $this->setEntities();
        $this->params = self::escapeParams($_REQUEST);
        if ( CriIsNotaire() ) {//if logged
            $this->user = CriNotaireData();//get Notaire
        }
    }
    
    /**
     * Intialize entities
     */
    protected function setEntities(){
        foreach ( $this->entities as $entity ){
            //Ajouter les modèles à utiliser dans le traitement
            $this->entityManager->addEntity( $entity );
        }
        //Add entities in registry
        //créer ces modèles et les associès au registre pour pouvoir les associés après s'ils sont présents dans un requête SELECT
        $this->entityManager->create();
    }
    
    /**
     * Clean URL for $_REQUEST
     * @return string
     */
    protected function getUrl(){
        $url = $_SERVER['REQUEST_URI'];
        $regex = '/(\?[a-zA-Z=0-9]+|&[a-zA-Z=0-9]+)/';
        return preg_replace($regex, '', $url);
    }
    /**
     * Setup pagination
     * 
     * @param mixed $collection
     */
    public function setPagination($collection) {
        $params = $this->params;
        if( isset( $params['page'] ) ){
            unset($params['page']);            
        }
        if( isset( $params['conditions'] ) ){
            unset($params['conditions']);
        } 
        $url = home_url().$this->getUrl();
        $this->pagination = array(
            'base' => $url.'%_%',
            'format' => '?page=%#%',
            'total' => $collection['total_pages'],
            'current' => $collection['page'],
            'add_args' => $params
        );
        $this->cleanWpQuery();
    }
    
    /**
     * Clean global var wp_query
     * @global \WP_Query $wp_query
     */
    protected function cleanWpQuery() {
        global $wp_query;
        $wp_query->is_single = false;
        $wp_query->is_page = false;
        $wp_query->queried_object = null;
        $wp_query->is_home = false;
    }
    
    /**
     * Clean data in URL
     * @param array $params
     * @return array
     */
    public static function escapeParams($params) {
        if (is_array($params)) {
            foreach ($params as $key => $value) {
                if (is_string($value)) {
                    $params[$key] = stripslashes($value);
                } else if (is_array($value)) {
                    $params[$key] = self::escapeParams($value);
                }
            }
        }
        return $params;
    }
    /**
     * Get pagination in front
     * 
     * @return string
     */
    public function getPagination(){
        if( empty($this->pagination ) ){
            return '';
        }
        return paginate_links($this->pagination);
    }
    
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
     * Gérer les options pour la requête
     * 
     * @param type $treated
     * @return array
     */
    protected function generateOptionsQueries( $treated ){
        global $wpdb;
        $condTreated = (!is_array($treated)) ? 'Q.treated = '.$treated : 'Q.treated IN ('.implode(',',$treated).')';
        //Requête principale
        //Au niveau du SELECT nous avons les noms des modèles mais ils doivent être aussi utilisés comme alias aussi
        //[LIMIT] sert à inserer le limit si nous avons une pagination sinon il sera remplacer par un vide('')
        $sql = '
            SELECT Document,Question,Support,Matiere,Competence,Affectation,Notaire
            FROM (SELECT Q.* 
                    FROM '.$wpdb->prefix.'question AS Q
                    JOIN '.$wpdb->prefix.'notaire AS N ON Q.client_number = N.client_number
                    JOIN '.$wpdb->prefix.'etude AS E ON E.crpcen = N.crpcen 
                    WHERE '.$condTreated.' AND E.crpcen = "'.$this->user->crpcen.'" 
                    ORDER BY Q.creation_date DESC 
                    [LIMIT]
                 ) AS Question
            LEFT JOIN '.$wpdb->prefix.'document AS Document ON (Document.id_externe = Question.id AND Document.type = "question" ) 
            LEFT JOIN '.$wpdb->prefix.'affectation AS Affectation ON Affectation.id = Question.id_affectation 
            LEFT JOIN '.$wpdb->prefix.'support AS Support ON Support.id = Question.id_support 
            LEFT JOIN '.$wpdb->prefix.'competence AS Competence ON Competence.id = Question.id_competence_1 
            LEFT JOIN '.$wpdb->prefix.'matiere AS Matiere ON Matiere.code = Competence.code_matiere 
            JOIN '.$wpdb->prefix.'notaire AS Notaire ON Notaire.client_number = Question.client_number
                ';
        //Requête utilisée pour le total des éléments pour la pagination
        $sqlCount ='
            SELECT COUNT(*) AS count 
            FROM (SELECT Q.* 
                    FROM '.$wpdb->prefix.'question AS Q
                    JOIN '.$wpdb->prefix.'notaire AS N ON Q.client_number = N.client_number
                    JOIN '.$wpdb->prefix.'etude AS E ON E.crpcen = N.crpcen 
                    WHERE '.$condTreated.' AND E.crpcen = "'.$this->user->crpcen.'" 
                    ORDER BY Q.creation_date DESC 
                 ) AS Question
            LEFT JOIN '.$wpdb->prefix.'affectation AS Affectation ON Affectation.id = Question.id_affectation 
            LEFT JOIN '.$wpdb->prefix.'support AS Support ON Support.id = Question.id_support 
            LEFT JOIN '.$wpdb->prefix.'competence AS Competence ON Competence.id = Question.id_competence_1 
            LEFT JOIN '.$wpdb->prefix.'matiere AS Matiere ON Matiere.code = Competence.code_matiere 
            JOIN '.$wpdb->prefix.'notaire AS Notaire ON Notaire.client_number = Question.client_number
                '; 
        $options = array(
            'query' => $sql,
            'query_count' => $sqlCount
        );
        return $options;
    }
}
