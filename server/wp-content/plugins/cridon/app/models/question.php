<?php

/**
 * Question Model
 */

class Question extends \App\Override\Model\CridonMvcModel
{
    /**
     * @var string
     */
    const IMPORT_ODBC_OPTION = 'odbc';

    /**
     * Max number of chars in a string for Oracle SQL query.
     * Theorical limit is 4000, but replacing chars (line feed and quotes) will increase number of chars
     * Performing the replacement before could have side effects by cutting the two '' in two seperates strings.
     * @var int
     */
    const ODBC_MAX_CHARS = 3000;

    /**
     * @var string
     */
    const IMPORT_OCI_OPTION = 'oci';

    var $display_field = 'srenum';
    var $table         = '{prefix}question';
    var $includes      = array('Competence','Competences','Support', 'Notaire');
    var $belongs_to    = array(
        'Competence' => array('foreign_key' => 'id_competence_1'),
        'Support' => array('foreign_key' => 'id_support')
    );
    var $has_and_belongs_to_many = array(
        'Competences' => array(
            'foreign_key' => 'id_question',
            'association_foreign_key' => 'id_competence',
            'join_table' => '{prefix}question_competence',
            'fields' => array('id','label','code_matiere')
        )
    );

    /**
     * @var int : item to be imported per group
     */
    const CONST_LIMIT = 10000;

    /**
     * @var mixed
     */
    protected $adapter;

    /**
     * @var int : number total of itemes to be imported
     */
    protected $nbItems = 0;

    /**
     * @var int : query offset
     */
    protected $offset = 0;

    /**
     * @var array : list of existing question on Site
     */
    protected $siteQuestList = array();

    /**
     * @var resource
     */
    protected $results;

    /**
     * @var array : list of question to be deleted
     */
    protected $questListForDelete = array();

    /**
     * @var bool : File import success flag
     */
    protected $importSuccess = false;

    /**
     * Constructor
     */
    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;

        parent::__construct();
    }

    public function importIntoCriQuestion()
    {
        // init flag
        $i = 1;
        // set adapter
        switch (strtolower(CONST_IMPORT_OPTION)) {
            case self::IMPORT_ODBC_OPTION:
                $this->adapter = CridonODBCAdapter::getInstance();
                break;
            case self::IMPORT_OCI_OPTION:
                //if case above did not match, set OCI
                $this->adapter = CridonOCIAdapter::getInstance();
                break;
        }
        $this->setSiteQuestList();
        // nb items
        $this->getNbItems();
        // import action
        $this->importInitialData($i);
    }

    /**
     * Get number of items
     *
     * @return int
     */
    protected function getNbItems()
    {
        try {
            if (!$this->nbItems) {
                // instance of adapter
                $adapter = $this->adapter;
                // query
                // filter by list of supports if necessary
                $sql = 'SELECT COUNT(*) as NB FROM ' . CONST_ODBC_TABLE_QUEST;
                if (is_array(Config::$acceptedSupports) && count(Config::$acceptedSupports) > 0) {
                    $sql .= ' WHERE ' . $adapter::QUEST_YCODESUP . ' IN(' . implode(',', Config::$acceptedSupports) . ')
                    AND '.$adapter::QUEST_NOFAC_TEL. '!= 2';
                }

                // exec query
                $results = $this->adapter->execute($sql)->fetchData();
                $this->nbItems = $results['NB'];
            }
        } catch (\Exception $e) {
            // send email
            reportError(CONST_EMAIL_ERROR_CATCH_EXCEPTION, $e->getMessage(),'Cridon - Erreur comptage en base de donnée');
        }
    }

    /**
     * Import action by groups (10000 items per group)
     *
     * @param int $i
     */
    protected function importInitialData($i)
    {
        try {
            // increase memory limit
            ini_set('memory_limit', '-1');

            // instance of adapter
            $adapter = $this->adapter;

            // set max limit
            $limitMax = intval($this->nbItems / self::CONST_LIMIT) + 1;

            // repeat action until limit max OR the end is reached
            if ($i <= $limitMax) {

                // query
                $mainQuery = 'SELECT * FROM ' . CONST_ODBC_TABLE_QUEST;
                // filter by list of supports if necessary
                if (is_array(Config::$acceptedSupports) && count(Config::$acceptedSupports) > 0) {
                    $mainQuery .= ' WHERE ' . $adapter::QUEST_YCODESUP . ' IN(' . implode(',', Config::$acceptedSupports) . ')
                    AND '.$adapter::QUEST_NOFAC_TEL. '!= 2';
                }
                switch (CONST_DB_TYPE) {
                    case CONST_DB_ORACLE:
                        /*
                         * How to write a LIMIT OFFSET in Oracle SQL
                         SELECT * FROM (
                            SELECT rownum rnum, a.*
                            FROM(
                                SELECT fieldA,fieldB
                                FROM table
                            ) a
                            WHERE rownum <= offset + limit
                        )
                        WHERE rnum >= offset
                         */
                        $oracleLimit = self::CONST_LIMIT + intval($this->offset);
                        $limitQuery = 'SELECT rownum rnum, subquery.* FROM ('.$mainQuery.') subquery WHERE rownum <= '.$oracleLimit;
                        $sql = 'SELECT * FROM ('.$limitQuery.') WHERE rnum >= '.$this->offset;
                        break;
                    case CONST_DB_DEFAULT:
                    default:
                        $sql = $mainQuery.' LIMIT ' . self::CONST_LIMIT . ' OFFSET ' . intval($this->offset);
                        break;
                }
                $this->offset += self::CONST_LIMIT;

                // exec query
                $this->adapter->execute($sql);
                $this->intitSiteQuestData();

                // increments flag
                $i ++;

                // call import action
                $this->importInitialData($i);

            } else {
                // Close Connection
                $this->adapter->closeConnection();
            }

        } catch (\Exception $e) {
            writeLog($e->getMessage(), 'importQuestions.log');
            // send email
            reportError(CONST_EMAIL_ERROR_CATCH_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * List of existing question on Site
     *
     * @return void
     */
    protected function setSiteQuestList($queryOptions = array())
    {
        // get list of existing question
        $sql = "SELECT id, srenum, client_number FROM {$this->table}";
        if (isset($queryOptions['daily'])) {

            $questions = $this->wpdb->get_results($sql);
            // fill list of existing question on site
            // with key id_question : for matching created question on ERP
            // and value (client_number + srenum) : for matching created question by ERP on Site
            foreach ($questions as $question) {
                $this->siteQuestList[$question->id] = $question->client_number . $question->srenum;
            }

        } elseif(isset($queryOptions['weekly']) && $queryOptions['weekly']) {
            // except all questions not yet transmitted to ERP (new questions & 2006-2009)
            $sql .= " WHERE srenum IS NOT NULL AND transmis_erp <> 0 ";
            $questions = $this->wpdb->get_results($sql);
            // fill list of existing question on site with unique key (client_number + srenum)
            foreach ($questions as $question) {
                array_push($this->siteQuestList, $question->client_number . $question->srenum);
            }
        } else {
            $questions = $this->wpdb->get_results($sql);
            // fill list of existing question on site with unique key (client_number + srenum)
            foreach ($questions as $question) {
                array_push($this->siteQuestList, $question->client_number . $question->srenum);
            }
        }
    }

    /**
     * Insert into cri_question
     */
    protected function intitSiteQuestData()
    {
        // get instance of adapter
        $adapter = $this->adapter;

        // init list of values to be inserted
        $insertValues = array();
        // insert options
        $options               = array();
        $options['table']      = 'question';
        $options['attributes'] = 'srenum, client_number, sreccn, id_support, id_competence_1, `resume`, id_affectation, juriste, ';
        $options['attributes'] .= 'affectation_date, wish_date, real_date, yuser, creation_date, date_modif, ';
        $options['attributes'] .= 'hour_modif, transmis_erp, confidential, content';

        while ($data = $adapter->fetchData()) {

            if (isset( $data[$adapter::QUEST_SREBPC] ) && intval($data[$adapter::QUEST_SREBPC]) > 0) { // valid client_number
                // unique key "client_number + num question"
                $uniqueKey = intval($data[$adapter::QUEST_SREBPC]) . $data[$adapter::QUEST_SRENUM];

                if (!in_array($uniqueKey, $this->siteQuestList)) { // quest not found on site
                    // prepare bulk insert
                    $insertValues[] = $this->prepareBulkInsert($data);
                }

            }
        }

        // execute query
        if (count($insertValues) > 0) {
            $queryBulder       = mvc_model('QueryBuilder');
            $options['values'] = implode(', ', $insertValues);
            // bulk insert
            $queryBulder->insertMultiRows($options);
        }
    }
    
    // Alert on issues without documents
    
    /**
     * Alert on issues without documents every 30 minutes.
     * 
     * @return boolean
     */
    public function checkQuestionsWithoutDocuments(){
        $queryBuilder   = mvc_model('QueryBuilder');        
        $db = $queryBuilder->getInstanceMysqli();//get instance mysqli
        $sql = $this->generateQueryEmptyPdf();//get query
        try{
            $datas = $db->query($sql);            
        } catch (\Exception $ex) {
            writeLog($ex,'question_pdf');
        }
        //No result
        if( $datas->num_rows == 0 ){
            return false;
        }
        $nums = array();
        while( $data = $datas->fetch_object() ){
            //questions without documents
            $nums[] = $data->srenum;
        }   
        $wp_secretaries = get_users( 'role=contributor' );//secretaries in website
        if( empty( $wp_secretaries ) ){
            $secretaries = Config::$emailNotificationEmptyDocument['secretaries'];//Default
        }else{
            $secretaries = array();
            foreach ( $wp_secretaries as $secr ){
                $secretaries[] = $secr->data->user_email;
            }
        }
        $secretaries = array_unique($secretaries);
        sendNotification(Config::$emailNotificationEmptyDocument['message'], implode(',',$nums),$secretaries);
        return true;
    } 
    
    /**
     * Alert on issues without documents once a day.
     * 
     * @return boolean
     */
    public function checkQuestionsWithoutDocumentsDaily(){
        $queryBuilder   = mvc_model('QueryBuilder');        
        $db = $queryBuilder->getInstanceMysqli();//get instance mysqli
        $sql = $this->generateQueryEmptyPdf(true);//get query
        try{
            $datas = $db->query($sql);            
        } catch (\Exception $ex) {
            writeLog($ex,'question_pdf');
        }
        //No result
        if( $datas->num_rows == 0 ){
            return false;
        }
        $nums = array();
        while( $data = $datas->fetch_object() ){
            //questions without documents
            $nums[] = $data->srenum;
        }   
        $wp_secretaries = get_users( 'role=contributor' );//secretaries in website
        if( empty( $wp_secretaries ) ){
            $secretaries = Config::$emailNotificationEmptyDocument['secretaries'];//Default
        }else{
            $secretaries = array();
            foreach ( $wp_secretaries as $secr ){
                $secretaries[] = $secr->data->user_email;
            }
        }
        $wp_administrators = get_users( 'role=admincridon' );//administrators in website
        if( empty( $wp_administrators ) ){
            $administrators = Config::$emailNotificationEmptyDocument['administrators'];//Default
        }else{
            $administrators = array();
            foreach ( $wp_administrators as $admin ){
                $administrators[] = $admin->data->user_email;
            }
        }
        $mails = array_merge($secretaries,$administrators);
        $mails = array_unique($mails);
        return sendNotification(Config::$emailNotificationEmptyDocument['message'], implode(',',$nums),$mails);
    } 
    
    /**
     * Get query for question without document 
     * 
     * @return string
     */
    protected function generateQueryEmptyPdf( $daily = false ){
        $document = mvc_model('Document');
        $cond = ( !$daily ) ? " AND TIMESTAMPDIFF(MINUTE,CONCAT_WS(' ', q.date_modif, q.hour_modif), NOW()) >= ".CONST_ALERT_MINUTE : "";
        $sql = "
            SELECT q.id,q.srenum
            FROM ".$this->table." q
            LEFT JOIN ".$document->table." d
            ON (d.id_externe = q.id AND d.type = 'question' AND d.label = 'question/reponse')
            WHERE d.id IS NULL
            AND q.confidential = 0
            AND q.date_modif IS NOT NULL
            AND q.hour_modif IS NOT NULL
            ".$cond.
            " AND q.treated = 2
            ORDER BY q.srenum ASC
         ";
        return $sql;
    }
    

    public function uploadDocuments( $post ){
        // init error
        $response = array();
        $response['error'] = array();

        //Not access form, only for Notaire connected
        if( !is_user_logged_in() || !CriIsNotaire() ){
            $response['error'][] = is_user_logged_in() ? 'Vous n\'êtes pas autorisé à effectuer cette action' : 'Veuillez vous re-connecter';
            return $response;
        }
        try {
            // notaire data
            $notaire = CriNotaireData();

            // notaire exist
            if ($notaire->client_number
                && isset($post[CONST_QUESTION_OBJECT_FIELD]) && $post[CONST_QUESTION_OBJECT_FIELD] != ''
                && isset($post[CONST_QUESTION_SUPPORT_FIELD]) && ctype_digit($post[CONST_QUESTION_SUPPORT_FIELD])  && ((int) $post[CONST_QUESTION_SUPPORT_FIELD] > 0)
                && isset($post[CONST_QUESTION_MATIERE_FIELD]) && !empty($post[CONST_QUESTION_MATIERE_FIELD])
                && isset($post[CONST_QUESTION_COMPETENCE_FIELD]) && $post[CONST_QUESTION_COMPETENCE_FIELD] != ''
                && isset($post[CONST_QUESTION_MESSAGE_FIELD]) && $post[CONST_QUESTION_MESSAGE_FIELD] != ''
            ) {
                // prepare data
                $creationDate = date('Y-m-d');
                $post['creation_date'] = $creationDate;
                $question = array(
                    'Question' => array(
                        'client_number' => $notaire->client_number,
                        'sreccn' => $notaire->code_interlocuteur,
                        'resume' => htmlentities($post[CONST_QUESTION_OBJECT_FIELD]),
                        'creation_date' => $creationDate,
                        'id_support' => $post[CONST_QUESTION_SUPPORT_FIELD],// Support
                        'id_competence_1' => $post[CONST_QUESTION_COMPETENCE_FIELD],// Competence
                        'content' => htmlentities($post[CONST_QUESTION_MESSAGE_FIELD])// Message
                    )
                );
                // insert question
                $questionId = $this->create($question);

                // Attached files
                if ($questionId && isset($_FILES[CONST_QUESTION_ATTACHEMENT_FIELD])) {
                    // instance of CriFileUploader
                    $criFileUploader = new CriFileUploader();
                    // set files list
                    $criFileUploader->setFiles($_FILES[CONST_QUESTION_ATTACHEMENT_FIELD]);
                    // set max size from config (@see : const.inc.php)
                    $criFileUploader->setMaxSize(CONST_QUESTION_MAX_FILE_SIZE);
                    // set upload dir
                    $uploadDir = wp_upload_dir();
                    $path = $uploadDir['basedir'] . '/questions/' . date('Ym') . '/';
                    if( !file_exists( $path )) { // not yet directory
                        // crete the directory
                        wp_mkdir_p($path);
                    }
                    $criFileUploader->setUploaddir($path);

                    // validate file size, max upload authorized,...
                    if ($criFileUploader->validate()) {
                        $listDocuments = $criFileUploader->execute();

                        if (is_array($listDocuments) && count($listDocuments) > 0) {
                            foreach ($listDocuments as $document) {
                                // prepare data
                                $documents = array(
                                    'Document' => array(
                                        'file_path'     => '/questions/' . date('Ym') . '/' . $document,
                                        'download_url'  => '/documents/download/' . $questionId,
                                        'date_modified' => date('Y-m-d H:i:s'),
                                        'type'          => 'question',
                                        'id_externe'    => $questionId,
                                        'name'          => $document,
                                        'label'         => 'PJ'
                                    )
                                );

                                // insert
                                $documentId = mvc_model('Document')->create($documents);

                                // update download_url
                                $documents = array(
                                    'Document' => array(
                                        'id'            => $documentId,
                                        'download_url'  => '/documents/download/' . $documentId
                                    )
                                );
                                mvc_model('Document')->save($documents);
                            }
                        }
                    } else {
                        $response['error'][] = sprintf(CONST_QUESTION_FILE_SIZE_ERROR,
                                           (CONST_QUESTION_MAX_FILE_SIZE / 1000000) . 'M');

                        return $response;
                    }
                }
            }else{
                if (!isset($post[CONST_QUESTION_OBJECT_FIELD]) || $post[CONST_QUESTION_OBJECT_FIELD] == '') {
                    $response['error'][] = CONST_EMPTY_OBJECT_ERROR_MSG;
                }
                if (!isset($post[CONST_QUESTION_MESSAGE_FIELD]) || $post[CONST_QUESTION_MESSAGE_FIELD] == '') {
                    $response['error'][] = CONST_EMPTY_MESSAGE_ERROR_MSG;
                }
                if (!isset($post[CONST_QUESTION_SUPPORT_FIELD]) || !ctype_digit($post[CONST_QUESTION_SUPPORT_FIELD]) || intval($post[CONST_QUESTION_SUPPORT_FIELD] <= 0)) {
                    $response['error'][] = CONST_EMPTY_SUPPORT_ERROR_MSG;
                }
                if (!isset($post[CONST_QUESTION_MATIERE_FIELD]) || intval($post[CONST_QUESTION_MATIERE_FIELD] <= 0)) {
                    $response['error'][] = CONST_EMPTY_MATIERE_ERROR_MSG;
                }
                if (!isset($post[CONST_QUESTION_COMPETENCE_FIELD]) || $post[CONST_QUESTION_COMPETENCE_FIELD] == '') {
                    $response['error'][] = CONST_EMPTY_COMPETENCE_ERROR_MSG;
                }
                return $response;
            }

            // response data
            return $this->getResponseData($post);
        } catch(\Exception $e) {
            writeLog( $e,'upload.log' );
            return false;
        }
    }

    /**
     * Get response data
     *
     * @param array $post
     * @return array
     * @throws Exception
     */
    protected function getResponseData($post)
    {
        // response
        $response = array(
            'resume'         => htmlentities($post[CONST_QUESTION_OBJECT_FIELD]), // objet
            'content'        => htmlentities($post[CONST_QUESTION_MESSAGE_FIELD]), // Message
            'matiere'        => '', // Matiere
            'competence'     => '', // Competence
            'support'        => '', // Support
            'dateSoumission' => strftime('%d', strtotime($post['creation_date'])) .' '.strftime('%B', strtotime($post['creation_date'])).' '.strftime('%Y', strtotime($post['creation_date'])) // DateSoumission
        );
        // matiere
        $options  = array(
            'conditions' => "id = {$post[CONST_QUESTION_MATIERE_FIELD]}",
            'limit'      => 1,
        );
        $matieres = mvc_model('QueryBuilder')->findOne('matiere', $options);
        if (is_object($matieres) && !empty($matieres)) {
            $response['matiere'] = $matieres;
        }
        // competence
        $options     = array(
            'fields'     => 'label',
            'conditions' => "id = {$post[CONST_QUESTION_COMPETENCE_FIELD]}",
            'limit'      => 1,
        );
        $competences = mvc_model('QueryBuilder')->findOne('competence', $options);
        if (is_object($competences) && !empty($competences->label)) {
            $response['competence'] = $competences->label;
        }
        // support
        $options  = array(
            'conditions' => "id = {$post[CONST_QUESTION_SUPPORT_FIELD]}",
            'limit'      => 1,
        );
        $response['support'] = mvc_model('QueryBuilder')->findOne('support', $options);

        return $response;
    }

    /**
     * Prepare bulk query
     *
     * @param array $data
     * @return string
     */
    protected function prepareBulkInsert($data = array())
    {
        $adapter = $this->adapter;

        // convert date to mysql format
        $affectationDate = ''; // accepted date format if not set
        if (isset($data[$adapter::QUEST_SREDATASS])) {
            $date = $data[$adapter::QUEST_SREDATASS];
            if (preg_match("/^(\d+)\/(\d+)\/(\d+)$/", $date)) {
                $dateTime        = date_create_from_format('d/m/Y', $date);
                $affectationDate = $dateTime->format('Y-m-d');
            } elseif (preg_match("/^(\d+)-([A-Z]{1,4})-(\d+)$/", $date)) {
                $affectationDate = date('Y-m-d', strtotime($date));
            }
        }
        $wishDate = ''; // accepted date format if not set
        if (isset($data[$adapter::QUEST_YRESSOUH])) {
            $date = $data[$adapter::QUEST_YRESSOUH];
            if (preg_match("/^(\d+)\/(\d+)\/(\d+)$/", $date)) {
                $dateTime = date_create_from_format('d/m/Y', $date);
                $wishDate = $dateTime->format('Y-m-d');
            } elseif (preg_match("/^(\d+)-([A-Z]{1,4})-(\d+)$/", $date)) {
                $wishDate = date('Y-m-d', strtotime($date));
            }
        }
        $realDate = ''; // accepted date format if not set
        if (isset($data[$adapter::QUEST_SRERESDAT])) {
            $date = $data[$adapter::QUEST_SRERESDAT];
            if (preg_match("/^(\d+)\/(\d+)\/(\d+)$/", $date)) {
                $dateTime = date_create_from_format('d/m/Y', $date);
                $realDate = $dateTime->format('Y-m-d');
            } elseif (preg_match("/^(\d+)-([A-Z]{1,4})-(\d+)$/", $date)) {
                $realDate = date('Y-m-d', strtotime($date));
            }
        }
        $updatedDate = ''; // accepted date format if not set
        if (isset($data[$adapter::QUEST_UPDDAT])) {
            $date = $data[$adapter::QUEST_UPDDAT];
            if (preg_match("/^(\d+)\/(\d+)\/(\d+)$/", $date)) {
                $dateTime    = date_create_from_format('d/m/Y', $date);
                $updatedDate = $dateTime->format('Y-m-d');
            } elseif (preg_match("/^(\d+)-([A-Z]{1,4})-(\d+)$/", $date)) {
                $updatedDate = date('Y-m-d', strtotime($date));
            }
        }
        $updatedHour = '';
        if (isset($data[$adapter::QUEST_ZUPDHOU])) {
            $hour = trim($data[$adapter::QUEST_ZUPDHOU]);
            if (!empty($hour)) {
                if (count(explode(':', $hour)) > 1) {
                    $updatedHour = $hour;
                }
            }
        }

        // confidential : conversion du couple 1,2 vers un booleen 0,1
        $confidential = 0;
        if (isset($data[$adapter::QUEST_ZANOAMITEL]) && intval($data[$adapter::QUEST_ZANOAMITEL]) == 2) {
            $confidential = 1;
        }
        // prepare bulk insert query
        $value = "(";

        $value .= "'" . (isset($data[$adapter::QUEST_SRENUM]) ? esc_sql($data[$adapter::QUEST_SRENUM]) : '') . "', "; // srenum
        $value .= "'" . (isset($data[$adapter::QUEST_SREBPC]) ? esc_sql($data[$adapter::QUEST_SREBPC]) : '') . "', "; // client_number
        $value .= "'" . (isset($data[$adapter::QUEST_SRECCN]) ? esc_sql($data[$adapter::QUEST_SRECCN]) : '') . "', "; // sreccn
        $value .= "'" . (isset($data[$adapter::QUEST_YCODESUP]) ? esc_sql($data[$adapter::QUEST_YCODESUP]) : '') . "', "; // id_support
        $value .= "'" . (isset($data[$adapter::QUEST_ZCOMPETENC]) ? esc_sql($data[$adapter::QUEST_ZCOMPETENC]) : '') . "', "; // id_competence_1
        $value .= "'" . (isset($data[$adapter::QUEST_YRESUME]) ? esc_sql($data[$adapter::QUEST_YRESUME]) : '') . "', "; // resume
        $value .= "'" . (isset($data[$adapter::QUEST_YSREASS]) ? intval($data[$adapter::QUEST_YSREASS]) : 0) . "', "; // id_affectation
        $value .= "'" . (isset($data[$adapter::QUEST_SREDET]) ? esc_sql($data[$adapter::QUEST_SREDET]) : '') . "', "; // juriste
        $value .= empty($affectationDate) ? 'NULL, ' : "'" . $affectationDate . "', "; // affectation_date
        $value .= empty($wishDate) ? 'NULL, ' : "'" . $wishDate . "', "; // wish_date
        $value .= empty($realDate) ? 'NULL, ' : "'" . $realDate . "', "; // real_date
        $value .= "'" . (isset($data[$adapter::QUEST_YUSER]) ? esc_sql($data[$adapter::QUEST_YUSER]) : '') . "', "; // yuser
        $value .= "'" . (($updatedDate != '') ? $updatedDate : date('Y-m-d')) . "', "; // creation_date
        $value .= empty($updatedDate) ? 'NULL, ' : "'" . $updatedDate . "', "; // date_modif
        $value .= empty($updatedHour) ? 'NULL, ' : "'" . $updatedHour . "', "; // hour_modif
        $value .= "'" . CONST_QUEST_TRANSMIS_ERP . "', "; // transmis_erp
        $value .= "'" . $confidential . "', "; // confidential
        $value .= "''"; // content

        $value .= ")";

        return $value;
    }

    /**
     * Daily update
     *
     * @param $force bool : Force to update all questions ? (defaut false)
     *
     * @return int : code status
     */
    public function cronUpdate($force = false)
    {
        try {
            //get date for update
            $dateOptionUpdate = date('Y-m-d H:i:s');
            // enable gbcollector
            if (function_exists('gc_enable')) {
                gc_enable();
            }

            // set adapter
            switch (strtolower(CONST_IMPORT_OPTION)) {//wrong test, this does not depend on the connector but on the used DB type
                case self::IMPORT_ODBC_OPTION:
                    $this->adapter = $adapter = CridonODBCAdapter::getInstance();
                    break;
                case self::IMPORT_OCI_OPTION:
                default :
                    $this->adapter = $adapter = CridonOCIAdapter::getInstance();
                    break;
            }

            $queryOptions          = array();
            $queryOptions['daily'] = true;

            $this->setSiteQuestList($queryOptions);

            // insert options
            $options               = array();
            $options['table']      = 'question';
            $options['attributes'] = 'srenum, client_number, sreccn, id_support, id_competence_1, `resume`, id_affectation, juriste, ';
            $options['attributes'] .= 'affectation_date, wish_date, real_date, yuser, creation_date, date_modif, ';
            $options['attributes'] .= 'hour_modif, transmis_erp, confidential, content';
            // insert values
            $insertValues = array();

            // update values
            $updateValues = array();

            $mainQuery = 'SELECT * FROM ' . CONST_ODBC_TABLE_QUEST . ' WHERE ';

            if (!$force) {
                // get last cron date if is set or server datetime
                $lastDateUpdate          = get_option('cronquestionupdate');
                if (empty($lastDateUpdate)) {
                    $lastUpQuestion = $this->find_one(array(
                            'conditions' => array(
                                'transmis_erp' => CONST_QUEST_TRANSMIS_ERP, //avoid considering those coming from website
                            ),
                            'order' => 'Question.date_modif DESC, Question.hour_modif DESC',
                        )
                    );
                    $lastDateUpdate = $lastUpQuestion->date_modif . ' ' . $lastUpQuestion->hour_modif;
                }
                $date                  = new DateTime($lastDateUpdate);
                $dateModif             = $date->format('Y-m-d');
                $hourModif             = $date->format('H:i:s');

                switch (strtolower(CONST_IMPORT_OPTION)) {//wrong test, this does not depend on the connector but on the used DB type
                    case self::IMPORT_ODBC_OPTION:
                        $mainQuery .= "TIMESTAMPDIFF(MINUTE, '{$dateModif} {$hourModif}', CONCAT_WS(' ', STR_TO_DATE(" . $adapter::QUEST_UPDDAT . ", '%d/%m/%Y'), " . $adapter::QUEST_ZUPDHOU . ")) > 0 AND ";
                        $mainQuery .= " " . $adapter::QUEST_ZUPDHOU . " IS NOT NULL
                        AND " . $adapter::QUEST_ZUPDHOU . " != '' ";
                        break;
                    case self::IMPORT_OCI_OPTION:
                    default:
                        $dateModif = explode('-', $dateModif);
                        $dateModif = $dateModif[2].'/'.$dateModif[1].'/'.$dateModif[0];
                        $mainQuery .= " (
                    ( ". $adapter::QUEST_UPDDAT . " = TO_DATE('". $dateModif . "', 'DD/MM/YYYY')
                        AND (
                            ( " . $adapter::QUEST_ZUPDHOU . " <> ' '
                                AND TO_DATE(". $adapter::QUEST_ZUPDHOU . ", ' hh24:mi:ss') >= TO_DATE('". $hourModif . "', ' hh24:mi:ss')
                            )
                            OR " . $adapter::QUEST_ZUPDHOU . " = ' '
                        )
                    )
                    OR ". $adapter::QUEST_UPDDAT . " > TO_DATE('". $dateModif . "', 'DD/MM/YYYY')
                )";
                        break;
                }
                if (is_array(Config::$acceptedSupports) && count(Config::$acceptedSupports) > 0) {
                    $mainQuery .= ' AND ';
                }
            }

            // filter by list of supports if necessary
            if (is_array(Config::$acceptedSupports) && count(Config::$acceptedSupports) > 0) {
                $mainQuery .= $adapter::QUEST_YCODESUP . ' IN(' . implode(',',
                        Config::$acceptedSupports) . ')
                    AND '.$adapter::QUEST_NOFAC_TEL. '!= 2';
            }

            // exec query
            $this->adapter->execute($mainQuery);
            while ($data = $this->adapter->fetchData()) {
                if (isset($data[$adapter::QUEST_SREBPC]) && intval($data[$adapter::QUEST_SREBPC]) > 0) { // valid client_number
                    // unique key "client_number + num question"
                    $uniqueKey = intval($data[$adapter::QUEST_SREBPC]) . $data[$adapter::QUEST_SRENUM];

                    if (!in_array($uniqueKey, $this->siteQuestList) // quest ERP unique key condition
                        && !array_key_exists(intval($data[$adapter::QUEST_ZIDQUEST]), $this->siteQuestList) // id quest condition
                    ) { // quest not found on site

                        // prepare bulk insert
                        $insertValues[] = $this->prepareBulkInsert($data);
                    } else { // update bloc
                        // list of field
                        $query = " UPDATE  {$this->table}";
                        $query .= " SET ";

                        // convert date to mysql format
                        $affectationDate = ''; // accepted date format if not set
                        if (isset($data[$adapter::QUEST_SREDATASS])) {
                            $date = $data[$adapter::QUEST_SREDATASS];
                            if (preg_match("/^(\d+)\/(\d+)\/(\d+)$/", $date)) {
                                $dateTime        = date_create_from_format('d/m/Y', $date);
                                $affectationDate = $dateTime->format('Y-m-d');
                            } elseif (preg_match("/^(\d+)-([A-Z]{1,4})-(\d+)$/", $date)) {
                                $affectationDate = date('Y-m-d', strtotime($date));
                            }
                        }
                        $wishDate = ''; // accepted date format if not set
                        if (isset($data[$adapter::QUEST_YRESSOUH])) {
                            $date = $data[$adapter::QUEST_YRESSOUH];
                            if (preg_match("/^(\d+)\/(\d+)\/(\d+)$/", $date)) {
                                $dateTime = date_create_from_format('d/m/Y', $date);
                                $wishDate = $dateTime->format('Y-m-d');
                            } elseif (preg_match("/^(\d+)-([A-Z]{1,4})-(\d+)$/", $date)) {
                                $wishDate = date('Y-m-d', strtotime($date));
                            }
                        }
                        $realDate = ''; // accepted date format if not set
                        if (isset($data[$adapter::QUEST_SRERESDAT])) {
                            $date = $data[$adapter::QUEST_SRERESDAT];
                            if (preg_match("/^(\d+)\/(\d+)\/(\d+)$/", $date)) {
                                $dateTime = date_create_from_format('d/m/Y', $date);
                                $realDate = $dateTime->format('Y-m-d');
                            } elseif (preg_match("/^(\d+)-([A-Z]{1,4})-(\d+)$/", $date)) {
                                $realDate = date('Y-m-d', strtotime($date));
                            }
                        }
                        $updatedDate = ''; // accepted date format if not set
                        if (isset($data[$adapter::QUEST_UPDDAT])) {
                            $date = $data[$adapter::QUEST_UPDDAT];
                            if (preg_match("/^(\d+)\/(\d+)\/(\d+)$/", $date)) {
                                $dateTime    = date_create_from_format('d/m/Y', $date);
                                $updatedDate = $dateTime->format('Y-m-d');
                            } elseif (preg_match("/^(\d+)-([A-Z]{1,4})-(\d+)$/", $date)) {
                                $updatedDate = date('Y-m-d', strtotime($date));
                            }
                        }
                        $updatedHour = '';
                        if (isset($data[$adapter::QUEST_ZUPDHOU])) {
                            $hour = trim($data[$adapter::QUEST_ZUPDHOU]);
                            if (!empty($hour)) {
                                if (count(explode(':', $hour)) > 1) {
                                    $updatedHour = $hour;
                                }
                            }
                        }

                        //Start Query

                        if (isset($data[$adapter::QUEST_SRENUM])) { // srenum
                            $query .= " srenum = '" . esc_sql($data[$adapter::QUEST_SRENUM]) . "', ";
                        }

                        if (isset($data[$adapter::QUEST_SREBPC])) { // client_number
                            $query .= " client_number = '" . esc_sql($data[$adapter::QUEST_SREBPC]) . "', ";
                        }

                        if (isset($data[$adapter::QUEST_SRECCN])) { // sreccn
                            $query .= " sreccn = '" . esc_sql($data[$adapter::QUEST_SRECCN]) . "', ";
                        }

                        if (isset($data[$adapter::QUEST_YCODESUP])) { // id_support
                            $query .= " id_support = '" . esc_sql($data[$adapter::QUEST_YCODESUP]) . "', ";
                        }

                        if (isset($data[$adapter::QUEST_ZCOMPETENC])) { // id_competence_1
                            $query .= " id_competence_1 = '" . esc_sql($data[$adapter::QUEST_ZCOMPETENC]) . "', ";
                        }

                        if (isset($data[$adapter::QUEST_YRESUME])) { // resume
                            $query .= " resume = '" . esc_sql($data[$adapter::QUEST_YRESUME]) . "', ";
                        }

                        if (isset($data[$adapter::QUEST_YSREASS])) { // id_affectation
                            $query .= " id_affectation = '" . intval($data[$adapter::QUEST_YSREASS]) . "', ";
                        }

                        if (isset($data[$adapter::QUEST_SREDET])) { // juriste
                            $query .= " juriste = '" . esc_sql($data[$adapter::QUEST_SREDET]) . "', ";
                        }

                        $query .= " affectation_date = " . (empty($affectationDate) ? "NULL," : "'".$affectationDate."'") . ", ";
                        $query .= " wish_date = " . (empty($wishDate) ? "NULL" : "'".$wishDate."'") . ", ";
                        $query .= " real_date = " . (empty($realDate) ? "NULL" : "'".$realDate."'") . ", ";

                        if (isset($data[$adapter::QUEST_YUSER])) {
                            $query .= " yuser = '" . esc_sql($data[$adapter::QUEST_YUSER]) . "', ";
                        }

                        $query .= " date_modif = " . (empty($updatedDate) ? "NULL" : "'".$updatedDate."'") . ", ";
                        $query .= " hour_modif = " . (empty($updatedHour) ? "NULL" : "'".$updatedHour."'") . ", ";


                        $query .= " transmis_erp = '" . CONST_QUEST_TRANSMIS_ERP . "', "; // transmis_erp

                        /**
                         * "1 : non (il n'y a pas d'anomalie)
                         * 2 : oui (anomalie, afficher sans document PDF, pas de génération d'alerte email pour les secrétaire)"
                         * A transformer en booleen
                         */
                        $confidential = 0;
                        if (isset($data[$adapter::QUEST_ZANOAMITEL]) && intval($data[$adapter::QUEST_ZANOAMITEL]) == 2) {
                            $confidential = '1';
                        }
                        $query .= " confidential = '" . $confidential . "' "; // confidential

                        // conditions
                        if (isset($data[$adapter::QUEST_ZIDQUEST]) && intval($data[$adapter::QUEST_ZIDQUEST]) > 0) { // maj par id question
                            $query .= " WHERE id = '" . intval($data[$adapter::QUEST_ZIDQUEST]) . "';";
                        } else { // maj par client_number et srenum
                            $query .= " WHERE client_number = '" . esc_sql($data[$adapter::QUEST_SREBPC]) . "'";
                            $query .= " AND srenum = '" . esc_sql($data[$adapter::QUEST_SRENUM]) . "';";
                        }

                        // query bloc
                        $updateValues[] = $query;
                    }
                }
                // Send mail if status / support change
                // get questions info if on db
                $question = '';
                if (!empty ($data[$adapter::QUEST_ZIDQUEST]) && intval($data[$adapter::QUEST_ZIDQUEST]) > 0) {
                    $question = mvc_model('Question')->find_by_id(intval($data[$adapter::QUEST_ZIDQUEST]));
                } elseif ( (!empty ($data[$adapter::QUEST_SREBPC])) && ($data[$adapter::QUEST_SREBPC] !== ' ') && (!empty ($data[$adapter::QUEST_SRENUM])) && ($data[$adapter::QUEST_SRENUM] !== ' ' )) {
                    $question = mvc_model('Question')->find_one(array(
                        'conditions' => array(
                            'Question.client_number' => esc_sql($data[$adapter::QUEST_SREBPC]),
                            'Question.srenum' => esc_sql($data[$adapter::QUEST_SRENUM])
                        )
                    ));
                }

                // Récupération du srenum pour l'objet du mail
                if (!empty($data[$adapter::QUEST_SRENUM])) { // srenum
                    $srenum = esc_sql($data[$adapter::QUEST_SRENUM]);
                } elseif (!empty($question->srenum)) {
                    $srenum = $question->srenum;
                } else {
                    $srenum = false;
                }

                $mail = false;
                $creation_date = false;
                $type_question = 0;
                $subject = '';
                // id_affectation change / first time question is !empty to site -> Send mail
                if ( ( !empty($data[$adapter::QUEST_YSREASS]) && isset($question) && !empty($question->id_affectation) && intval($data[$adapter::QUEST_YSREASS]) != $question->id_affectation )
                    || (!empty($question) && empty($question->srenum) )
                    || (!isset($question)) ) {
                    $mail = true;
                    if (intval($data[$adapter::QUEST_YSREASS]) == 1) {
                        if (!empty($question->creation_date)) { // date de création de la question
                            $creation_date = $question->creation_date;
                        }
                        $type_question = 2;
                        $subject = sprintf(Config::$mailSubjectQuestionStatusChange['2'],$srenum);
                    } elseif (intval($data[$adapter::QUEST_YSREASS]) == 2) {
                        $type_question = 4;
                        $subject = sprintf(Config::$mailSubjectQuestionStatusChange['4'],$srenum);
                    } elseif (intval($data[$adapter::QUEST_YSREASS]) == 3) {
                        $type_question = 5;
                        $subject = sprintf(Config::$mailSubjectQuestionStatusChange['5'],$srenum);
                    } elseif (intval($data[$adapter::QUEST_YSREASS]) == 4) {
                        $type_question = 6;
                        $subject = sprintf(Config::$mailSubjectQuestionStatusChange['6'],$srenum);
                    } else {
                        $mail = '';
                    }
                }
                // id_support change -> Send mail
                //TODO Implement new supports after mix produit
                if (isset($data[$adapter::QUEST_YCODESUP]) && isset($question) && !empty($question->id_support)
                    && (!empty(Config::$declassement[$question->id_support])
                    && in_array(intval($data[$adapter::QUEST_YCODESUP]),Config::$declassement[$question->id_support]))
                    && (intval($data[$adapter::QUEST_YCODESUP]) != $question->id_support)
                    && isset($data[$adapter::QUEST_YSREASS])
                    && intval($data[$adapter::QUEST_YSREASS]) == 1)
                {
                    $mail = true;
                    $type_question = 3;
                    $subject = sprintf(Config::$mailSubjectQuestionStatusChange['3'],$srenum);
                }

                if ($mail) {
                    // set mail headers
                    $headers = array('Content-Type: text/html; charset=UTF-8');

                    // Récupération des données

                    if (!empty($data[$adapter::QUEST_YCODESUP]) && $data[$adapter::QUEST_YCODESUP] !== ' ') { // support
                        $support = esc_sql($data[$adapter::QUEST_YCODESUP]);
                    } elseif (!empty($question->id_support)) {
                        $support = $question->id_support;
                    } else {
                        $support = '';
                    }

                    if (!empty($data[$adapter::QUEST_ZCOMPETENC]) && $data[$adapter::QUEST_ZCOMPETENC] !== ' ') { // competence
                        $competence = esc_sql($data[$adapter::QUEST_ZCOMPETENC]);
                    } elseif (!empty($question->id_competence_1) && $question->id_competence_1 !== ' ') {
                        $competence = $question->id_competence_1;
                    } else {
                        $competence = '';
                    }

                    if (!empty($data[$adapter::QUEST_YRESUME]) && $data[$adapter::QUEST_YRESUME] !== ' ') { // resume
                        $resume = esc_sql($data[$adapter::QUEST_YRESUME]);
                    } elseif (!empty($question->resume)) {
                        $resume = $question->resume;
                    } else {
                        $resume = '';
                    }

                    if (!empty($data[$adapter::QUEST_YCONTENT]) && $data[$adapter::QUEST_YCONTENT] !== ' ') { // content
                        $content = esc_sql($data[$adapter::QUEST_YCONTENT]);
                    } elseif (!empty($question->content)) {
                        $content = $question->content;
                    } else {
                        $content = '';
                    }

                    if (!empty($data[$adapter::QUEST_SREDET]) && $data[$adapter::QUEST_SREDET] !== ' ') { // juriste
                        $juriste = esc_sql($data[$adapter::QUEST_SREDET]);
                    } elseif (!empty($question->juriste)) {
                        $juriste = $question->juriste;
                    } else {
                        $juriste = '';
                    }

                    if (!empty($data[$adapter::ZQUEST_YMATIERE_0]) && $data[$adapter::ZQUEST_YMATIERE_0] !== ' ') { // matière
                        $matiere = esc_sql($data[$adapter::ZQUEST_YMATIERE_0]);
                    } elseif (!empty($question->matiere)) {
                        $matiere = $question->matiere;
                    } else {
                        $matiere = '';
                    }

                    if (!empty($data[$adapter::QUEST_SREDATASS]) && $data[$adapter::QUEST_SREDATASS] !== ' ') { //date affectation
                        $affectation_date = $data[$adapter::QUEST_SREDATASS];
                    } elseif (!empty($question->affectation_date)) {
                        $affectation_date = $question->affectation_date;
                    } else {
                        $affectation_date = '';
                    }

                    if (!empty($data[$adapter::QUEST_YRESSOUH]) && $data[$adapter::QUEST_YRESSOUH] !== ' ') { // date de réponse souhaitée
                        $wish_date = $data[$adapter::QUEST_YRESSOUH];
                    } elseif (!empty($question->wish_date)) {
                        $wish_date = $question->wish_date;
                    } else {
                        $wish_date = '';
                    }

                    // Mise en forme des données
                    $date = '';
                    if (!empty($creation_date)) {
                        $newDate  = strftime('%d', strtotime($creation_date));
                        $newDate .= ' '.strftime('%B', strtotime($creation_date));
                        $newDate .= ' '.strftime('%Y', strtotime($creation_date));
                        $creation_date = $newDate;
                        $date = $creation_date;
                    }

                    if (!empty($affectation_date)) {
                        $newDate  = strftime('%d', strtotime($affectation_date));
                        $newDate .= ' '.strftime('%B', strtotime($affectation_date));
                        $newDate .= ' '.strftime('%Y', strtotime($affectation_date));
                        $affectation_date = $newDate;
                        $date = $affectation_date;
                    }

                    if (!empty($wish_date)) {
                        $newDate  = strftime('%d', strtotime($wish_date));
                        $newDate .= ' '.strftime('%B', strtotime($wish_date));
                        $newDate .= ' '.strftime('%Y', strtotime($wish_date));
                        $wish_date = $newDate;
                    }

                    if (!empty($support)) {
                        $supports = mvc_model('Support')->find_by_id($support);
                        if (is_object($supports) && !empty($supports->id)) {
                            $support = $supports;
                            $expertise = CriExpertiseBySupport($support->id);
                        }
                    }

                    if (!empty($juriste)) {
                        $juristes = mvc_model('UserCridon')->find_one(array(
                            'conditions' => array(
                                'UserCridon.id_erp' => $juriste
                            ),
                            'joins' => array(
                                'User'
                            )
                        ));
                        if (is_object($juristes) && !empty ($juristes->user->display_name)) {
                            $juriste = $juristes->user->display_name;
                        }
                    }

                    if (!empty($competence) && $competence !== 0) {
                        $competences = mvc_model('Competence')->find_by_id($competence);
                        if (is_object($competences) && !empty ($competences->label)){
                            $competence = $competences->label;
                        }
                    }

                    if (!empty($matiere)){
                        $matieres = mvc_model('Matiere')->find_one(array(
                            'conditions' => array(
                                'Matiere.code' => $matiere
                            )
                        ));
                        if (is_object($matieres) && !empty ($matieres->label)){
                            $matiere = $matieres;
                        }
                    }

                    // get notaire data
                    if (!empty($data[$adapter::ZQUEST_SREBPC_0])) { // numéro client
                        $client_number = $data[$adapter::ZQUEST_SREBPC_0];
                    } elseif (!empty($question->client_number)) {
                        $client_number = $question->client_number;
                    }

                    if (!empty($data[$adapter::ZQUEST_SRECCN_0])) { // code interlocuteur
                        $sreccn = $data[$adapter::ZQUEST_SRECCN_0];
                    } elseif (!empty($question->sreccn)) {
                        $sreccn = $question->sreccn;
                    }

                    $notaire = '';
                    if (!empty($client_number) && !empty($sreccn) ) {
                        $notaire = mvc_model('Notaire')->find_one(array(
                            'conditions' => array(
                                'Notaire.client_number' => $client_number,
                                'Notaire.code_interlocuteur' => $sreccn
                            ),
                            'joins' => array(
                                'Etude'
                            )
                        ));
                    }

                    $vars = array (
                        'numero_question'  => $srenum,
                        'expertise'        => (empty($expertise) || empty($expertise->label_front)) ? '' : $expertise->label_front,
                        'support'          => (empty($support)   || empty($support->label_front))   ? '' : $support->label_front,
                        'matiere'          => $matiere,
                        'competence'       => $competence,
                        'resume'           => stripslashes($resume),
                        'content'          => stripslashes($content),
                        'juriste'          => $juriste,
                        'creation_date'    => $creation_date,
                        'affectation_date' => $affectation_date,
                        'wish_date'        => $wish_date,
                        'date'             => $date,
                        'type_question'    => $type_question,
                        'notaire'          => $notaire,
                    );

                    $message = CriRenderView('mail_notification_question', $vars, 'custom', false);

                    $env = getenv('ENV');
                    if (empty($env)|| ($env !== 'PROD')) {
                        if ($env === 'PREPROD') {
                            $dest = Config::$notificationAddressPreprod;
                        } else {
                            $dest = Config::$notificationAddressDev;
                        }
                        $email = wp_mail( $dest , $subject, $message, $headers );
                        writeLog("not Prod: " . $email . "\n", "mailog.txt");
                    } else {
                        if (!empty($notaire->email_adress)) {
                            $destinataire = $notaire->email_adress;
                        } elseif (!empty($notaire->etude->office_email_adress_1)){
                            $destinataire = $notaire->etude->office_email_adress_1;
                        } elseif (!empty($notaire->etude->office_email_adress_2)){
                            $destinataire = $notaire->etude->etude->office_email_adress_2;
                        } elseif (!empty($notaire->etude->office_email_adress_3)){
                            $destinataire = $notaire->etude->office_email_adress_3;
                        }
                        if (!empty($destinataire)) {
                            wp_mail($destinataire, $subject, $message, $headers);
                        }
                    }
                }
            }

            // execute query
            if (count($insertValues) > 0) {
                $queryBulder       = mvc_model('QueryBuilder');
                $options['values'] = implode(', ', $insertValues);
                // bulk insert
                $queryBulder->insertMultiRows($options);
                writeLog(count($insertValues). ' nouvelles questions','questioncronUpdate.log');
            }
            if (count($updateValues) > 0) {
                // bulk update
                $i = 0;
                $updateValuesChunk = array_chunk($updateValues, CONST_MAX_SQL_OPERATION);
                foreach ($updateValuesChunk as $uv) {

                    $updtateQuery = implode(' ', $uv);
                    /**
                     * @var $mysqliBuilder mysqli
                     */
                    $mysqliBuilder = mvc_model('QueryBuilder')->getInstanceMysqli();
                    if( $mysqliBuilder->multi_query($updtateQuery) )
                    {
                        do {
                            $mysqliBuilder->next_result();
                            $i++;
                        }
                        while( $mysqliBuilder->more_results() );
                    } else {
                        writeLog('Une erreur inconnue est survenue', 'questioncronUpdate.log');
                    }

                    if (!empty($mysqliBuilder->error)) {
                        // write into logfile
                        writeLog($mysqliBuilder->error . ' IN : ' . $updateValues[$i], 'questioncronUpdate.log');
                    }
                }
                writeLog($i. ' questions maj', 'questioncronUpdate.log');
            }

            if (!empty($lastDateUpdate)) {
                // maj derniere date d'execution
                update_option('cronquestionupdate', $dateOptionUpdate);
            }

            // notification client mobile si question traitee
            // id_affectation = 4
            $this->pushNotification();

            return CONST_STATUS_CODE_OK;
        } catch (\Exception $e) {
            writeLog($e, 'questioncronUpdate.log');

            return CONST_STATUS_CODE_GONE;
        }
    }


    /**
     * Weekly update
     */
    public function weeklyUpdate()
    {
        try {
            // enable gbcollector
            if (function_exists('gc_enable')) {
                gc_enable();
            }
            // set adapter
            switch (strtolower(CONST_IMPORT_OPTION)) {
                case self::IMPORT_ODBC_OPTION:
                    $this->adapter = $adapter = CridonODBCAdapter::getInstance();
                    break;
                case self::IMPORT_OCI_OPTION:
                    //if case above did not match, set OCI
                    $this->adapter = $adapter = CridonOCIAdapter::getInstance();
                    break;
            }

            // site quest list
            $queryOptions['weekly'] = true;
            $this->setSiteQuestList($queryOptions);
            // store question list into dedicated var
            $this->questListForDelete = $this->siteQuestList;

            // nb items
            $this->getNbItems();

            // list of question
            $i = 0;
            $this->setQuestListForDelete($i);

            // delete action
            /**
             * $this->questListForDelete : tableau de couple "client_number + srenum"
             */
            if (count($this->questListForDelete) > 0) {
                // obtenir une chaine sous la forme : '84803274728', '80603274726', '82605774731'
                $conditions = "'" . implode("','", $this->questListForDelete) . "'";
                $query = "DELETE
                          FROM `{$this->table}`
                          WHERE CONCAT(client_number, srenum) IN (" . $conditions . ")";

                // execute query
                $queryBuilder = mvc_model('QueryBuilder')->getInstanceMysqli();
                $queryBuilder->query($query);
                if (!empty($queryBuilder->error)) {
                    throw new Exception($queryBuilder->error);
                }
            }

            return CONST_STATUS_CODE_OK;
        } catch(\Exception $e) {
            writeLog($e, 'questionweeklyupdate.log');

            return CONST_STATUS_CODE_GONE;
        }
    }

    /**
     * Set Quest list for delete
     *
     * @param int $i
     */
    protected function setQuestListForDelete($i)
    {
        try {
            // instance of adapter
            $adapter = $this->adapter;

            // set max limit
            $limitMax = intval($this->nbItems / self::CONST_LIMIT) + 1;

            // repeat action until limit max OR the end is reached
            if ($i <= $limitMax) {

                // query
                $mainQuery = 'SELECT ' . $adapter::QUEST_SREBPC . ', ' . $adapter::QUEST_SRENUM . ' FROM ' . CONST_ODBC_TABLE_QUEST;
                // filter by list of supports if necessary
                if (is_array(Config::$acceptedSupports) && count(Config::$acceptedSupports) > 0) {
                    $mainQuery .= ' WHERE ' . $adapter::QUEST_YCODESUP . ' IN(' . implode(',', Config::$acceptedSupports) . ')
                    AND '.$adapter::QUEST_NOFAC_TEL. '!= 2';
                }
                switch (CONST_DB_TYPE) {
                    case CONST_DB_ORACLE:
                        /*
                         * How to write a LIMIT OFFSET in Oracle SQL
                         SELECT * FROM (
                            SELECT rownum rnum, a.*
                            FROM(
                                SELECT fieldA,fieldB
                                FROM table
                            ) a
                            WHERE rownum <= offset + limit
                        )
                        WHERE rnum >= offset
                         */
                        $oracleLimit = self::CONST_LIMIT + intval($this->offset);
                        $limitQuery = 'SELECT rownum rnum, subquery.* FROM ('.$mainQuery.') subquery WHERE rownum <= '.$oracleLimit;
                        $sql = 'SELECT * FROM ('.$limitQuery.') WHERE rnum >= '.$this->offset;
                        break;
                    case CONST_DB_DEFAULT:
                    default:
                        $sql = $mainQuery.' LIMIT ' . self::CONST_LIMIT . ' OFFSET ' . intval($this->offset);
                        break;
                }
                $this->offset += self::CONST_LIMIT;

                // exec query
                $this->adapter->execute($sql);
                while ($data = $this->adapter->fetchData()) {
                    if (isset($data[$adapter::QUEST_SREBPC]) && intval($data[$adapter::QUEST_SREBPC]) > 0) { // valid client_number
                        // unique key "client_number + num question"
                        $uniqueKey = intval($data[$adapter::QUEST_SREBPC]) . $data[$adapter::QUEST_SRENUM];

                        if (in_array($uniqueKey, $this->questListForDelete)) { // quest found on Site / ERP

                            // remove quest on list
                            $key = array_search($uniqueKey, $this->questListForDelete);
                            if ($key !== false) {
                                unset($this->questListForDelete[$key]);
                            }
                        }
                    }
                }

                // increments flag
                $i++;

                // call import action
                $this->setQuestListForDelete($i);

            } else {
                // Close Connection
                $this->adapter->closeConnection();
            }

        } catch (\Exception $e) {
            // send email
            reportError(CONST_EMAIL_ERROR_CATCH_EXCEPTION, $e->getMessage(),'Cridon - Question - Erreur création liste de suppression');
            writeLog($e, 'questionweeklyupdatesetListDelete.log');
        }
    }

    /**
     * @return array|null|object
     */
    public function exportQuestion()
    {
        try {
            $questions = $this->find(array(
                                         'conditions' => array(
                                             'transmis_erp' => 0
                                         )
                                     )
            );

            // verification nb question à exporter
            if (count($questions) > 0) {
                // set adapter
                switch (strtolower(CONST_IMPORT_OPTION)) {
                    case self::IMPORT_ODBC_OPTION:
                        $this->adapter = CridonODBCAdapter::getInstance();
                        break;
                    case self::IMPORT_OCI_OPTION:
                        //if case above did not match, set OCI
                        $this->adapter = CridonOCIAdapter::getInstance();
                        break;
                }

                // bloc de requette
                $queryBloc = array();
                // adapter instance
                $adapter = $this->adapter;
                // list des id question pour maj cri_question apres transfert
                $qList = array();

                // requete commune
                $query  = " INTO " . CONST_DB_TABLE_QUESTTEMP;
                $query .= " (";
                $query .= $adapter::ZQUEST_ZIDQUEST_0 . ", "; // ZQUEST_ZIDQUEST_0
                $query .= $adapter::ZQUEST_ZTRAITEE_0 . ", "; // ZQUEST_ZTRAITEE_0
                $query .= $adapter::ZQUEST_SREBPC_0 . ", ";   // ZQUEST_SREBPC_0
                $query .= $adapter::ZQUEST_SRECCN_0 . ", ";   // ZQUEST_SRECCN_0
                $query .= $adapter::ZQUEST_YCODESUP_0 . ", ";   // ZQUEST_YCODESUP_0
                $query .= $adapter::ZQUEST_YMATIERE_0 . ", ";   // ZQUEST_YMATIERE_0
                $query .= $adapter::ZQUEST_YMAT_0 . ", ";   // ZQUEST_YMAT_0
                $query .= $adapter::ZQUEST_YMAT_1 . ", ";   // ZQUEST_YMAT_1
                $query .= $adapter::ZQUEST_YMAT_2 . ", ";   // ZQUEST_YMAT_2
                $query .= $adapter::ZQUEST_YMAT_3 . ", ";   // ZQUEST_YMAT_3
                $query .= $adapter::ZQUEST_YMAT_4 . ", ";   // ZQUEST_YMAT_4
                $query .= $adapter::ZQUEST_ZCOMPETENC_0 . ", ";   // ZQUEST_ZCOMPETENC_0
                $query .= $adapter::ZQUEST_ZCOMP_0 . ", ";   // ZQUEST_ZCOMP_0
                $query .= $adapter::ZQUEST_ZCOMP_1 . ", ";   // ZQUEST_ZCOMP_1
                $query .= $adapter::ZQUEST_ZCOMP_2 . ", ";   // ZQUEST_ZCOMP_2
                $query .= $adapter::ZQUEST_ZCOMP_3 . ", ";   // ZQUEST_ZCOMP_3
                $query .= $adapter::ZQUEST_ZCOMP_4 . ", ";   // ZQUEST_ZCOMP_4
                $query .= $adapter::ZQUEST_YRESUME_0 . ", ";   // ZQUEST_YRESUME_0
                $query .= $adapter::ZQUEST_YSREASS_0 . ", ";   // ZQUEST_YSREASS_0
                $query .= $adapter::ZQUEST_CREDAT_0 . "";   // ZQUEST_CREDAT_0
                $query .= ") ";
                $query .= " VALUES ";

                // complement requete selon le type de BDD
                switch (CONST_DB_TYPE) {
                    case CONST_DB_ORACLE:
                        /*
                         * How to write a bulk insert in Oracle SQL
                         INSERT ALL
                          INTO mytable (column1, column2, column_n) VALUES (expr1, expr2, expr_n)
                          INTO mytable (column1, column2, column_n) VALUES (expr1, expr2, expr_n)
                          INTO mytable (column1, column2, column_n) VALUES (expr1, expr2, expr_n)
                        SELECT * FROM dual;
                         */
                        // Add required fields on the Oracle DB
                        $query = substr($query, 0, - strlen(")  VALUES ")).', ';
                        $query .= $adapter::ZQUEST_ZLIENS_0 . ", ";   // ZQUEST_ZLIENS_0
                        $query .= $adapter::ZQUEST_ZLIENS_1 . ", ";   // ZQUEST_ZLIENS_1
                        $query .= $adapter::ZQUEST_ZLIENS_2 . ", ";   // ZQUEST_ZLIENS_2
                        $query .= $adapter::ZQUEST_ZLIENS_3 . ", ";   // ZQUEST_ZLIENS_3
                        $query .= $adapter::ZQUEST_ZLIENS_4 . ", ";   // ZQUEST_ZLIENS_4
                        $query .= $adapter::ZQUEST_ZTXTQUEST_0 . ", ";   // ZQUEST_ZTXTQUEST_0
                        $query .= $adapter::ZQUEST_SRENUM_0 . ", ";   // ZQUEST_SRENUM_0
                        $query .= $adapter::ZQUEST_ZMESERR_0 . ", ";   // ZQUEST_ZMESSERR_0
                        $query .= $adapter::ZQUEST_ZERR_0 . " ";   // ZQUEST_ZERR_0

                        $query .= ")  VALUES ";

                        foreach ($questions as $question) {
                            $documents = mvc_model('Document')->find(array(
                                'conditions' => array(
                                    'Document.id_externe' => $question->id
                                ))
                            );

                            // remplit la liste des questions
                            $qList[] = $question->id;
                            // competence et matiere principale associées
                            $compCode    = 0;
                            $matiereCode = 0;
                            if (isset($question->competence) && is_object($question->competence)) {
                                if ($question->competence->id) {
                                    $compCode = $question->competence->id;
                                }
                                if ($question->competence->code_matiere) {
                                    $matiereCode = $question->competence->code_matiere;
                                }
                            }

                            // competence et maitere secondaire associées
                            $zquest_zcomp_0 = $zquest_zcomp_1 = $zquest_zcomp_2 = $zquest_zcomp_3 = $zquest_zcomp_4 = 0;
                            $zquest_ymat_0  = $zquest_ymat_1 = $zquest_ymat_2 = $zquest_ymat_3 = $zquest_ymat_4 = 0;
                            if (is_array($question->competences) && count($question->competences) > 0) {
                                foreach ($question->competences as $key => $comp) {
                                    $paramComp  = 'zquest_zcomp_' . $key;
                                    $paramMat   = 'zquest_ymat_' . $key;
                                    $$paramComp = $comp->id;
                                    $$paramMat = $comp->code_matiere;
                                }
                            }

                            $content = "' '";
                            $trimQuestion = trim($question->content);
                            if (!empty($trimQuestion)) {
                                $content = trim(html_entity_decode($question->content));
                                if (mb_strlen($content) <= static::ODBC_MAX_CHARS) {
                                    $content = str_replace(array("\r\n","\r","\n","\\r","\\n","\\r\\n"), "'||chr(13)||chr(10)||'", $content); // avoid considering all forms of newline
                                    $content = "'" . str_replace('\\\'', '\'\'', $content) . "'";
                                } else {
                                    $contents = str_split($content, static::ODBC_MAX_CHARS);
                                    //to_clob('...') || to_clob('...')..
                                    array_walk($contents, function(& $chunk) {
                                        $chunk = str_replace(array("\r\n","\r","\n","\\r","\\n","\\r\\n"), "')||chr(13)||chr(10)||to_clob('", $chunk); // also change plain characters
                                        $chunk = "to_clob('" . str_replace('\\\'', '\'\'', $chunk) . "')";
                                    });
                                    $content = implode('||', $contents);
                                }
                            }
                            $value  = $query;
                            $value .= "(";

                            $value .= "'" . $question->id . "', "; // ZQUEST_ZIDQUEST_0
                            $value .= "0, "; // ZQUEST_ZTRAITEE_0
                            $value .= "'" . $question->client_number . "', "; // ZQUEST_SREBPC_0
                            $value .= "'" . $question->sreccn . "', "; // ZQUEST_SRECCN_0
                            $value .= "'" . $question->id_support . "', "; // ZQUEST_YCODESUP_0
                            $value .= "'" . $matiereCode . "', "; // ZQUEST_YMATIERE_0
                            $value .= "'" . $zquest_ymat_0 . "', "; // ZQUEST_YMAT_0
                            $value .= "'" . $zquest_ymat_1 . "', "; // ZQUEST_YMAT_1
                            $value .= "'" . $zquest_ymat_2 . "', "; // ZQUEST_YMAT_2
                            $value .= "'" . $zquest_ymat_3 . "', "; // ZQUEST_YMAT_3
                            $value .= "'" . $zquest_ymat_4 . "', "; // ZQUEST_YMAT_4
                            $value .= "'" . $compCode . "', ";       // ZQUEST_ZCOMPETENC_0
                            $value .= "'" . $zquest_zcomp_0 . "', "; // ZQUEST_ZCOMP_0
                            $value .= "'" . $zquest_zcomp_1 . "', "; // ZQUEST_ZCOMP_1
                            $value .= "'" . $zquest_zcomp_2 . "', "; // ZQUEST_ZCOMP_2
                            $value .= "'" . $zquest_zcomp_3 . "', "; // ZQUEST_ZCOMP_3
                            $value .= "'" . $zquest_zcomp_4 . "', "; // ZQUEST_ZCOMP_4
                            $value .= "'" . ( empty($question->resume) ? ' ' : str_replace('\\\'', '\'\'', html_entity_decode($question->resume)) ) . "', "; // ZQUEST_YRESUME_0
                            $value .= "'" . $question->id_affectation . "', "; // ZQUEST_YSREASS_0
                            $value .= "TO_DATE('" . date('d/m/Y', strtotime($question->creation_date)) . "', 'dd/mm/yyyy'), "; // ZQUEST_CREDAT_0
                            $value .= "'" . ( ( !empty($documents[0]) && !empty($documents[0]->id) ) ? mvc_model('Document')->generatePublicUrl($documents[0]->id) : ' ' ) ."', "; // ZQUEST_ZLIENS_0
                            $value .= "'" . ( ( !empty($documents[1]) && !empty($documents[1]->id) ) ? mvc_model('Document')->generatePublicUrl($documents[1]->id) : ' ' ) ."', "; // ZQUEST_ZLIENS_1
                            $value .= "'" . ( ( !empty($documents[2]) && !empty($documents[2]->id) ) ? mvc_model('Document')->generatePublicUrl($documents[2]->id) : ' ' ) ."', "; // ZQUEST_ZLIENS_2
                            $value .= "'" . ( ( !empty($documents[3]) && !empty($documents[3]->id) ) ? mvc_model('Document')->generatePublicUrl($documents[3]->id) : ' ' ) ."', "; // ZQUEST_ZLIENS_3
                            $value .= "'" . ( ( !empty($documents[4]) && !empty($documents[4]->id) ) ? mvc_model('Document')->generatePublicUrl($documents[4]->id) : ' ' ) ."', "; // ZQUEST_ZLIENS_4
//                            $value .= $content . ","; // ZTXTQUEST_0
                            $value .= "' ',"; // ZTXTQUEST_0
                            $value .= "'000000',"; // ZQUEST_SRENUM1_0
                            $value .= "' ',"; // ZQUEST_ZMESSERR_0
                            $value .= "'0'"; // ZQUEST_ZERR_0

                            $value .= ")";

                            $queryBloc[] = $value;
                        }
                        // preparation requete en masse
                        if (count($queryBloc) > 0) {
                            $query = 'INSERT ALL ';
                            $query .= implode(' ', $queryBloc);
                            $query .= ' SELECT * FROM dual';
writeLog($query, 'query_export.log');
                        }
                        break;
                    case CONST_DB_DEFAULT:
                    default:
                        foreach ($questions as $question) {
                            // remplit la liste des questions
                            $qList[] = $question->id;

                            // competence et matiere principale associées
                            $compCode     = 0;
                            $matiereCode  = 0;
                            if (isset($question->competence) && is_object($question->competence)) {
                                if ($question->competence->id) {
                                    $compCode = $question->competence->id;
                                }
                                if ($question->competence->code_matiere) {
                                    $matiereCode = $question->competence->code_matiere;
                                }
                            }

                            // competence et maitere secondaire associées
                            $zquest_zcomp_0 = $zquest_zcomp_1 = $zquest_zcomp_2 = $zquest_zcomp_3 = $zquest_zcomp_4 = 0;
                            $zquest_ymat_0  = $zquest_ymat_1 = $zquest_ymat_2 = $zquest_ymat_3 = $zquest_ymat_4 = 0;
                            if (is_array($question->competences) && count($question->competences) > 0) {
                                foreach ($question->competences as $key => $comp) {
                                    $paramComp  = 'zquest_zcomp_' . $key;
                                    $paramMat   = 'zquest_ymat_' . $key;
                                    $$paramComp = $comp->id;
                                    $$paramMat = $comp->code_matiere;
                                }
                            }

                            $value = "(";

                            $value .= "'" . $question->id . "', "; // ZQUEST_ZIDQUEST_0
                            $value .= "0, "; // ZQUEST_ZTRAITEE_0
                            $value .= "'" . $question->client_number . "', "; // ZQUEST_SREBPC_0
                            $value .= "'" . $question->sreccn . "', "; // ZQUEST_SRECCN_0
                            $value .= "'" . $question->id_support . "', "; // ZQUEST_YCODESUP_0
                            $value .= "'" . $matiereCode . "', "; // ZQUEST_YMATIERE_0
                            $value .= "'" . $zquest_ymat_0 . "', "; // ZQUEST_YMAT_0
                            $value .= "'" . $zquest_ymat_1 . "', "; // ZQUEST_YMAT_1
                            $value .= "'" . $zquest_ymat_2 . "', "; // ZQUEST_YMAT_2
                            $value .= "'" . $zquest_ymat_3 . "', "; // ZQUEST_YMAT_3
                            $value .= "'" . $zquest_ymat_4 . "', "; // ZQUEST_YMAT_4
                            $value .= "'" . $compCode . "', ";        // ZQUEST_ZCOMPETENC_0
                            $value .= "'" . $zquest_zcomp_0 . "', "; // ZQUEST_ZCOMP_0
                            $value .= "'" . $zquest_zcomp_1 . "', "; // ZQUEST_ZCOMP_1
                            $value .= "'" . $zquest_zcomp_2 . "', "; // ZQUEST_ZCOMP_2
                            $value .= "'" . $zquest_zcomp_3 . "', "; // ZQUEST_ZCOMP_3
                            $value .= "'" . $zquest_zcomp_4 . "', "; // ZQUEST_ZCOMP_4
                            $value .= "'" . $question->resume . "', "; // ZQUEST_YRESUME_0
                            $value .= "'" . $question->id_affectation . "', "; // ZQUEST_YSREASS_0
                            $value .= "'" . date('d/m/Y', strtotime($question->creation_date)) . "'"; // ZQUEST_CREDAT_0

                            $value .= ")";

                            $queryBloc[] = $value;
                        }
                        // preparation requete en masse
                        if (count($queryBloc) > 0) {
                            $query = 'INSERT' . $query . implode(', ', $queryBloc);
                        }
                        break;
                }
            }

            // execution requete
            if (!empty($query)) {
                if ($result = $this->adapter->execute($query) && !empty($qList)) {
                    // update cri_question.transmis_erp
                    $sql = " UPDATE {$this->table} SET transmis_erp = 1 WHERE id IN (" . implode(', ', $qList) . ")";
                    $this->wpdb->query($sql);
                } else {
                    // log erreur
                    $error = sprintf(CONST_EXPORT_EMAIL_ERROR, date('d/m/Y à H:i:s'));
                    writeLog($error, 'exportquestion.log','Cridon - Export');

                    // send email
                    reportError(CONST_EXPORT_EMAIL_ERROR, $error);
                }
            }

            // status code
            return CONST_STATUS_CODE_OK;
        } catch(\Exception $e) {
            // write into logfile
            writeLog($e, 'exportquestion.log');

            // status code
            return CONST_STATUS_CODE_GONE;
        }
    } 
    
    /**
     * @return array
     */
    public function getJuristeAndAssistant() {
        global $wpdb;
        $sql = "
    SELECT
        q.juriste as juriste_code,
        u.display_name as juriste_name,
        uc.profil as juriste_profil,
        q.yuser as assistant_code,
        u2.display_name as assistant_name,
        uc2.profil as assistant_profil,
        q.id as id
    FROM
        ".$wpdb->prefix."question AS q
            LEFT JOIN
        ".$wpdb->prefix."user_cridon AS uc ON q.juriste = uc.id_erp
            LEFT JOIN
        ".$wpdb->prefix."user_cridon AS uc2 ON q.yuser = uc2.id_erp
            LEFT JOIN
        ".$wpdb->prefix."users AS u ON uc.id_wp_user = u.id
            LEFT JOIN
        ".$wpdb->prefix."users AS u2 ON uc2.id_wp_user = u2.id
    WHERE q.id = ".$this->id.";
        ";
        $r = $wpdb->get_results($sql);
        $result = array();
        foreach($r as $data) {
            $result[$data->id] = $data;
        }
        return $result;
    }

    /**
     * @param $questions
     * @return mixed
     */
    public static function getJuristeAndAssistantFromQuestions($questions) {
        global $wpdb;
        $id_array = array();
        if (is_object($questions) && get_class($questions) == "MvcModelObject") {
            $id_array[] = $questions->id;
        } else if (is_array($questions)
            && is_object($questions[0])
            && get_class($questions[0]) == "MvcModelObject"
        ) {
            foreach ($questions as $q ) {
                $id_array[] = $q->id;
            }
        } else if (is_array($questions) && is_int($questions[0])) {
            $id_array = $questions;
        } else if (is_int($questions)) {
            $id_array[] = $questions;
        } else {
            return false;
        }
        $sql = "
    SELECT
        q.juriste as juriste_code,
        u.display_name as juriste_name,
        uc.profil as juriste_profil,
        q.yuser as assistant_code,
        u2.display_name as assistant_name,
        uc2.profil as assistant_profil,
        q.id as id
    FROM
        ".$wpdb->prefix."question AS q
            LEFT JOIN
        ".$wpdb->prefix."user_cridon AS uc ON q.juriste = uc.id_erp
            LEFT JOIN
        ".$wpdb->prefix."user_cridon AS uc2 ON q.yuser = uc2.id_erp
            LEFT JOIN
        ".$wpdb->prefix."users AS u ON uc.id_wp_user = u.id
            LEFT JOIN
        ".$wpdb->prefix."users AS u2 ON uc2.id_wp_user = u2.id
    WHERE q.id IN (".implode(",",$id_array).");
        ";

        $r = $wpdb->get_results($sql);
        $result = array();
        foreach($r as $data) {
            $result[$data->id] = $data;
        }
        return $result;
    }


    /**
     * Action for importing data using CSV file
     *
     * @params array $indexes
     * @return bool
     */
    public function importQuestion2006to2009($indexes)
    {
        // wp default upload dir
        $uploadDir = wp_upload_dir();

        // instance of csvPrser
        $csvParser = new CridonCsvParser();

        try {

            // check if file exist
            if (file_exists($uploadDir['basedir'] . '/questions2006_2010.csv')) {

                $csvParser->enclosure = '';
                $csvParser->encoding(null, 'UTF-8');
                $csvParser->auto($uploadDir['basedir'] . '/questions2006_2010.csv');

                // no error was found
                if (property_exists($csvParser, 'data') && intval($csvParser->error) <= 0) {

                    // prepare question data
                    $this->manageQuestData($indexes, $csvParser->data);

                    // do archive
                    if ($this->importSuccess) {
                        rename($uploadDir['basedir'] . '/questions2006_2010.csv', str_replace(".csv", ".csv." . date('YmdHi'), $uploadDir['basedir'] . '/questions2006_2010.csv'));
                    }
                } else { // file content error
                    // write into logfile
                    writeLog(sprintf(CONST_EMAIL_ERROR_CORRUPTED_FILE, 'Question (' . $uploadDir['basedir'] . '/questions2006_2010.csv)'), 'importQuest2006_2009.log');
                }
            } else { // file doesn't exist
                // write into logfile
                writeLog(sprintf(CONST_EMAIL_ERROR_CONTENT, 'Question (' . $uploadDir['basedir'] . '/questions2006_2010.csv)'), 'importQuest2006_2009.log');
            }
        } catch (Exception $e) {

            // write write into logfile
            writeLog($e, 'importQuest2006_2009.log');
        }

        return $this->importSuccess;
    }

    /**
     * Import action
     *
     * @param array $indexes
     * @param array $datas
     * @throws Exception
     */
    public function manageQuestData($indexes = array(), $datas = array())
    {
        // init  logs var
        $errorDocList = array();

        foreach ($datas as $data) {
            // question
            $question = $this->getQuestionBy($data[$indexes['CRPCEN']], $data[$indexes['SRENUM']]);

            if ($question->id) { // quest exist
                if ($data[$indexes['PDF']]) { // Champ PDF non vide
                    // doc label
                    $label = ($data[$indexes['SUITE']]) ? 'Suite' : 'question/reponse';

                    // Date réponse réelle dans la table cri_question
                    $transDate = $question->real_date;
                    $transDate = date('Ym', strtotime($transDate));

                    $uploadDir = wp_upload_dir();
                    $path      = $uploadDir['basedir'] . '/questions/' . $transDate . '/';
                    if (!file_exists($path)) { // repertoire manquant
                        // creation du nouveau repertoire
                        wp_mkdir_p($path);
                    }

                    // ajout document dans Site
                    $this->addQuestDoc($path, $transDate, $question->id, $label, $indexes, $data, $errorDocList);
                }
            } else { // sinon ajout nouvelle question, notaire, doc associés
                $questData = array(
                    'Question' => array(
                        'srenum'           => $data[$indexes['SRENUM']],
                        'crpcen'           => $data[$indexes['CRPCEN']],
                        'resume'           => $data[$indexes['OBJET']],
                        'id_competence_1'  => $data[$indexes['COMPETENCE']],
                        'juriste'          => $data[$indexes['JURISTE']],
                        'id_support'       => $data[$indexes['SUPPORT']],
                        'id_affectation'   => $data[$indexes['CODE_AFFECTATION']],
                        'creation_date'    => $data[$indexes['DATE_CREATION']],
                        'affectation_date' => $data[$indexes['DATE_AFFECTATION']],
                        'real_date'        => $data[$indexes['DATE_REPONSE']]
                    )
                );

                // question id
                $questionId = mvc_model('Question')->create($questData);

                // ajout document
                // doc label
                $label = 'question/reponse';

                // Date réponse réelle dans la table cri_question
                $transDate = $data[$indexes['DATE_REPONSE']];
                $transDate = date('Ym', strtotime($transDate));

                $uploadDir = wp_upload_dir();
                $path      = $uploadDir['basedir'] . '/questions/' . $transDate . '/';

                // ajout document dans Site
                $this->addQuestDoc($path, $transDate, $questionId, $label, $indexes, $data, $errorDocList);

                // ajout notaire associe
                $notaryData = array(
                    'category'           => '-',
                    'first_name'         => $data[$indexes['NOTAIRE_PRENOM']],
                    'last_name'          => $data[$indexes['NOTAIRE_NOM']],
                    'crpcen'             => $data[$indexes['CRPCEN']],
                    'web_password'       => CONST_NOTARY_PWD,
                    'tel_password'       => '-',
                    'code_interlocuteur' => '-',
                    'id_civilite'        => '-',
                    'email_adress'       => '-',
                    'id_fonction'        => '-',
                    'tel'                => '-',
                    'fax'                => '-',
                    'tel_portable'       => '-',
                    'date_modified'      => $data[$indexes['DATE_CREATION']],
                    'id_wp_user'         => 0,
                );

                // notaire id
                $notaryId = mvc_model('Notaire')->create($notaryData);

                // creation wp_user associe
                // utilisation de requette simple vs fonction native de wp pour insertion user afin d'eviter
                // l'affection de role par defaut
                $displayName = $data[$indexes['NOTAIRE_PRENOM']] . ' ' . $data[$indexes['NOTAIRE_NOM']];
                $userData    = array(
                    'user_login'      => $data[$indexes['CRPCEN']] . CONST_LOGIN_SEPARATOR . $notaryId,
                    'user_pass'       => wp_hash_password(CONST_NOTARY_PWD),
                    'user_nicename'   => sanitize_title($displayName),
                    'user_email'      => ' - ',
                    'user_registered' => $data[$indexes['DATE_CREATION']],
                    'user_status'     => CONST_STATUS_ENABLED,
                    'display_name'    => $displayName,
                );

                if ($this->wpdb->insert($this->wpdb->users, $userData)) {
                    // maj Notaire.id_wp_user
                    $notaryUpdData = array(
                        'Notaire' => array(
                            'id'         => $notaryId,
                            'id_wp_user' => $this->wpdb->insert_id
                        )
                    );
                    mvc_model('Notaire')->save($notaryUpdData);
                }
            }
        }

        // trace logs
        if (count($errorDocList) > 0) {
            writeLog($errorDocList, 'importQuest2006_2009.log');
        }

        // import terminé
        $this->importSuccess = true;
    }

    /**
     * Add Question associated docs
     *
     * @param string $path
     * @param string $transDate
     * @param int $questionId
     * @param string $label
     * @param mixed $errorDocList
     * @throws Exception
     */
    protected function addQuestDoc($path, $transDate, $questionId, $label, $indexes, $data, $errorDocList)
    {
        // fichier pdf
        $storedInfoFile = $data[$indexes['PDF']];
        $docName = pathinfo($data[$indexes['PDF']])['basename'];//premier PDF

        if (($iPos = strpos($docName, '_')) !== false) {
            //a prefix can be mentionned in the file. Don't use it.
            $docName = substr($docName, $iPos + 1);
        }

        // Incrémenter le nom de fichier s'il existe déjà dans le dossier le même nom de fichier
        $filename = mvc_model('Document')->incrementFileName($path, $docName);

        // preserve file as exists in metadata : fileExists might change it into FALSE if not found
        $fileToImport   = fileExists($storedInfoFile, false);
        if ($fileToImport && copy($fileToImport, $path . $filename)) {
            // donnees document
            $docData = array(
                'Document' => array(
                    'file_path'     => '/questions/' . $transDate . '/' . $filename,
                    'download_url'  => '/documents/download/' . $questionId,
                    'date_modified' => date('Y-m-d H:i:s'),
                    'type'          => 'question',
                    'id_externe'    => $questionId,
                    'name'          => $filename,
                    'label'         => $label
                )
            );

            $documentId = mvc_model('Document')->create($docData);

            // maj download_url
            $docData = array(
                'Document' => array(
                    'id'           => $documentId,
                    'download_url' => '/documents/download/' . $documentId
                )
            );
            mvc_model('Document')->save($docData);
        } else {
            $errorDocList[] = '404 File : ' . $storedInfoFile;
        }
    }

    /**
     * get Question by crpcen and srenum
     *
     * @param string $crpcen
     * @param string $srenum
     * @return array|null|object|void
     */
    public function getQuestionBy($crpcen, $srenum)
    {
        $query = "SELECT id, real_date FROM {$this->table} as q
                LEFT JOIN {$this->wpdb->prefix}notary as n on q.client_number = n.client_number
                WHERE n.crpcen = '{$crpcen}' and q.srenum = '{$srenum}'
                LIMIT 1";

        return $this->wpdb->get_row($query);
    }

    /**
     * Insert data into DB
     *
     * @param object $notaire
     * @param array $post
     * @param array $response
     * @return mixed
     * @throws Exception
     */
    protected function insertData($notaire, $post, $response)
    {
        // notaire exist
        if ($notaire->client_number
            && isset($post[CONST_QUESTION_OBJECT_FIELD]) && $post[CONST_QUESTION_OBJECT_FIELD] != ''
            && isset($post[CONST_QUESTION_SUPPORT_FIELD]) && ctype_digit($post[CONST_QUESTION_SUPPORT_FIELD]) && ((int)$post[CONST_QUESTION_SUPPORT_FIELD] > 0)
            && isset($post[CONST_QUESTION_MATIERE_FIELD]) && !empty($post[CONST_QUESTION_MATIERE_FIELD])
            && isset($post[CONST_QUESTION_COMPETENCE_FIELD]) && $post[CONST_QUESTION_COMPETENCE_FIELD] != ''
            && isset($post[CONST_QUESTION_MESSAGE_FIELD]) && $post[CONST_QUESTION_MESSAGE_FIELD] != ''
        ) {
            // prepare data
            $question = array(
                'Question' => array(
                    'client_number'      => $notaire->client_number,
                    'sreccn'             => $notaire->code_interlocuteur,
                    'resume'             => htmlentities($post[CONST_QUESTION_OBJECT_FIELD]),
                    'creation_date'      => date('Y-m-d H:i:s'),
                    'id_support'         => $post[CONST_QUESTION_SUPPORT_FIELD], // Support
                    'id_competence_1'    => $post[CONST_QUESTION_COMPETENCE_FIELD], // Competence
                    'content'            => htmlentities($post[CONST_QUESTION_MESSAGE_FIELD]), // Message
                    'mobile_push_token'  => ($post[CONST_QUESTION_PUSHTOKEN_FIELD]), // push token
                    'mobile_device_type' => ($post[CONST_QUESTION_DEVICETYPE_FIELD]) // device type
                )
            );
            // insert question
            $questionId = $this->create($question);

            // Attached files
            if ($questionId && isset($_FILES[CONST_QUESTION_ATTACHEMENT_FIELD]['name'])) {
                // prepare file data
                $files = $_FILES[CONST_QUESTION_ATTACHEMENT_FIELD];

                // \CriFileUploader required an array of data
                if (!is_array($files['name'])) {
                    $files = array(
                        'name'     => array($_FILES[CONST_QUESTION_ATTACHEMENT_FIELD]['name']),
                        'type'     => array($_FILES[CONST_QUESTION_ATTACHEMENT_FIELD]['type']),
                        'tmp_name' => array($_FILES[CONST_QUESTION_ATTACHEMENT_FIELD]['tmp_name']),
                        'error'    => array($_FILES[CONST_QUESTION_ATTACHEMENT_FIELD]['error']),
                        'size'     => array($_FILES[CONST_QUESTION_ATTACHEMENT_FIELD]['size']),
                    );
                }
                // instance of CriFileUploader
                $criFileUploader = new CriFileUploader();
                // set files list
                $criFileUploader->setFiles($files);
                // set max size from config (@see : const.inc.php)
                $criFileUploader->setMaxSize(CONST_QUESTION_MAX_FILE_SIZE);
                // set upload dir
                $uploadDir = wp_upload_dir();
                $path      = $uploadDir['basedir'] . '/questions/' . date('Ym') . '/';
                if (!file_exists($path)) { // not yet directory
                    // crete the directory
                    wp_mkdir_p($path);
                }
                $criFileUploader->setUploaddir($path);

                // validate file size, max upload authorized,...
                if ($criFileUploader->validate()) {
                    $listDocuments = $criFileUploader->execute();

                    if (is_array($listDocuments) && count($listDocuments) > 0) {
                        foreach ($listDocuments as $document) {
                            // prepare data
                            $documents = array(
                                'Document' => array(
                                    'file_path'     => '/questions/' . date('Ym') . '/' . $document,
                                    'download_url'  => '/documents/download/' . $questionId,
                                    'date_modified' => date('Y-m-d H:i:s'),
                                    'type'          => 'question',
                                    'id_externe'    => $questionId,
                                    'name'          => $document,
                                    'label'         => 'PJ'
                                )
                            );

                            // insert
                            $documentId = mvc_model('Document')->create($documents);

                            // update download_url
                            $documents = array(
                                'Document' => array(
                                    'id'           => $documentId,
                                    'download_url' => '/documents/download/' . $documentId
                                )
                            );
                            mvc_model('Document')->save($documents);
                        }
                    }
                } else {
                    $response['error'][] = sprintf(CONST_QUESTION_FILE_SIZE_ERROR,
                        (CONST_QUESTION_MAX_FILE_SIZE / 1000000) . 'M');

                    return $response;
                }
            }
        } else {
            if (!isset($post[CONST_QUESTION_OBJECT_FIELD]) || $post[CONST_QUESTION_OBJECT_FIELD] == '') {
                $response['error'][] = CONST_EMPTY_OBJECT_ERROR_MSG;
            }
            if (!isset($post[CONST_QUESTION_MESSAGE_FIELD]) || $post[CONST_QUESTION_MESSAGE_FIELD] == '') {
                $response['error'][] = CONST_EMPTY_MESSAGE_ERROR_MSG;
            }
            if (!isset($post[CONST_QUESTION_SUPPORT_FIELD]) || !ctype_digit($post[CONST_QUESTION_SUPPORT_FIELD]) || intval($post[CONST_QUESTION_SUPPORT_FIELD] <= 0)) {
                $response['error'][] = CONST_EMPTY_SUPPORT_ERROR_MSG;
            }
            if (!isset($post[CONST_QUESTION_MATIERE_FIELD]) || intval($post[CONST_QUESTION_MATIERE_FIELD] <= 0)) {
                $response['error'][] = CONST_EMPTY_ACTIVITY_ERROR_MSG;
            }
            if (!isset($post[CONST_QUESTION_COMPETENCE_FIELD]) || $post[CONST_QUESTION_COMPETENCE_FIELD] == '') {
                $response['error'][] = CONST_EMPTY_SUBACTIVITY_ERROR_MSG;
            }
        }

        return $response;
    }

    /**
     * Create Question from mobile
     *
     * @param mixed $data
     * @return mixed
     */
    public function createFromMobile($data)
    {
        // init error
        $response = array();
        $response['error'] = array();

        return $this->insertData($data['notary'], $data['post'], $response);
    }

    /**
     * Get list of question need to be notified
     *
     * @return array|null|object
     */
    public function getQuestForNotification()
    {
        // init query
        $query  = " SELECT `q`.`id`, `q`.`resume`, `q`.`mobile_device_type`, `q`.`mobile_push_token`, `n`.`id` AS id_notaire FROM `{$this->table}` q ";
        $query .= " LEFT JOIN `{$this->wpdb->prefix}notaire` n ON `n`.`client_number` = `q`.`client_number` ";
        $query .= " WHERE `mobile_push_token` IS NOT NULL
                    AND `mobile_device_type` IS NOT NULL
                    AND `mobile_notification_status` = 0
                    AND `id_affectation` = %d ";

        // exec prepared query
        return $this->wpdb->get_results($this->wpdb->prepare($query, CONST_QUEST_ANSWERED));
    }

    /**
     * Update notification status
     *
     * @param int $questionId
     */
    public function updateQuestNotificationStatus($questionId)
    {
        $this->wpdb->query(" UPDATE {$this->table} SET mobile_notification_status = 1 WHERE id = {$questionId} ");
    }

    /**
     * Push notification action
     *
     * @throws Exception
     */
    public function pushNotification()
    {
        global $cri_container;

        // get instance of Cridon Tools
        $tools = $cri_container->get('tools');

        // get list of questions to be notified
        $questions = $this->getQuestForNotification();

        // only executed for valid $questions data
        if (is_array($questions) && count($questions) > 0) {
            foreach ($questions as $question) {
                $messages = array(
                    'message'      => sprintf(CONST_NOTIFICATION_CONTENT_MSG, $question->resume),
                    'urlnotaire' => mvc_public_url(array('controller' => 'notaires', 'id' => $question->id_notaire))
                );

                $resp = $tools->pushNotification($question->mobile_device_type, $question->mobile_push_token, $messages, $question->id);

                // update question notification status
                if ($resp) {
                    $this->updateQuestNotificationStatus($question->id);
                }
            }
        }
    }
}
