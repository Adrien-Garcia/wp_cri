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
}
