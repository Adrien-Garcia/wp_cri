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
    const IMPORT_OCI_OPTION = 'oci';

    /**
     * @var string
     */
    public $display_field = 'first_name';

    /**
     * @var string
     */
    public $table = '{prefix}notaire';

    /**
     * @var string
     */
    protected $logs;

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
    public $has_and_belongs_to_many = array(
        'Matiere' => array(
            'foreign_key' => 'id_notaire',
            'association_foreign_key' => 'id_matiere',
            'join_table' => '{prefix}matiere_notaire',
            'fields' => array('id','label','code','short_label','displayed','picto')
        )
    );
    /**
     *
     * @var array
     */
    var $includes   = array('Etude','Civilite','Fonction','Matiere');
    /**
     * @var array
     */
    public $belongs_to = array(
        'User' => array(
            'class'       => 'MvcUser',
            'foreign_key' => 'ID'
        ),
        'Etude' => array(
            'foreign_key' => 'crpcen'
        ),
        'Civilite' => array(
            'foreign_key' => 'id_civilite'
        ),
        'Fonction' => array(
            'foreign_key' => 'id_fonction'
        )
    );

    /**
     * @var mixed
     */
    protected $wpdb;

    /**
     * @var array
     */
    protected $csvData = array();

    /**
     * @var CridonCsvParser
     */
    protected $csvParser;

    /**
     * @var array : list of existing notaire on Site
     */
    protected $siteNotaireList = array();

    /**
     * @var array : list of notaire in ERP
     */
    protected $erpNotaireList = array();

    /**
     * @var array : list of notaire data from ERP
     */
    protected $erpNotaireData = array();

    /**
     * @var CridonSoldeParser
     */
    protected $soldeParser;

    /**
     * @var array : list of existing solde on Site
     */
    protected $siteSoldeList = array();

    /**
     * @var array : list of solde in ERP
     */
    protected $erpSoldeList = array();

    /**
     * @var array : list of solde data from ERP
     */
    protected $erpSoldeData = array();

    /**
     * @var array
     */
    protected $soldeData = array();

    /**
     * @var mixed : solde wpmvc model
     */
    protected $soldeModel;

    /**
     * @var bool : File import success flag
     */
    protected $importSuccess = false;

    /**
     * @var DBConnect
     */
    protected $adapter;

    /**
     * Constructor
     */
    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->csvParser = new CridonCsvParser();
        $this->soldeParser = new CridonSoldeParser();
        $this->soldeModel  = mvc_model('solde');

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
        $this->adapter = null;

        // try to import and prevent exception to announce a false positive result
        try {
            switch (strtolower(CONST_IMPORT_OPTION)) {
                case self::IMPORT_CSV_OPTION:
                    $this->adapter = new CridonCsvParser();
                    $this->importFromCsvFile();
                    break;
                case self::IMPORT_ODBC_OPTION:
                    $this->adapter = CridonODBCAdapter::getInstance();
                case self::IMPORT_OCI_OPTION:
                    //if case above did not match, set OCI
                    $this->adapter = empty($this->adapter) ? CridonOCIAdapter::getInstance() : $this->adapter;
                default :
                    //both OCI and ODBC will can this
                    $this->importDataUsingDBconnect();
                    break;
            }
        } catch (Exception $e) {
            // write into logfile
            errorLog('Import notaire', $e->getMessage());
            array_push($this->logs['error'], $e->getMessage());
        }

        echo json_encode($this->logs);
        exit();
    }

    /**
     * Action for importing data using CSV file
     *
     * @return void
     */
    public function importFromCsvFile()
    {
        // get csv file
        $files = glob(CONST_IMPORT_CSV_NOTAIRE_FILE_PATH . '/*.csv');

        try {

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
                        rename($files[0], str_replace(".csv", ".csv." . date('YmdHi'), $files[0]));
                    }
                } else { // file content error
                    // write into logfile
                    $error = sprintf(CONST_EMAIL_ERROR_CORRUPTED_FILE, 'Solde');
                    errorLog(CONST_EMAIL_ERROR_SUBJECT, $error);

                    // send email
                    reportError(CONST_EMAIL_ERROR_CORRUPTED_FILE, 'Notaire');
                }
            } else { // file doesn't exist
                // write into logfile
                $error = sprintf(CONST_EMAIL_ERROR_CONTENT, 'Solde');
                errorLog(CONST_EMAIL_ERROR_SUBJECT, $error);

                // send email
                reportError(CONST_EMAIL_ERROR_CONTENT, 'Notaire');
            }
        } catch (Exception $e) {
            // archive file
            if (isset($files[0]) && is_file($files[0])) {
                rename($files[0], str_replace(".csv", ".csv." . date('YmdHi'), $files[0]));
            }

            // write write into logfile
            errorLog(CONST_EMAIL_ERROR_SUBJECT, $e->getMessage());

            // send email
            reportError(CONST_EMAIL_ERROR_CATCH_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * Import data with ODBC Link
     *
     * @throws Exception
     *
     * @return void
     */
    protected function importDataUsingDBconnect()
    {
        try {
            // query
            $sql = 'SELECT * FROM ' . CONST_DB_TABLE_NOTAIRE;

            // exec query
            $this->adapter->getResults($sql);

            // prepare data
            $this->adapter->prepareData();

            $this->erpNotaireList = $this->adapter->erpNotaireList;
            $this->erpNotaireData = $this->adapter->erpNotaireData;

            // set list of existing notaire
            $this->setSiteNotaireList();

            // insert or update data
            $this->manageNotaireData();

            // Close Connection
            $this->adapter->closeConnection();

        } catch (\Exception $e) {
            // write into logfile
            errorLog(CONST_EMAIL_ERROR_SUBJECT, $e->getMessage());
            // send email
            reportError(CONST_EMAIL_ERROR_CATCH_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * Prepare data for listing existing notaire on Site and new from ERP
     *
     * @return void
     */
    protected function prepareNotairedata()
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
     *
     * @return void
     */
    protected function setSiteNotaireList()
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
    protected function getNewNotaireList()
    {
        return array_diff($this->erpNotaireList, $this->siteNotaireList);
    }

    /**
     * List of notaire to be updated from Site (intersect of Site and ERP)
     *
     * @return array
     */
    protected function getNotaireToBeUpdated()
    {
        // common values between Site and ERP
        $items = array_intersect($this->siteNotaireList, $this->erpNotaireList);

        // return filtered items with associated data from ERP
        return array_intersect_key($this->erpNotaireData, array_flip($items));
    }

    /**
     * Manage Notaire data (insert, update)
     *
     * @return void
     */
    protected function manageNotaireData()
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
                                $updateCategValues[]        = " id = {$currentData->id} THEN '" . esc_sql($newData[$adapter::NOTAIRE_CATEG]) . "' ";

                            if (isset($newData[$adapter::NOTAIRE_NUMCLIENT]))
                                $updateNumclientValues[]    = " id = {$currentData->id} THEN '" . esc_sql($newData[$adapter::NOTAIRE_NUMCLIENT]) . "' ";

                            if (isset($newData[$adapter::NOTAIRE_FNAME]))
                                $updateFirstnameValues[]    = " id = {$currentData->id} THEN '" . esc_sql($newData[$adapter::NOTAIRE_FNAME]) . "' ";

                            if (isset($newData[$adapter::NOTAIRE_LNAME]))
                                $updateLastnameValues[]     = " id = {$currentData->id} THEN '" . esc_sql($newData[$adapter::NOTAIRE_LNAME]) . "' ";

                            if (isset($newData[$adapter::NOTAIRE_PWDTEL]))
                                $updatePwdtelValues[]       = " id = {$currentData->id} THEN '" . esc_sql($newData[$adapter::NOTAIRE_PWDTEL]) . "' ";

                            if (isset($newData[$adapter::NOTAIRE_INTERCODE]))
                                $updateInterCodeValues[]    = " id = {$currentData->id} THEN '" . esc_sql(intval($newData[$adapter::NOTAIRE_INTERCODE])) . "' ";

                            if (isset($newData[$adapter::NOTAIRE_CIVILIT]))
                                $updateCivlitValues[]       = " id = {$currentData->id} THEN '" . esc_sql($newData[$adapter::NOTAIRE_CIVILIT]) . "' ";

                            if (isset($newData[$adapter::NOTAIRE_EMAIL]))
                                $updateEmailValues[]        = " id = {$currentData->id} THEN '" . esc_sql($newData[$adapter::NOTAIRE_EMAIL]) . "' ";

                            if (isset($newData[$adapter::NOTAIRE_FONC]))
                                $updateFoncValues[]         = " id = {$currentData->id} THEN '" . esc_sql($newData[$adapter::NOTAIRE_FONC]) . "' ";

                            if (isset($newData[$adapter::NOTAIRE_TEL]))
                                $updateTelValues[]         = " id = {$currentData->id} THEN '" . esc_sql($newData[$adapter::NOTAIRE_TEL]) . "' ";

                            if (isset($newData[$adapter::NOTAIRE_FAX]))
                                $updateFaxValues[]         = " id = {$currentData->id} THEN '" . esc_sql($newData[$adapter::NOTAIRE_FAX]) . "' ";

                            if (isset($newData[$adapter::NOTAIRE_PORTABLE]))
                                $updateMobileValues[]      = " id = {$currentData->id} THEN '" . esc_sql($newData[$adapter::NOTAIRE_PORTABLE]) . "' ";

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
                    // import only authorized category
                    // @see https://trello.com/c/P81yRyRM/21-s-43-import-des-notaires-et-creation-des-etudes-il-y-a-deux-notaires-avec-le-meme-crpcen-qui-n-ont-pas-les-memes-infos-pour-l-et
                    if (isset($this->erpNotaireData[$notaire][$adapter::NOTAIRE_CATEG])
                        && !in_array(strtolower($this->erpNotaireData[$notaire][$adapter::NOTAIRE_CATEG]), Config::$notImportedList)) {

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

                        $value .= "'" . (isset($this->erpNotaireData[$notaire][$adapter::NOTAIRE_CATEG]) ? esc_sql($this->erpNotaireData[$notaire][$adapter::NOTAIRE_CATEG]) : '') . "', ";
                        $value .= "'" . (isset($this->erpNotaireData[$notaire][$adapter::NOTAIRE_NUMCLIENT]) ? esc_sql($this->erpNotaireData[$notaire][$adapter::NOTAIRE_NUMCLIENT]) : '') . "', ";
                        $value .= "'" . (isset($this->erpNotaireData[$notaire][$adapter::NOTAIRE_FNAME]) ? esc_sql($this->erpNotaireData[$notaire][$adapter::NOTAIRE_FNAME]) : '') . "', ";
                        $value .= "'" . (isset($this->erpNotaireData[$notaire][$adapter::NOTAIRE_LNAME]) ? esc_sql($this->erpNotaireData[$notaire][$adapter::NOTAIRE_LNAME]) : '') . "', ";
                        $value .= "'" . (isset($this->erpNotaireData[$notaire][$adapter::NOTAIRE_CRPCEN]) ? esc_sql(intval($this->erpNotaireData[$notaire][$adapter::NOTAIRE_CRPCEN])) : '') . "', ";
                        $value .= "'" . (isset($this->erpNotaireData[$notaire][$adapter::NOTAIRE_PWDWEB]) ? esc_sql($this->erpNotaireData[$notaire][$adapter::NOTAIRE_PWDWEB]) : '') . "', ";
                        $value .= "'" . (isset($this->erpNotaireData[$notaire][$adapter::NOTAIRE_PWDTEL]) ? esc_sql($this->erpNotaireData[$notaire][$adapter::NOTAIRE_PWDTEL]) : '') . "', ";
                        $value .= "'" . (isset($this->erpNotaireData[$notaire][$adapter::NOTAIRE_INTERCODE]) ? esc_sql(intval($this->erpNotaireData[$notaire][$adapter::NOTAIRE_INTERCODE])) : '') . "', ";
                        $value .= (isset($this->erpNotaireData[$notaire][$adapter::NOTAIRE_CIVILIT]) ? esc_sql($this->erpNotaireData[$notaire][$adapter::NOTAIRE_CIVILIT]) : '') . ", ";
                        $value .= "'" . (isset($this->erpNotaireData[$notaire][$adapter::NOTAIRE_EMAIL]) ? esc_sql($this->erpNotaireData[$notaire][$adapter::NOTAIRE_EMAIL]) : '') . "', ";
                        $value .= (isset($this->erpNotaireData[$notaire][$adapter::NOTAIRE_FONC]) ? esc_sql($this->erpNotaireData[$notaire][$adapter::NOTAIRE_FONC]) : '') . ", ";
                        $value .= "'" . (isset($this->erpNotaireData[$notaire][$adapter::NOTAIRE_TEL]) ? esc_sql($this->erpNotaireData[$notaire][$adapter::NOTAIRE_TEL]) : '') . "', ";
                        $value .= "'" . (isset($this->erpNotaireData[$notaire][$adapter::NOTAIRE_FAX]) ? esc_sql($this->erpNotaireData[$notaire][$adapter::NOTAIRE_FAX]) : '') . "', ";
                        $value .= "'" . (isset($this->erpNotaireData[$notaire][$adapter::NOTAIRE_PORTABLE]) ? esc_sql($this->erpNotaireData[$notaire][$adapter::NOTAIRE_PORTABLE]) : '') . "', ";
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

        } catch (\Exception $e) {
            // write into logfile
            errorLog(CONST_EMAIL_ERROR_SUBJECT, $e->getMessage());
            // send email
            reportError(CONST_EMAIL_ERROR_CATCH_EXCEPTION, $e->getMessage());
        }

        // import into wp_users table
        $this->insertOrUpdateWpUsers();
    }

    /**
     * Set notaire role
     *
     * @return void
     */
    public function setNotaireRole()
    {
        global $cri_container;

        // block query
        $roleQuery             = $roleUpdateQuery = array();
        $options               = array();
        $options['table']      = 'usermeta';
        $options['attributes'] = 'user_id, meta_key, meta_value';

        // instance of cridonTools
        $cridonTools = $cri_container->get('tools');

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
     *
     * @return void
     */
    protected function insertOrUpdateWpUsers()
    {
        global $cri_container;

        try {
            $this->logs = array();
            $notaires   = $this->find();

            if (count($notaires) > 0) {
                // adapter
                $adapter = $this->adapter;

                // list of values to be inserted
                $insertValues = array();

                // instance of cridon tools
                $criTools = $cri_container->get('tools');

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

                        $value .= "'" . esc_sql($userName) . "', ";
                        $value .= "'" . wp_hash_password($notaire->web_password) . "', ";
                        $value .= "'" . sanitize_title($displayName) . "', ";
                        $value .= "'" . $notaire->email_adress . "', ";
                        $value .= "'" . date('Y-m-d H:i:s') . "', ";
                        $value .= $userStatus . ", ";
                        $value .= "'" . esc_sql($displayName) . "'";

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
                            $bulkDisplayNameUpdate[] = " ID = {$notaire->id_wp_user} THEN '" . esc_sql($displayName) . "' ";
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
            // write into logfile
            errorLog(CONST_EMAIL_ERROR_SUBJECT, $e->getMessage());
            // send email
            reportError(CONST_EMAIL_ERROR_CATCH_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * Update id_wp_user in cri_notaire
     *
     * @param array $notaires
     */
    protected function updateCriNotaireWpUserId($notaires)
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
            // write into logfile
            errorLog(CONST_EMAIL_ERROR_SUBJECT, $e->getMessage());
            // send email
            reportError(CONST_EMAIL_ERROR_CATCH_EXCEPTION, $e->getMessage());
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



    /**
     * Get by crpcen and email
     *
     * @param string $login
     * @param string $email
     *
     * @return array|null
     */
    public function findByLoginAndEmail($login, $email)
    {
        $items = $this->find(array(
                                       'selects'    => array(
                                           'Notaire.id',
                                           'Notaire.web_password',
                                           'Notaire.email_adress',
                                           'Notaire.crpcen',
                                           'Notaire.id_civilite',
                                           'Notaire.id_fonction'
                                       ),
                                       'conditions' => array(
                                           'crpcen'       => $login,
                                           'email_adress' => $email
                                       )
                                   )
        );

        return $items;
    }

    /**
     * Get by login and password
     *
     * @param string $login
     * @param string $pwd
     *
     * @return array|null
     */
    public function findByLoginAndPassword($login, $pwd)
    {
        $objects = $this->find_one(array(
                'selects' => array(
                    'Notaire.id',
                    'Notaire.crpcen',
                    'Notaire.id_civilite',
                    'Notaire.id_fonction'
                ),
                'conditions' => array(
                    'crpcen'       => $login,
                    'web_password' => $pwd
                )
            )
        );

        return $objects;
    }

    /**
     * Get connected user data
     *
     * @return mixed
     */
    public function getUserConnectedData()
    {
        global $current_user;

        $object = $this->find_one_by_id_wp_user($current_user->ID);

        if (is_object($object) && property_exists($object, 'client_number')) {
            $datas = mvc_model('solde')->getSoldeByClientNumber($object->client_number);

            // init data
            $object->nbAppel = $object->nbCourrier = $object->quota = $object->pointConsomme = $object->solde = 0;
            $object->date = '';

            // quota, pointCosomme, solde
            if (isset($datas[0])) {
                $object->quota = $datas[0]->quota;
                $object->pointConsomme = $datas[0]->totalPoint;
                $object->solde = intval($datas[0]->quota) - intval($datas[0]->totalPoint);
                $object->date = date('d/m/Y', strtotime($datas[0]->date_arret));
            }

            // fill nbAppel && nbCourrier
            foreach($datas as $data) {
                if ($data->type_support == CONST_SUPPORT_APPEL_ID) {
                    $object->nbAppel += $data->nombre;
                } elseif ($data->type_support == CONST_SUPPORT_COURRIER_ID) {
                    $object->nbCourrier += $data->nombre;
                }
            }

        }

        return $object;
    }

    /**
     * Action for importing notaire data into wp_users
     */
    public function importSolde()
    {
        $this->importSoldeFromCsvFile();
        return $this->logs;
    }

    /**
     * Action for importing data using CSV file
     *
     * @return void
     */
    public function importSoldeFromCsvFile()
    {
        // get csv file
        $files = glob(CONST_IMPORT_CSV_SOLDE_FILE_PATH . '/*.csv');

        try {

            // check if file exist
            if (isset($files[0]) && is_file($files[0])) {

                $this->soldeParser->enclosure = '';
                $this->soldeParser->encoding(null, 'UTF-8');
                $this->soldeParser->auto($files[0]);

                // no error was found
                if (property_exists($this->soldeParser, 'data') && intval($this->soldeParser->error) <= 0) {
                    // Csv data setter
                    $this->setSoldeData($this->soldeParser->data);

                    // prepare data
                    $this->prepareSoldedata();

                    // insert or update data
                    $this->manageSoldeData();

                    // do archive
                    if ($this->importSuccess) {
                        rename($files[0], str_replace(".csv", ".csv." . date('YmdHi'), $files[0]));
                    }
                } else { // file content error
                    // write into logfile
                    $error = sprintf(CONST_EMAIL_ERROR_CORRUPTED_FILE, 'Solde');
                    errorLog(CONST_EMAIL_ERROR_SUBJECT, $error);

                    // send email
                    reportError(CONST_EMAIL_ERROR_CORRUPTED_FILE, 'Solde');
                }
            } else { // file doesn't exist
                // write into logfile
                $error = sprintf(CONST_EMAIL_ERROR_CONTENT, 'Solde');
                errorLog(CONST_EMAIL_ERROR_SUBJECT, $error);

                // send email
                reportError(CONST_EMAIL_ERROR_CONTENT, 'Solde');
            }
        } catch (Exception $e) {
            // archive file
            if (isset($files[0]) && is_file($files[0])) {
                rename($files[0], str_replace(".csv", ".csv." . date('YmdHi'), $files[0]));
            }

            // write write into logfile
            errorLog(CONST_EMAIL_ERROR_SUBJECT, $e->getMessage());

            // send email
            reportError(CONST_EMAIL_ERROR_CATCH_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * Prepare data for listing existing notaire on Site and new from ERP
     *
     * @return void
     */
    protected function prepareSoldedata()
    {
        // set list of existing solde
        $this->setSiteSoldeList();

        // instance of CridonCsvParser
        $csv = $this->soldeParser;

        // fill list of solde from ERP (client_number + support)
        foreach ($this->getSoldeData() as $items) {
            // only item having NUMCLIENT
            if (isset($items[$csv::SOLDE_NUMCLIENT]) && $items[$csv::SOLDE_NUMCLIENT]) {
                // the only unique key available is the "client_number + support"
                $uniqueKey = intval($items[$csv::SOLDE_NUMCLIENT]) . $items[$csv::SOLDE_SUPPORT];
                array_push($this->erpSoldeList, $uniqueKey);

                // solde data filter
                $this->erpSoldeData[$uniqueKey] = $items;
            }
        }
    }

    /**
     * List of existing solde on Site
     *
     * @return void
     */
    protected function setSiteSoldeList()
    {
        // get list of existing notaire
        $soldes = $this->soldeModel->find();


        // fill list of existing solde on site with unique key (client_number + support)
        foreach ($soldes as $solde) {
            array_push($this->siteSoldeList, $solde->client_number . $solde->type_support);
        }
    }

    /**
     * Get list of new solde list
     *
     * @return array
     */
    protected function getNewSoldeList()
    {
        return array_diff($this->erpSoldeList, $this->siteSoldeList);
    }

    /**
     * List of solde to be updated from Site (intersect of Site and ERP)
     *
     * @return array
     */
    protected function getSoldeToBeUpdated()
    {
        // common values between Site and ERP
        $items = array_intersect($this->siteSoldeList, $this->erpSoldeList);

        // return filtered items with associated data from ERP
        return array_intersect_key($this->erpSoldeData, array_flip($items));
    }

    /**
     * Manage Solde data (insert, update)
     *
     * @return void
     */
    protected function manageSoldeData()
    {
        // instance of adapter
        $parser = $this->soldeParser;

        // init list of values to be inserted
        $insertValues = array();

        // init list of values to be updated
        $updateQuotaValues = $updateNombreValues = $updatePointsValues = $updateDateArret = array();

        // list of new data
        $newSoldes = $this->getNewSoldeList();

        // list of data for update
        $updateSoldeList = $this->getSoldeToBeUpdated();

        // update
        if (count($updateSoldeList) > 0) {
            // start/end query block
            $queryStart = " UPDATE `{$this->wpdb->prefix}solde` ";
            $queryEnd   = ' END ';

            // only update if erpData.date_arret > cri_solde.date_arret
            foreach($this->soldeModel->find() as $currentData) {
                // the only unique key available is the "crpcen + web_password"
                $key = $currentData->client_number . $currentData->type_support;

                if (array_key_exists($key, $updateSoldeList)) {
                    $newData = $updateSoldeList[$key];

                    // format date
                    $dateArret = '';
                    if (isset($newData[$parser::SOLDE_DATEARRET])) {
                        $dateArret = $newData[$parser::SOLDE_DATEARRET];

                        // convert date to mysql format
                        if (preg_match("/^(\d+)\/(\d+)\/(\d+)$/", $dateArret)) {
                            $dateTime = date_create_from_format('d/m/Y', $dateArret);
                            $dateArret = $dateTime->format('Y-m-d');
                        }
                    }
                    $newDate = new DateTime($dateArret);
                    $newDate = $newDate->format('Ymd');
                    $oldDate = new DateTime($currentData->date_arret);
                    $oldDate = $oldDate->format('Ymd');
                    if ($newDate > $oldDate) {
                        // prepare all update   query
                        if (isset($newData[$parser::SOLDE_QUOTA]))
                            $updateQuotaValues[]        = " id = {$currentData->id} THEN '" . esc_sql($newData[$parser::SOLDE_QUOTA]) . "' ";

                        if (isset($newData[$parser::SOLDE_NOMBRE]))
                            $updateNombreValues[]   = " id = {$currentData->id} THEN '" . esc_sql($newData[$parser::SOLDE_NOMBRE]) . "' ";

                        if (isset($newData[$parser::SOLDE_POINTS]))
                            $updatePointsValues[]   = " id = {$currentData->id} THEN '" . esc_sql($newData[$parser::SOLDE_POINTS]) . "' ";

                        $updateDateArret[]          = " id = {$currentData->id} THEN '" . $dateArret . "' ";
                    }
                }

                $this->importSuccess = true;
            }

            // execute update query
            if (count($updateQuotaValues) > 0) {
                // quota
                $soldeQuery = ' SET `quota` = CASE ';
                $soldeQuery .= ' WHEN ' . implode(' WHEN ', $updateQuotaValues);
                $soldeQuery .= ' ELSE `quota` ';
                $this->wpdb->query($queryStart . $soldeQuery . $queryEnd);
                // nombre
                $soldeQuery = ' SET `nombre` = CASE ';
                $soldeQuery .= ' WHEN ' . implode(' WHEN ', $updateNombreValues);
                $soldeQuery .= ' ELSE `nombre` ';
                $this->wpdb->query($queryStart . $soldeQuery . $queryEnd);
                // points
                $soldeQuery = ' SET `points` = CASE ';
                $soldeQuery .= ' WHEN ' . implode(' WHEN ', $updatePointsValues);
                $soldeQuery .= ' ELSE `points` ';
                $this->wpdb->query($queryStart . $soldeQuery . $queryEnd);
                // date_arret
                $soldeQuery = ' SET `date_arret` = CASE ';
                $soldeQuery .= ' WHEN ' . implode(' WHEN ', $updateDateArret);
                $soldeQuery .= ' ELSE `date_arret` ';
                $this->wpdb->query($queryStart . $soldeQuery . $queryEnd);

                $this->importSuccess = true;
            }
        }

        // insert new data
        if (count($newSoldes) > 0) {
            $options               = array();
            $options['table']      = 'solde';
            $options['attributes'] = 'client_number, quota, type_support, nombre, points, date_arret';

            // prepare multi rows data values
            foreach ($newSoldes as $solde) {
                if (isset($this->erpSoldeData[$solde][$parser::SOLDE_NUMCLIENT])
                    && intval($this->erpSoldeData[$solde][$parser::SOLDE_NUMCLIENT]) > 0) {

                    // format date
                    $dateArret = '';
                    if (isset($this->erpSoldeData[$solde][$parser::SOLDE_DATEARRET])) {
                        $dateArret = $this->erpSoldeData[$solde][$parser::SOLDE_DATEARRET];

                        // convert date to mysql format
                        if (preg_match("/^(\d+)\/(\d+)\/(\d+)$/", $dateArret)) {
                            $dateTime = date_create_from_format('d/m/Y', $dateArret);
                            $dateArret = $dateTime->format('Y-m-d');
                        }
                    }

                    $value = "(";

                    $value .= "'" . (isset($this->erpSoldeData[$solde][$parser::SOLDE_NUMCLIENT]) ? esc_sql($this->erpSoldeData[$solde][$parser::SOLDE_NUMCLIENT]) : '') . "', ";
                    $value .= "'" . (isset($this->erpSoldeData[$solde][$parser::SOLDE_QUOTA]) ? esc_sql($this->erpSoldeData[$solde][$parser::SOLDE_QUOTA]) : '') . "', ";
                    $value .= "'" . (isset($this->erpSoldeData[$solde][$parser::SOLDE_SUPPORT]) ? esc_sql($this->erpSoldeData[$solde][$parser::SOLDE_SUPPORT]) : '') . "', ";
                    $value .= (isset($this->erpSoldeData[$solde][$parser::SOLDE_NOMBRE]) ? esc_sql($this->erpSoldeData[$solde][$parser::SOLDE_NOMBRE]) : '') . ", ";
                    $value .= "'" . (isset($this->erpSoldeData[$solde][$parser::SOLDE_POINTS]) ? esc_sql($this->erpSoldeData[$solde][$parser::SOLDE_POINTS]) : '') . "', ";
                    $value .= "'" . $dateArret . "'";

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
    }

    /**
     * @return array
     */
    public function getSoldeData()
    {
        return $this->soldeData;
    }

    /**
     * @param array $soldeData
     */
    public function setSoldeData($soldeData)
    {
        $this->soldeData = $soldeData;
    }

    /**
     * Check if users can access finances
     *
     * @return bool
     */
    public function userCanAccessFinance()
    {
        $object = $this->getUserConnectedData();

        return (isset($object->category)
            && strtolower($object->category) === CONST_OFFICES_ROLE
            && isset($object->fonction->id)
            && !in_array($object->fonction->id, Config::$cannotAccessFinance)
        ) ? true : false;
    }
}