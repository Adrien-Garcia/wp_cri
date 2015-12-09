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
    var $includes      = array('Competence','Competences','Affectation','Support', 'Notaire');
    var $belongs_to    = array(
        'Competence' => array('foreign_key' => 'id_competence_1'),
        //'Competence_2' => array('foreign_key' => 'id_competence_2'),
        'Affectation' => array('foreign_key' => 'id_affectation'),
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
                    $sql .= ' WHERE ' . $adapter::QUEST_YCODESUP . ' IN(' . implode(',', Config::$acceptedSupports) . ')';
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
                    $mainQuery .= ' WHERE ' . $adapter::QUEST_YCODESUP . ' IN(' . implode(',', Config::$acceptedSupports) . ')';
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
            $sql .= " WHERE srenum IS NOT NULL ";
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
        $options['attributes'] .= 'affectation_date, wish_date, real_date, yuser, treated, creation_date, date_modif, ';
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
        $wp_administrators = get_users( 'role=administrator' );//administrators in website
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
        $error = array();

        //Not access form, only for Notaire connected
        if( !is_user_logged_in() || !CriIsNotaire() ){
            return false;
        }
        try {
            // notaire data
            $notaire = CriNotaireData();

            // notaire exist
            if ($notaire->client_number
                && isset($post[CONST_QUESTION_OBJECT_FIELD]) && $post[CONST_QUESTION_OBJECT_FIELD] != ''
                && isset($post[CONST_QUESTION_SUPPORT_FIELD]) && $post[CONST_QUESTION_SUPPORT_FIELD] != ''
                && isset($post[CONST_QUESTION_MATIERE_FIELD]) && intval($post[CONST_QUESTION_MATIERE_FIELD]) > 0
                && isset($post[CONST_QUESTION_COMPETENCE_FIELD]) && intval($post[CONST_QUESTION_COMPETENCE_FIELD]) > 0
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
                        $error[] = sprintf(CONST_QUESTION_FILE_SIZE_ERROR,
                                           (CONST_QUESTION_MAX_FILE_SIZE / 1000000) . 'M');

                        return $error;
                    }
                }
            }else{
                if (!isset($post[CONST_QUESTION_OBJECT_FIELD]) || $post[CONST_QUESTION_OBJECT_FIELD] == '') {
                    $error[] = CONST_EMPTY_OBJECT_ERROR_MSG;
                }
                if (!isset($post[CONST_QUESTION_SUPPORT_FIELD]) || intval($post[CONST_QUESTION_SUPPORT_FIELD] <= 0)) {
                    $error[] = CONST_EMPTY_SUPPORT_ERROR_MSG;
                }
                if (!isset($post[CONST_QUESTION_MATIERE_FIELD]) || intval($post[CONST_QUESTION_MATIERE_FIELD] <= 0)) {
                    $error[] = CONST_EMPTY_MATIERE_ERROR_MSG;
                }
                if (!isset($post[CONST_QUESTION_COMPETENCE_FIELD]) || intval($post[CONST_QUESTION_COMPETENCE_FIELD] <= 0)) {
                    $error[] = CONST_EMPTY_COMPETENCE_ERROR_MSG;
                }
                return $error;
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
        $affectationDate = '0000-00-00'; // accepted date format if not set
        if (isset($data[$adapter::QUEST_SREDATASS])) {
            if (preg_match("/^(\d+)\/(\d+)\/(\d+)$/", $data[$adapter::QUEST_SREDATASS])) {
                $dateTime        = date_create_from_format('d/m/Y', $data[$adapter::QUEST_SREDATASS]);
                $affectationDate = $dateTime->format('Y-m-d');
            }
        }
        $wishDate = '0000-00-00'; // accepted date format if not set
        if (isset($data[$adapter::QUEST_YRESSOUH])) {
            if (preg_match("/^(\d+)\/(\d+)\/(\d+)$/", $data[$adapter::QUEST_YRESSOUH])) {
                $dateTime = date_create_from_format('d/m/Y', $data[$adapter::QUEST_YRESSOUH]);
                $wishDate = $dateTime->format('Y-m-d');
            }
        }
        $realDate = '0000-00-00'; // accepted date format if not set
        if (isset($data[$adapter::QUEST_SRERESDAT])) {
            if (preg_match("/^(\d+)\/(\d+)\/(\d+)$/", $data[$adapter::QUEST_SRERESDAT])) {
                $dateTime = date_create_from_format('d/m/Y', $data[$adapter::QUEST_SRERESDAT]);
                $realDate = $dateTime->format('Y-m-d');
            }
        }
        $updatedDate = ''; // accepted date format if not set
        if (isset($data[$adapter::QUEST_UPDDAT])) {
            if (preg_match("/^(\d+)\/(\d+)\/(\d+)$/", $data[$adapter::QUEST_UPDDAT])) {
                $dateTime    = date_create_from_format('d/m/Y', $data[$adapter::QUEST_UPDDAT]);
                $updatedDate = $dateTime->format('Y-m-d');
            }
        }

        // confidential
        $confidential = 0;
        if (isset($data[$adapter::QUEST_ZANOAMITEL]) && intval($data[$adapter::QUEST_ZANOAMITEL]) == 1) {
            $confidential = $data[$adapter::QUEST_ZANOAMITEL];
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
        $value .= "'" . $affectationDate . "', "; // affectation_date
        $value .= "'" . $wishDate . "', "; // wish_date
        $value .= "'" . $realDate . "', "; // real_date
        $value .= "'" . (isset($data[$adapter::QUEST_YUSER]) ? esc_sql($data[$adapter::QUEST_YUSER]) : '') . "', "; // yuser
        $value .= "'" . CONST_QUEST_UPDATED_IN_X3 . "', "; // treated
        $value .= "'" . (($updatedDate != '0000-00-00') ? $updatedDate : date('Y-m-d')) . "', "; // creation_date
        $value .= empty($updatedDate) ? 'NULL, ' : "'" . $updatedDate . "', "; // date_modif
        $value .= (isset($data[$adapter::QUEST_ZUPDHOU]) && count(explode(':', $data[$adapter::QUEST_ZUPDHOU])) > 1) ? "'" . esc_sql($data[$adapter::QUEST_ZUPDHOU]) . "', " : 'NULL, '; // hour_modif
        $value .= "'" . CONST_QUEST_TRANSMIS_ERP . "', "; // transmis_erp
        $value .= "'" . $confidential . "', "; // confidential
        $value .= "''"; // content

        $value .= ")";

        return $value;
    }

    /**
     * Daily update
     */
    public function dailyUpdate()
    {
        try {
            // enable gbcollector
            if (function_exists('gc_enable')) {
                gc_enable();
            }

            // set adapter
            switch (strtolower(CONST_IMPORT_OPTION)) {
                case self::IMPORT_ODBC_OPTION:
                    $todateFunc    = 'STR_TO_DATE'; // SQL
                    $intervalParam = 'MINUTE'; // SQL
                    $timestampFunc = 'TIMESTAMPDIFF'; // SQL
                    $this->adapter = $adapter = CridonODBCAdapter::getInstance();
                    break;
                case self::IMPORT_OCI_OPTION:
                default :
                    //if case above did not match, set OCI
                    $todateFunc    = 'TO_DATE'; // Oracle
                    $intervalParam = 'SQL_TSI_MINUTE'; // Oracle
                    $timestampFunc = 'TIMESTAMPDIFF'; // @see: https://docs.oracle.com/html/A95915_01/sqfunc.htm#i1006893
                    $this->adapter = $adapter = CridonOCIAdapter::getInstance();
                    break;
            }

            // get last cron date if is set or server datetime
            $lastDateUpdate        = date('Y-m-d H:i:s');
            $lastCronDate          = get_option('cronquestionupdate') ? get_option('cronquestionupdate') : $lastDateUpdate;
            $date                  = new DateTime($lastCronDate);
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

            $mainQuery = 'SELECT * FROM ' . CONST_ODBC_TABLE_QUEST;
            $mainQuery .= " WHERE {$timestampFunc}({$intervalParam}, '{$dateModif} {$hourModif}', CONCAT_WS(' ', {$todateFunc}(" . $adapter::QUEST_UPDDAT . ", '%d/%m/%Y'), " . $adapter::QUEST_ZUPDHOU . ")) > 0  ";
            $mainQuery .= " AND " . $adapter::QUEST_ZUPDHOU . " IS NOT NULL
                        AND " . $adapter::QUEST_ZUPDHOU . " != '' ";
            // filter by list of supports if necessary
            if (is_array(Config::$acceptedSupports) && count(Config::$acceptedSupports) > 0) {
                $mainQuery .= ' AND ' . $adapter::QUEST_YCODESUP . ' IN(' . implode(',',
                        Config::$acceptedSupports) . ')';
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
                        if (isset($data[$adapter::QUEST_SRENUM])) {
                            $query .= " srenum = '" . esc_sql($data[$adapter::QUEST_SRENUM]) . "', ";
                        }

                        if (isset($data[$adapter::QUEST_SRECCN])) {
                            $query .= " sreccn = '" . esc_sql($data[$adapter::QUEST_SRECCN]) . "', ";
                        }

                        if (isset($data[$adapter::QUEST_YCODESUP])) {
                            $query .= " id_support = '" . esc_sql($data[$adapter::QUEST_YCODESUP]) . "', ";
                        }

                        if (isset($data[$adapter::QUEST_ZCOMPETENC])) {
                            $query .= " id_competence_1 = '" . intval($data[$adapter::QUEST_ZCOMPETENC]) . "', ";
                        }

                        if (isset($data[$adapter::QUEST_YRESUME])) {
                            $query .= " resume = '" . esc_sql($data[$adapter::QUEST_YRESUME]) . "', ";
                        }

                        if (isset($data[$adapter::QUEST_YSREASS])) {
                            $query .= " id_affectation = '" . intval($data[$adapter::QUEST_YSREASS]) . "', ";
                        }

                        if (isset($data[$adapter::QUEST_SREDET])) {
                            $query .= " juriste = '" . esc_sql($data[$adapter::QUEST_SREDET]) . "', ";
                        }

                        if (isset($data[$adapter::QUEST_SREDET])) {
                            $query .= " affectation_date = '" . esc_sql($data[$adapter::QUEST_SREDET]) . "', ";
                        }

                        if (isset($data[$adapter::QUEST_YRESSOUH]) && preg_match("/^(\d+)\/(\d+)\/(\d+)$/",
                                $data[$adapter::QUEST_YRESSOUH])
                        ) {
                            $dateTime = date_create_from_format('d/m/Y', $data[$adapter::QUEST_YRESSOUH]);
                            $query .= " wish_date = '" . $dateTime->format('Y-m-d') . "', ";
                        }

                        if (isset($data[$adapter::QUEST_SRERESDAT]) && preg_match("/^(\d+)\/(\d+)\/(\d+)$/",
                                $data[$adapter::QUEST_SRERESDAT])
                        ) {
                            $dateTime = date_create_from_format('d/m/Y', $data[$adapter::QUEST_UPDDAT]);
                            $query .= " real_date = '" . $dateTime->format('Y-m-d') . "', ";
                        }

                        if (isset($data[$adapter::QUEST_YUSER])) {
                            $query .= " yuser = '" . esc_sql($data[$adapter::QUEST_YUSER]) . "', ";
                        }

                        $query .= " treated = '" . CONST_QUEST_UPDATED_IN_X3 . "', ";

                        if (isset($data[$adapter::QUEST_UPDDAT]) && preg_match("/^(\d+)\/(\d+)\/(\d+)$/",
                                $data[$adapter::QUEST_UPDDAT])
                        ) {
                            $dateTime = date_create_from_format('d/m/Y', $data[$adapter::QUEST_UPDDAT]);
                            $query .= " date_modif = '" . $dateTime->format('Y-m-d') . "', ";
                        }

                        if (isset($data[$adapter::QUEST_ZUPDHOU]) && count(explode(':',
                                $data[$adapter::QUEST_ZUPDHOU])) > 1
                        ) {
                            $query .= " hour_modif = '" . esc_sql($data[$adapter::QUEST_ZUPDHOU]) . "', ";
                        }

                        $query .= " transmis_erp = '" . CONST_QUEST_TRANSMIS_ERP . "', ";

                        /**
                         * "0 : non
                         * 1 : oui (afficher sans document PDF, pas de génération d'alerte email pour les secrétaire)"
                         */
                        $confidential = 0;
                        if (isset($data[$adapter::QUEST_ZANOAMITEL]) && intval($data[$adapter::QUEST_ZANOAMITEL]) == 1) {
                            $confidential = $data[$adapter::QUEST_ZANOAMITEL];
                        }
                        $query .= " confidential = '" . $confidential . "' ";

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
            }
            if (count($updateValues) > 0) {
                $queryBulder = mvc_model('QueryBuilder');
                // bulk update
                $updtateQuery = implode(' ', $updateValues);

                if (!$queryBulder->getInstanceMysqli()->multi_query($updtateQuery)) {
                    // write into logfile
                    writeLog($queryBulder->getInstanceMysqli()->error, 'query.log');
                }
            }

            // maj derniere date d'execution
            update_option('cronquestionupdate', $lastDateUpdate);

            return CONST_STATUS_CODE_OK;
        } catch (\Exception $e) {
            writeLog($e, 'questiondailyupdate.log');

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
                    $mainQuery .= ' WHERE ' . $adapter::QUEST_YCODESUP . ' IN(' . implode(',', Config::$acceptedSupports) . ')';
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
}
