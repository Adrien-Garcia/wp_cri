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
    protected function setSiteQuestList()
    {
        // increase memory limit
        ini_set('memory_limit', '-1');

        // get list of existing question
        $sql = "SELECT srenum, client_number FROM {$this->table}";
        $questions = $this->wpdb->get_results($sql);
        // fill list of existing question on site with unique key (crpcen + passwd)
        foreach ($questions as $question) {
            array_push($this->siteQuestList, $question->client_number . $question->srenum);
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
                    $updatedDate = '0000-00-00'; // accepted date format if not set
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
                    $value .= "'" . date('Y-m-d') . "', "; // creation_date
                    $value .= "'" . $updatedDate . "', "; // date_modif
                    $value .= "'" . ((isset($data[$adapter::QUEST_ZUPDHOU]) && count(explode(':', $data[$adapter::QUEST_ZUPDHOU])) > 1) ? esc_sql($data[$adapter::QUEST_ZUPDHOU]) : '00:00:00') . "', "; // hour_modif
                    $value .= "'" . CONST_QUEST_TRANSMIS_ERP . "', "; // transmis_erp
                    $value .= "'" . $confidential . "', "; // confidential
                    $value .= "''"; // content

                    $value .= ")";

                    $insertValues[] = $value;

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

    public function uploadDocuments( $post ){
        //Not access form, only for Notaire connected
        if( !is_user_logged_in() || !CriIsNotaire() ){
            return false;
        }
        try {
            // notaire data
            $notaire = CriNotaireData();

            // notaire exist
            if ($notaire->client_number && isset($post[CONST_QUESTION_OBJECT_FIELD]) && isset($post[CONST_QUESTION_SUPPORT_FIELD]) && isset($post[CONST_QUESTION_COMPETENCE_FIELD]) && isset($post[CONST_QUESTION_MESSAGE_FIELD]) ) {
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
                    }
                }
            }else{
                return false;
            }

            return true;
        } catch(\Exception $e) {
            writeLog( $e,'upload.log' );
            return false;
        }
    }

    /**
     * @return array|null|object
     */
    public function exportQuestion()
    {
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
            $query = '';
            // adapter instance
            $adapter = $this->adapter;
            // list des id question pour maj cri_question apres transfert
            $qList = array();

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
                    $oracleLimit = self::CONST_LIMIT + intval($this->offset);
//                    $limitQuery = 'SELECT rownum rnum, subquery.* FROM ('.$mainQuery.') subquery WHERE rownum <= '.$oracleLimit;
//                    $sql = 'SELECT * FROM ('.$limitQuery.') WHERE rnum >= '.$this->offset;
                    break;
                case CONST_DB_DEFAULT:
                default:
                    foreach ($questions as $question) {
                        // remplit la liste des questions
                        $qList[] = $question->id;

                        // competence et matiere principale associées
                        $compId     = 0;
                        $maitiereId = 0;
                        if (isset($question->competence) && is_object($question->competence)) {
                            if ($question->competence->id) {
                                $compId = $question->competence->id;
                            }
                            if ($question->competence->code_matiere) {
                                $matieres = mvc_model('Matiere')->find_one_by_code($question->competence->code_matiere);
                                if ($matieres->id) {
                                    $maitiereId = $matieres->id;
                                }
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

                                $matSecond = mvc_model('Matiere')->find_one_by_code($comp->code_matiere);
                                if ($matSecond->id) {
                                    $$paramMat = $matSecond->id;
                                }
                            }
                        }

                        $value = "(";

                        $value .= "'" . $question->id . "', "; // ZQUEST_ZIDQUEST_0
                        $value .= "0, "; // ZQUEST_ZTRAITEE_0
                        $value .= "'" . $question->client_number . "', "; // ZQUEST_SREBPC_0
                        $value .= "'" . $question->sreccn . "', "; // ZQUEST_SRECCN_0
                        $value .= "'" . $question->id_support . "', "; // ZQUEST_YCODESUP_0
                        $value .= "'" . $maitiereId . "', "; // ZQUEST_YMATIERE_0
                        $value .= "'" . $zquest_ymat_0 . "', "; // ZQUEST_YMAT_0
                        $value .= "'" . $zquest_ymat_1 . "', "; // ZQUEST_YMAT_1
                        $value .= "'" . $zquest_ymat_2 . "', "; // ZQUEST_YMAT_2
                        $value .= "'" . $zquest_ymat_3 . "', "; // ZQUEST_YMAT_3
                        $value .= "'" . $zquest_ymat_4 . "', "; // ZQUEST_YMAT_4
                        $value .= "'" . $compId . "', ";        // ZQUEST_ZCOMPETENC_0
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
                        $query  = " INSERT INTO " . CONST_DB_TABLE_QUESTTEMP;
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
                        $query .= implode(', ', $queryBloc);
                    }
                    break;
            }
        }

        // execution requete
        if (!empty($query)) {
            if ($result = $this->adapter->execute($query)) {
                // update cri_question.transmis_erp
                $sql = " UPDATE {$this->table} SET transmis_erp = 1 WHERE id IN (" . implode(', ', $qList) . ")";
                $this->wpdb->query($sql);
            } else {
                // log erreur
            }
        }

//        echo '<pre>'; die(print_r($questions));
        return $questions;
    }
}
