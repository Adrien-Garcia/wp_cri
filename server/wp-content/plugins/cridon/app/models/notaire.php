<?php

/**
 * Class Notaire
 * @author Etech
 * @contributor Joelio
 */
class Notaire extends MvcModel
{

    /**
     * @var string
     */
    const UPDATE_ACTION = 'update';

    /**
     * @var string
     */
    const IMPORT_CSV_OPTION = 'csv';

    /**
     * @var string
     */
    const IMPORT_ODBC_OPTION = 'odbc';

    /**
     * @var string
     */
    public $display_field = 'first_name';

    /**
     * @var string
     */
    public $table = '{prefix}notaire';

    /**
     * @var array
     */
    public $has_many = array(
        'Question' => array(
            'foreign_key' => 'client_number'
        )
    );

    /**
     * @var array
     */
    public $belongs_to = array(
        'User' => array(
            'class'       => 'MvcUser',
            'foreign_key' => 'ID'
        )
    );

    /**
     * @var mixed
     */
    protected $wpdb;

    /**
     * @var array
     */
    private $csvData = array();

    /**
     * @var CridonCsvParser
     */
    private $csvParser;

    /**
     * @var array : list of existing notaire on Site
     */
    private $siteNotaireList = array();

    /**
     * @var array : list of notaire in ERP
     */
    private $erpNotaireList = array();

    /**
     * @var array : list of notaire data from ERP
     */
    private $erpNotaireData = array();

    /**
     * @var bool : File import success flag
     */
    private $importSuccess = false;

    /**
     * @var mixed
     */
    private $adapter;

    /**
     * @var string
     */
    protected $logs;

    /**
     * Constructor
     */
    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->csvParser = new CridonCsvParser();

        parent::__construct();
    }

    /**
     * Action for importing notaire data into wp_users
     *
     * @return mixed
     */
    public function importIntoWpUsers()
    {
        // init logs
        $this->logs = array();

        switch (strtolower(CONST_IMPORT_OPTION)) {
            case self::IMPORT_CSV_OPTION:
                $this->adapter = new CridonCsvParser();
                $this->importFromCsvFile();
                break;
            default : // odbc option by default
                $this->adapter = CridonODBCAdapter::getInstance();
                $this->importDataUsingODBC();
                break;
        }

        return $this->logs;
    }

    /**
     * Action for importing data using CSV file
     */
    public function importFromCsvFile()
    {
        // get csv file
        $files = glob(CONST_IMPORT_CSV_NOTAIRE_FILE_PATH . '/*.csv');

        // check if file exist
        if (isset($files[0]) && is_file($files[0])) {

            $this->csvParser->enclosure = '';
            $this->csvParser->encoding(null, 'UTF-8');
            $this->csvParser->auto($files[0]);

            // no error was found
            if (property_exists($this->csvParser, 'data') && intval($this->csvParser->error) <= 0) {

                // Csv data setter
                $this->setCsvData($this->csvParser->data);

                // prepare data
                $this->prepareNotairedata();

                // insert or update data
                $this->manageNotaireData();

                // do archive
                if ($this->importSuccess) {
//                    rename($files[0], str_replace(".csv", ".csv." . date('YmdHi'), $files[0]));
                }
            }
        }
    }

    /**
     * Import data with ODBC Link
     *
     * @throws Exception
     */
    private function importDataUsingODBC()
    {
        try {
            // query
            $sql = 'SELECT * FROM ' . CONST_ODBC_TABLE_NOTAIRE;

            // exec query
            CridonODBCAdapter::getInstance()->getResults($sql);

            // prepare data
            CridonODBCAdapter::getInstance()->prepareODBCData();

            $this->erpNotaireList = CridonODBCAdapter::getInstance()->erpNotaireList;
            $this->erpNotaireData = CridonODBCAdapter::getInstance()->erpNotaireData;

            // set list of existing notaire
            $this->setSiteNotaireList();

            // insert or update data
            $this->manageNotaireData();

            // Close Connection
            CridonODBCAdapter::getInstance()->closeConnection();

        } catch (\Exception $e) {
            throw new \Exception ($e->getMessage());
        }
    }

    /**
     * Prepare data for listing existing notaire on Site and new from ERP
     */
    private function prepareNotairedata()
    {
        // set list of existing notaire
        $this->setSiteNotaireList();

        // instance of CridonCsvParser
        $csv = $this->csvParser;

        // fill list of notaire from ERP (crpcen + passwd)
        foreach ($this->getCsvData() as $items) {
            // only notaire having CRPCEN
            if (isset($items[$csv::NOTAIRE_CRPCEN]) && $items[$csv::NOTAIRE_CRPCEN]) {
                // the only unique key available is the "crpcen + web_password"
                $uniqueKey = intval($items[$csv::NOTAIRE_CRPCEN]) . $items[$csv::NOTAIRE_PWDWEB];
                array_push($this->erpNotaireList, $uniqueKey);

                // notaire data filter
                $this->erpNotaireData[$uniqueKey] = $items;
            }
        }
    }

    /**
     * List of existing notaire on Site
     */
    private function setSiteNotaireList()
    {
        // get list of existing notaire
        $notaires = $this->find();


        // fill list of existing notaire on site with unique key (crpcen + passwd)
        foreach ($notaires as $notaire) {
            array_push($this->siteNotaireList, $notaire->crpcen . $notaire->web_password);
        }
    }

    /**
     * Get list of new notaire list
     *
     * @return array
     */
    private function getNewNotaireList()
    {
        return array_diff($this->erpNotaireList, $this->siteNotaireList);
    }

    /**
     * List of notaire to be updated from Site (intersect of Site and ERP)
     *
     * @return array
     */
    private function getNotaireToBeUpdated()
    {
        // common values between Site and ERP
        $items = array_intersect($this->siteNotaireList, $this->erpNotaireList);

        // return filtered items with associated data from ERP
        return array_intersect_key($this->erpNotaireData, array_flip($items));
    }

    /**
     * List of notaire to be deleted from Site (notaire not found in ERP)
     *
     * @return array
     */
    private function getNotaireToBeDeleted()
    {
        return array_diff($this->siteNotaireList, $this->erpNotaireList);
    }

    /**
     * Manage Notaire data (insert, update)
     */
    private function manageNotaireData()
    {
        try {
            // instance of adapter
            $adapter = $this->adapter;

            // init list of values to be inserted
            $insertValues = array();

            // init list of values to be updated
            $updateCategValues = $updateNumclientValues = $updateFirstnameValues = $updatePwdtelValues = $updateInterCodeValues = array();
            $updateCivlitValues = $updateLastnameValues = $updateEmailValues = $updateFoncValues = $updateDateModified = array();
            $updateTelValues = $updateFaxValues = $updateMobileValues = array();

            // list of new data
            $newNotaires = $this->getNewNotaireList();

            // list of data for update
            $updateNotaireList = $this->getNotaireToBeUpdated();

            // update
            if (count($updateNotaireList) > 0) {
                // start/end query block
                $queryStart = " UPDATE `{$this->table}` ";
                $queryEnd   = ' END ';

                // only update if erpData.date_modified > cri_notaire.date_modified
                foreach($this->find() as $currentData) {
                    // the only unique key available is the "crpcen + web_password"
                    $key = $currentData->crpcen . $currentData->web_password;

                    // start optimisation
                    if (array_key_exists($key, $updateNotaireList)) {
                        $newData = $updateNotaireList[$key];
                        // change date format (original "d/m/Y" with double quote)
                        $dateModified = '0000-00-00';
                        if (isset($newData[$adapter::NOTAIRE_DATEMODIF])) {
                            $dateModified = date("Y-m-d",
                                                 strtotime(
                                                     str_replace(
                                                         array('/', '"'),
                                                         array('-', ''),
                                                         $newData[$adapter::NOTAIRE_DATEMODIF]
                                                     )
                                                 )
                            );
                        }
                        $newDate = new DateTime($dateModified);
                        $newDate = $newDate->format('Ymd');
                        $oldDate = new DateTime($currentData->date_modified);
                        $oldDate = $oldDate->format('Ymd');
                        if ($newDate > $oldDate) {
                            // prepare all update   query
                            if (isset($newData[$adapter::NOTAIRE_CATEG]))
                                $updateCategValues[]        = " id = {$currentData->id} THEN '" . mysql_real_escape_string($newData[$adapter::NOTAIRE_CATEG]) . "' ";

                            if (isset($newData[$adapter::NOTAIRE_NUMCLIENT]))
                                $updateNumclientValues[]    = " id = {$currentData->id} THEN '" . mysql_real_escape_string($newData[$adapter::NOTAIRE_NUMCLIENT]) . "' ";

                            if (isset($newData[$adapter::NOTAIRE_FNAME]))
                                $updateFirstnameValues[]    = " id = {$currentData->id} THEN '" . mysql_real_escape_string($newData[$adapter::NOTAIRE_FNAME]) . "' ";

                            if (isset($newData[$adapter::NOTAIRE_LNAME]))
                                $updateLastnameValues[]     = " id = {$currentData->id} THEN '" . mysql_real_escape_string($newData[$adapter::NOTAIRE_LNAME]) . "' ";

                            if (isset($newData[$adapter::NOTAIRE_PWDTEL]))
                                $updatePwdtelValues[]       = " id = {$currentData->id} THEN '" . mysql_real_escape_string($newData[$adapter::NOTAIRE_PWDTEL]) . "' ";

                            if (isset($newData[$adapter::NOTAIRE_INTERCODE]))
                                $updateInterCodeValues[]    = " id = {$currentData->id} THEN '" . mysql_real_escape_string(intval($newData[$adapter::NOTAIRE_INTERCODE])) . "' ";

                            if (isset($newData[$adapter::NOTAIRE_CIVILIT]))
                                $updateCivlitValues[]       = " id = {$currentData->id} THEN '" . mysql_real_escape_string($newData[$adapter::NOTAIRE_CIVILIT]) . "' ";

                            if (isset($newData[$adapter::NOTAIRE_EMAIL]))
                                $updateEmailValues[]        = " id = {$currentData->id} THEN '" . mysql_real_escape_string($newData[$adapter::NOTAIRE_EMAIL]) . "' ";

                            if (isset($newData[$adapter::NOTAIRE_FONC]))
                                $updateFoncValues[]         = " id = {$currentData->id} THEN '" . mysql_real_escape_string($newData[$adapter::NOTAIRE_FONC]) . "' ";

                            if (isset($newData[$adapter::NOTAIRE_TEL]))
                                $updateTelValues[]         = " id = {$currentData->id} THEN '" . mysql_real_escape_string($newData[$adapter::NOTAIRE_TEL]) . "' ";

                            if (isset($newData[$adapter::NOTAIRE_FAX]))
                                $updateFaxValues[]         = " id = {$currentData->id} THEN '" . mysql_real_escape_string($newData[$adapter::NOTAIRE_FAX]) . "' ";

                            if (isset($newData[$adapter::NOTAIRE_PORTABLE]))
                                $updateMobileValues[]      = " id = {$currentData->id} THEN '" . mysql_real_escape_string($newData[$adapter::NOTAIRE_PORTABLE]) . "' ";

                            $updateDateModified[]          = " id = {$currentData->id} THEN '" . $dateModified . "' ";
                        }
                    }
                    // end optimisation
                }

                // execute update query
                if (count($updateCategValues) > 0) {
                    // category
                    $notaireQuery = ' SET `category` = CASE ';
                    $notaireQuery .= ' WHEN ' . implode(' WHEN ', $updateCategValues);
                    $notaireQuery .= ' ELSE `category` ';
                    $this->wpdb->query($queryStart . $notaireQuery . $queryEnd);
                    // client_number
                    $notaireQuery = ' SET `client_number` = CASE ';
                    $notaireQuery .= ' WHEN ' . implode(' WHEN ', $updateNumclientValues);
                    $notaireQuery .= ' ELSE `client_number` ';
                    $this->wpdb->query($queryStart . $notaireQuery . $queryEnd);
                    // first_name
                    $notaireQuery = ' SET `first_name` = CASE ';
                    $notaireQuery .= ' WHEN ' . implode(' WHEN ', $updateFirstnameValues);
                    $notaireQuery .= ' ELSE `first_name` ';
                    $this->wpdb->query($queryStart . $notaireQuery . $queryEnd);
                    // last_name
                    $notaireQuery = ' SET `last_name` = CASE ';
                    $notaireQuery .= ' WHEN ' . implode(' WHEN ', $updateLastnameValues);
                    $notaireQuery .= ' ELSE `last_name` ';
                    $this->wpdb->query($queryStart . $notaireQuery . $queryEnd);
                    // tel_password
                    $notaireQuery = ' SET `tel_password` = CASE ';
                    $notaireQuery .= ' WHEN ' . implode(' WHEN ', $updatePwdtelValues);
                    $notaireQuery .= ' ELSE `tel_password` ';
                    $this->wpdb->query($queryStart . $notaireQuery . $queryEnd);
                    // code_interlocuteur
                    $notaireQuery = ' SET `code_interlocuteur` = CASE ';
                    $notaireQuery .= ' WHEN ' . implode(' WHEN ', $updateInterCodeValues);
                    $notaireQuery .= ' ELSE `code_interlocuteur` ';
                    $this->wpdb->query($queryStart . $notaireQuery . $queryEnd);
                    // id_civilite
                    $notaireQuery = ' SET `id_civilite` = CASE ';
                    $notaireQuery .= ' WHEN ' . implode(' WHEN ', $updateCivlitValues);
                    $notaireQuery .= ' ELSE `id_civilite` ';
                    $this->wpdb->query($queryStart . $notaireQuery . $queryEnd);
                    // email_adress
                    $notaireQuery = ' SET `email_adress` = CASE ';
                    $notaireQuery .= ' WHEN ' . implode(' WHEN ', $updateEmailValues);
                    $notaireQuery .= ' ELSE `email_adress` ';
                    $this->wpdb->query($queryStart . $notaireQuery . $queryEnd);
                    // id_fonction
                    $notaireQuery = ' SET `id_fonction` = CASE ';
                    $notaireQuery .= ' WHEN ' . implode(' WHEN ', $updateFoncValues);
                    $notaireQuery .= ' ELSE `id_fonction` ';
                    $this->wpdb->query($queryStart . $notaireQuery . $queryEnd);
                    // tel
                    $notaireQuery = ' SET `tel` = CASE ';
                    $notaireQuery .= ' WHEN ' . implode(' WHEN ', $updateTelValues);
                    $notaireQuery .= ' ELSE `tel` ';
                    $this->wpdb->query($queryStart . $notaireQuery . $queryEnd);
                    // fax
                    $notaireQuery = ' SET `fax` = CASE ';
                    $notaireQuery .= ' WHEN ' . implode(' WHEN ', $updateFaxValues);
                    $notaireQuery .= ' ELSE `fax` ';
                    $this->wpdb->query($queryStart . $notaireQuery . $queryEnd);
                    // tel_portable
                    $notaireQuery = ' SET `tel_portable` = CASE ';
                    $notaireQuery .= ' WHEN ' . implode(' WHEN ', $updateMobileValues);
                    $notaireQuery .= ' ELSE `tel_portable` ';
                    $this->wpdb->query($queryStart . $notaireQuery . $queryEnd);
                    // date_modified
                    $notaireQuery = ' SET `date_modified` = CASE ';
                    $notaireQuery .= ' WHEN ' . implode(' WHEN ', $updateDateModified);
                    $notaireQuery .= ' ELSE `date_modified` ';
                    $this->wpdb->query($queryStart . $notaireQuery . $queryEnd);

                    $this->importSuccess = true;
                }
            }

            // insert new data
            if (count($newNotaires) > 0) {
                $options               = array();
                $options['table']      = 'notaire';
                $options['attributes'] = 'category, client_number, first_name, last_name, crpcen, web_password, tel_password, code_interlocuteur, ';
                $options['attributes'] .= 'id_civilite, email_adress, id_fonction, tel, fax, tel_portable, date_modified';

                // prepare multi rows data values
                foreach ($newNotaires as $notaire) {
                    // do not import DIV category
                    // @see https://trello.com/c/P81yRyRM/21-s-43-import-des-notaires-et-creation-des-etudes-il-y-a-deux-notaires-avec-le-meme-crpcen-qui-n-ont-pas-les-memes-infos-pour-l-et
                    if (isset($this->erpNotaireData[$notaire][$adapter::NOTAIRE_CATEG])
                        && strtolower($this->erpNotaireData[$notaire][$adapter::NOTAIRE_CATEG]) != CONST_CLIENTDIVERS_ROLE) {

                        // format date
                        $dateModified = '0000-00-00';
                        if (isset($this->erpNotaireData[$notaire][$adapter::NOTAIRE_DATEMODIF])) {
                            $dateModified = date("Y-m-d",
                                                 strtotime(
                                                     str_replace(
                                                         array('/', '"'),
                                                         array('-', ''),
                                                         $this->erpNotaireData[$notaire][$adapter::NOTAIRE_DATEMODIF]
                                                     )
                                                 )
                            );
                        }

                        $value = "(";

                        $value .= "'" . (isset($this->erpNotaireData[$notaire][$adapter::NOTAIRE_CATEG]) ? mysql_real_escape_string($this->erpNotaireData[$notaire][$adapter::NOTAIRE_CATEG]) : '') . "', ";
                        $value .= "'" . (isset($this->erpNotaireData[$notaire][$adapter::NOTAIRE_NUMCLIENT]) ? mysql_real_escape_string($this->erpNotaireData[$notaire][$adapter::NOTAIRE_NUMCLIENT]) : '') . "', ";
                        $value .= "'" . (isset($this->erpNotaireData[$notaire][$adapter::NOTAIRE_FNAME]) ? mysql_real_escape_string($this->erpNotaireData[$notaire][$adapter::NOTAIRE_FNAME]) : '') . "', ";
                        $value .= "'" . (isset($this->erpNotaireData[$notaire][$adapter::NOTAIRE_LNAME]) ? mysql_real_escape_string($this->erpNotaireData[$notaire][$adapter::NOTAIRE_LNAME]) : '') . "', ";
                        $value .= "'" . (isset($this->erpNotaireData[$notaire][$adapter::NOTAIRE_CRPCEN]) ? mysql_real_escape_string(intval($this->erpNotaireData[$notaire][$adapter::NOTAIRE_CRPCEN])) : '') . "', ";
                        $value .= "'" . (isset($this->erpNotaireData[$notaire][$adapter::NOTAIRE_PWDWEB]) ? mysql_real_escape_string($this->erpNotaireData[$notaire][$adapter::NOTAIRE_PWDWEB]) : '') . "', ";
                        $value .= "'" . (isset($this->erpNotaireData[$notaire][$adapter::NOTAIRE_PWDTEL]) ? mysql_real_escape_string($this->erpNotaireData[$notaire][$adapter::NOTAIRE_PWDTEL]) : '') . "', ";
                        $value .= "'" . (isset($this->erpNotaireData[$notaire][$adapter::NOTAIRE_INTERCODE]) ? mysql_real_escape_string(intval($this->erpNotaireData[$notaire][$adapter::NOTAIRE_INTERCODE])) : '') . "', ";
                        $value .= (isset($this->erpNotaireData[$notaire][$adapter::NOTAIRE_CIVILIT]) ? mysql_real_escape_string($this->erpNotaireData[$notaire][$adapter::NOTAIRE_CIVILIT]) : '') . ", ";
                        $value .= "'" . (isset($this->erpNotaireData[$notaire][$adapter::NOTAIRE_EMAIL]) ? mysql_real_escape_string($this->erpNotaireData[$notaire][$adapter::NOTAIRE_EMAIL]) : '') . "', ";
                        $value .= (isset($this->erpNotaireData[$notaire][$adapter::NOTAIRE_FONC]) ? mysql_real_escape_string($this->erpNotaireData[$notaire][$adapter::NOTAIRE_FONC]) : '') . ", ";
                        $value .= "'" . (isset($this->erpNotaireData[$notaire][$adapter::NOTAIRE_TEL]) ? mysql_real_escape_string($this->erpNotaireData[$notaire][$adapter::NOTAIRE_TEL]) : '') . "', ";
                        $value .= "'" . (isset($this->erpNotaireData[$notaire][$adapter::NOTAIRE_FAX]) ? mysql_real_escape_string($this->erpNotaireData[$notaire][$adapter::NOTAIRE_FAX]) : '') . "', ";
                        $value .= "'" . (isset($this->erpNotaireData[$notaire][$adapter::NOTAIRE_PORTABLE]) ? mysql_real_escape_string($this->erpNotaireData[$notaire][$adapter::NOTAIRE_PORTABLE]) : '') . "', ";
                        $value .= "'" . $dateModified . "'";

                        $value .= ")";

                        $insertValues[] = $value;
                    }
                }

                if (count($insertValues) > 0) {
                    $queryBulder       = mvc_model('QueryBuilder');
                    $options['values'] = implode(', ', $insertValues);
                    // bulk insert
                    $queryBulder->insertMultiRows($options);

                    $this->importSuccess = true;
                }
            }

        } catch (Exception $e) {
            echo 'Exception reçue : ' .  $e->getMessage() . "\n";
        }

        // import into wp_users table
        $this->insertOrUpdateWpUsers();
    }

    /**
     * Set notaire role
     */
    public function setNotaireRole()
    {
        // block query
        $roleQuery             = $roleUpdateQuery = array();
        $options               = array();
        $options['table']      = 'usermeta';
        $options['attributes'] = 'user_id, meta_key, meta_value';

        // instance of cridonTools
        $cridonTools = new CridonTools();

        // existing user roles
        $users = $cridonTools->getExistingUserRoles();

        foreach ($this->find() as $notaire) {
            // role by group
            // if not is set the category, use default role
            $role = ($notaire->category) ? strtolower($notaire->category) : CONST_NOTAIRE_ROLE;

            // recognized role format by WP
            $roleValue = serialize(
                array($role => true)
            );
            // prepare query
            if ($notaire->id_wp_user) {
                if (!in_array($notaire->id_wp_user, $users)) { // insert query
                    $value = "(";
                    $value .= "'" . $notaire->id_wp_user . "', ";
                    $value .= "'" . $this->wpdb->prefix . "capabilities', ";
                    $value .= "'" . $roleValue . "'";
                    $value .= ")";

                    $roleQuery[] = $value;
                } else { // update query
                    $roleUpdateQuery[] = " user_id = {$notaire->id_wp_user} AND meta_key = '{$this->wpdb->prefix}capabilities' THEN '{$roleValue}' ";
                }

            }
        }

        // execute prepared insert query
        if (count($roleQuery) > 0) {
            $queryBulder       = mvc_model('QueryBuilder');
            $options['values'] = implode(', ', $roleQuery);
            // bulk insert
            $queryBulder->insertMultiRows($options);
        }

        // execute prepared update query
        if (count($roleUpdateQuery) > 0) {
            // start/end query block
            $queryStart = " UPDATE `{$this->wpdb->prefix}usermeta` ";
            $queryEnd   = ' END ';

            // query
            $query = ' SET `meta_value` = CASE ';
            $query .= ' WHEN ' . implode(' WHEN ', $roleUpdateQuery);
            $query .= ' ELSE `meta_value` ';

            $this->wpdb->query($queryStart . $query . $queryEnd);
        }
    }

    /**
     * Action for importing data from cri_notaire into wp_users
     */
    private function insertOrUpdateWpUsers()
    {
        try {
            $this->logs = array();
            $notaires   = $this->find();

            if (count($notaires) > 0) {
                // adapter
                $adapter = $this->adapter;

                // list of values to be inserted
                $insertValues = array();

                // instance of cridon tools
                $criTools = new CridonTools();

                // bulk update separate
                // @TODO to be completed with other field to be updated
                // it's concerned only a specific data in wp_users
                $bulkPwdUpdate = $bulkNiceNameUpdate = $bulkStatusUpdate = $bulkEmailUpdate = $bulkDisplayNameUpdate = array();

                // query builder options
                $options               = array();
                $options['table']      = 'users';
                $options['attributes'] = 'user_login, user_pass, user_nicename, user_email, user_registered, user_status,  display_name';

                // list of existing users
                $users = $criTools->getWpUsers();

                foreach ($notaires as $notaire) {
                    // unique key
                    $uniqueKey = $notaire->crpcen . $notaire->web_password;

                    // check if user already exist
                    $userName = $notaire->crpcen . CONST_LOGIN_SEPARATOR . $notaire->id;

                    $displayName = $notaire->first_name . ' ' . $notaire->last_name;

                    // set user status
                    $userStatus = (isset($this->erpNotaireData[$uniqueKey][$adapter::NOTAIRE_STATUS])) ?
                        $this->erpNotaireData[$uniqueKey][$adapter::NOTAIRE_STATUS] : CONST_STATUS_DISABLED;

                    if (!in_array($userName, $users['username'])) { // prepare the bulk insert query
                        $value = "(";

                        $value .= "'" . mysql_real_escape_string($userName) . "', ";
                        $value .= "'" . wp_hash_password($notaire->web_password) . "', ";
                        $value .= "'" . sanitize_title($displayName) . "', ";
                        $value .= "'" . $notaire->email_adress . "', ";
                        $value .= "'" . date('Y-m-d H:i:s') . "', ";
                        $value .= $userStatus . ", ";
                        $value .= "'" . mysql_real_escape_string($displayName) . "'";

                        $value .= ")";

                        $insertValues[] = $value;

                    } else { // prepare the bulk update query
                        if ($notaire->id_wp_user) {
                            // pwd
                            $bulkPwdUpdate[] = " ID = {$notaire->id_wp_user} THEN '" . wp_hash_password($notaire->web_password) . "' ";
                            // nicename
                            $bulkNiceNameUpdate[] = " ID = {$notaire->id_wp_user} THEN '" . sanitize_title($displayName) . "' ";
                            // status
                            $bulkStatusUpdate[] = " ID = {$notaire->id_wp_user} THEN " . $userStatus . " ";
                            // email
                            $bulkEmailUpdate[] = " ID = {$notaire->id_wp_user} THEN '" . $notaire->email_adress . "' ";
                            // display name
                            $bulkDisplayNameUpdate[] = " ID = {$notaire->id_wp_user} THEN '" . mysql_real_escape_string($displayName) . "' ";
                        }
                    }
                }

                // execute the bulk insert query
                if (count($insertValues) > 0) {
                    $queryBulder       = mvc_model('QueryBuilder');
                    $options['values'] = implode(', ', $insertValues);
                    // bulk insert
                    $queryBulder->insertMultiRows($options);

                    // update cri_notaire.id_wp_user
                    $this->updateCriNotaireWpUserId($notaires);

                    $this->importSuccess = true;
                }

                // execute the bulk update query
                if (count($bulkPwdUpdate) > 0) {
                    // start/end query block
                    $queryStart = " UPDATE `{$this->wpdb->users}` ";
                    $queryEnd   = ' END ';

                    // pwd
                    $queryPwd = ' SET `user_pass` = CASE ';
                    $queryPwd .= ' WHEN ' . implode(' WHEN ', $bulkPwdUpdate);
                    $queryPwd .= ' ELSE `user_pass` ';
                    $this->wpdb->query($queryStart . $queryPwd . $queryEnd);

                    // nicename
                    $queryNicename = ' SET `user_nicename` = CASE ';
                    $queryNicename .= ' WHEN ' . implode(' WHEN ', $bulkNiceNameUpdate);
                    $queryNicename .= ' ELSE `user_nicename` ';
                    $this->wpdb->query($queryStart . $queryNicename . $queryEnd);

                    // status
                    $queryStatus = ' SET `user_status` = CASE ';
                    $queryStatus .= ' WHEN ' . implode(' WHEN ', $bulkStatusUpdate);
                    $queryStatus .= ' ELSE `user_status` ';
                    $this->wpdb->query($queryStart . $queryStatus . $queryEnd);

                    // email
                    $queryEmail = ' SET `user_email` = CASE ';
                    $queryEmail .= ' WHEN ' . implode(' WHEN ', $bulkEmailUpdate);
                    $queryEmail .= ' ELSE `user_email` ';
                    $this->wpdb->query($queryStart . $queryEmail . $queryEnd);

                    // display name
                    $queryDisplayName = ' SET `display_name` = CASE ';
                    $queryDisplayName .= ' WHEN ' . implode(' WHEN ', $bulkDisplayNameUpdate);
                    $queryDisplayName .= ' ELSE `display_name` ';
                    $this->wpdb->query($queryStart . $queryDisplayName . $queryEnd);

                    $this->importSuccess = true;
                }

                // set notaire role
                // should be execute after cri_notaire.id_wp_user was set
                $this->setNotaireRole();
            }
        } catch(Exception $e) {
            echo 'Exception reçue : ' .  $e->getMessage() . "\n";
        }
    }

    /**
     * Update id_wp_user in cri_notaire
     *
     * @param array $notaires
     */
    private function updateCriNotaireWpUserId($notaires)
    {
        try {
            // update
            $updateValues = array();

            // parse all notaire and prepare bulk query
            foreach ($notaires as $notaire) {
                $update = "`{$this->table}`.`crpcen` = '" . $notaire->crpcen . "' AND `id` = " . $notaire->id;
                $update .= " THEN (SELECT `{$this->wpdb->users}`.ID FROM `{$this->wpdb->users}` WHERE `user_login` = CONCAT('" . $notaire->crpcen . "', '~', '" . $notaire->id . "'))";

                $updateValues[] = $update;
            }

            // execute prepared query
            if (count($updateValues) > 0) {
                $query = " UPDATE `{$this->table}` ";
                $query .= ' SET `id_wp_user` = CASE ';
                $query .= ' WHEN ' . implode(' WHEN ', $updateValues);
                $query .= ' ELSE `id_wp_user` ';
                $query .= ' END ';
                $this->wpdb->query($query);
            }
        } catch (Exception $e) {
            echo 'Exception reçue : ' .  $e->getMessage() . "\n";
        }
    }

    /**
     * Hook for sanitize_user
     *
     * @param mixed $user
     * @param mixed $raw_user
     * @param boolean $strict
     *
     * @return mixed
     */
    public function custom_sanitize_user($user, $raw_user, $strict)
    {
        return $raw_user;
    }

    /**
     * Csv data getter
     *
     * @return array
     */
    public function getCsvData()
    {
        return $this->csvData;
    }

    /**
     * Csv data setter
     *
     * @param array $csvData
     */
    public function setCsvData($csvData)
    {
        $this->csvData = $csvData;
    }

}