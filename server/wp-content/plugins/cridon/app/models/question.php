<?php

/**
 * Question Model
 */

class Question extends MvcModel
{
    /**
     * @var string
     */
    const IMPORT_ODBC_OPTION = 'odbc';

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
            reportError(CONST_EMAIL_ERROR_CATCH_EXCEPTION, $e->getMessage());
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
                && isset($post[CONST_QUESTION_SUPPORT_FIELD]) && $post[CONST_QUESTION_SUPPORT_FIELD] != ''
                && isset($post[CONST_QUESTION_MATIERE_FIELD]) && intval($post[CONST_QUESTION_MATIERE_FIELD]) > 0
                && isset($post[CONST_QUESTION_COMPETENCE_FIELD]) && $post[CONST_QUESTION_COMPETENCE_FIELD] != ''
                && isset($post[CONST_QUESTION_MESSAGE_FIELD]) && $post[CONST_QUESTION_MESSAGE_FIELD] != ''
            ) {
                // prepare data
                $question = array(
                    'Question' => array(
                        'client_number' => $notaire->client_number,
                        'sreccn' => $notaire->code_interlocuteur,
                        'resume' => htmlentities($post[CONST_QUESTION_OBJECT_FIELD]),
                        'creation_date' => date('Y-m-d H:i:s'),
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
                if (!isset($post[CONST_QUESTION_SUPPORT_FIELD]) || intval($post[CONST_QUESTION_SUPPORT_FIELD] <= 0)) {
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

            return true;
        } catch(\Exception $e) {
            writeLog( $e,'upload.log' );
            return false;
        }
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
        $value .= "'" . (isset($data[$adapter::QUEST_ZCOMPETENC]) ? esc_sql(intval($data[$adapter::QUEST_ZCOMPETENC])) : '') . "', "; // id_competence_1
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
     */
    public function cronUpdate()
    {
        try {
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
            $queryOptions          = array();
            $queryOptions['daily'] = true;
            $dateModif             = $date->format('Y-m-d');
            $hourModif             = $date->format('H:i:s');

            $this->setSiteQuestList($queryOptions);

            // insert options
            $options               = array();
            $options['table']      = 'question';
            $options['attributes'] = 'srenum, client_number, sreccn, id_support, id_competence_1, `resume`, id_affectation, juriste, ';
            $options['attributes'] .= 'affectation_date, wish_date, real_date, yuser, treated, creation_date, date_modif, ';
            $options['attributes'] .= 'hour_modif, transmis_erp, confidential, content';
            // insert values
            $insertValues = array();

            // update values
            $updateValues = array();

            $mainQuery = 'SELECT * FROM ' . CONST_ODBC_TABLE_QUEST . ' WHERE ';
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
            // filter by list of supports if necessary
            if (is_array(Config::$acceptedSupports) && count(Config::$acceptedSupports) > 0) {
                $mainQuery .= ' AND ' . $adapter::QUEST_YCODESUP . ' IN(' . implode(',',
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
                            $query .= " id_competence_1 = '" . intval($data[$adapter::QUEST_ZCOMPETENC]) . "', ";
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
                        $query .= " wish_date = " . (empty($wishDate) ? "NULL," : "'".$wishDate."'") . ", ";
                        $query .= " real_date = " . (empty($realDate) ? "NULL," : "'".$realDate."'") . ", ";

                        if (isset($data[$adapter::QUEST_YUSER])) {
                            $query .= " yuser = '" . esc_sql($data[$adapter::QUEST_YUSER]) . "', ";
                        }

                        $query .= " date_modif = " . (empty($updatedDate) ? "NULL," : "'".$updatedDate."'") . ", ";
                        $query .= " hour_modif = " . (empty($updatedHour) ? "NULL," : "'".$updatedHour."'") . ", ";


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
                $queryBulder = mvc_model('QueryBuilder');
                // bulk update
                $updtateQuery = implode(' ', $updateValues);

                if (!$queryBulder->getInstanceMysqli()->multi_query($updtateQuery)) {
                    // write into logfile
                    writeLog($queryBulder->getInstanceMysqli()->error, 'questioncronUpdate.log');
                }
                writeLog(count($updateValues). ' questions maj', 'questioncronUpdate.log');
            }

            // maj derniere date d'execution
            update_option('cronquestionupdate', $lastDateUpdate);

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
            // store question list into local var
            $siteQuestList = $this->siteQuestList;

            // nb items
            $this->getNbItems();

            // list of question
            $i = 0;
            $this->setQuestListForDelete($i, $siteQuestList);

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
                mvc_model('QueryBuilder')->getInstanceMysqli()->query($query);
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
     * @param array $siteQuestList
     */
    protected function setQuestListForDelete($i, $siteQuestList)
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

                        if (in_array($uniqueKey, $siteQuestList)) { // quest found on Site / ERP

                            // remove quest on list
                            $key = array_search($uniqueKey, $siteQuestList);
                            if ($key !== false) {
                                unset($siteQuestList[$key]);
                                $this->questListForDelete = $siteQuestList;
                            }
                        }
                    }
                }

                // increments flag
                $i++;

                // call import action
                $this->setQuestListForDelete($i, $siteQuestList);

            } else {
                // Close Connection
                $this->adapter->closeConnection();
            }

        } catch (\Exception $e) {
            // send email
            reportError(CONST_EMAIL_ERROR_CATCH_EXCEPTION, $e->getMessage());
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
                        $query .= $adapter::ZQUEST_SRENUM_0 . ", ";   // ZQUEST_SRENUM_0
                        $query .= $adapter::ZQUEST_ZMESERR_0 . ", ";   // ZQUEST_ZMESSERR_0
                        $query .= $adapter::ZQUEST_ZERR_0 . " ";   // ZQUEST_ZERR_0

                        $query .= ")  VALUES ";

                        foreach ($questions as $question) {
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
                            $value .= "'000000',"; // ZQUEST_SRENUM_0
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
                    writeLog($error, 'exportquestion.log');

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
}
