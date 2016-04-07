<?php

/**
 * Class Notaire
 * @author Etech
 * @contributor Joelio
 */

class Notaire extends \App\Override\Model\CridonMvcModel
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
     * @var mixed
     */
    private static $userConnectedData = null;

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
            'foreign_key' => 'id_wp_user'
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
     * @var array : list of existing etude on Site
     */
    protected $siteEtudeList = array();

    /**
     * @var array : list of etude in ERP
     */
    protected $erpEtudeList = array();

    /**
     * @var array : list of etude data from ERP
     */
    protected $erpEtudeData = array();

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
     * @param bool $force : Update all notaries ? (default false) Only available through DB update
     *
     * @return mixed
     */
    public function importIntoWpUsers($force = false)
    {
        // log start of import
        if (CONST_TRACE_IMPORT_NOTAIRE) {
            writeLog('Debut import', 'importnotaire.log');
        }
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
                    $this->importDataUsingDBconnect($force);
                    break;
            }

            // disable notaire admin bar
            CriDisableAdminBarForExistingNotaire();

            // log end of import
            if (CONST_TRACE_IMPORT_NOTAIRE) {
                writeLog('Fin import', 'importnotaire.log');
            }

            // status code
            return CONST_STATUS_CODE_OK;

        } catch (Exception $e) {
            // write into logfile
            writeLog($e, 'notaire.log');

            // log end of import
            if (CONST_TRACE_IMPORT_NOTAIRE) {
                writeLog('Fin import', 'importnotaire.log');
            }

            // status code
            return CONST_STATUS_CODE_GONE;
        }
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
                    $error = sprintf(CONST_EMAIL_ERROR_CORRUPTED_FILE, 'Notaire');
                    writeLog($error, 'notaire.log');

                    // send email
                    reportError(CONST_EMAIL_ERROR_CORRUPTED_FILE, 'Notaire');
                }
            } else { // file doesn't exist
                // write into logfile
                $error = sprintf(CONST_EMAIL_ERROR_CONTENT, 'Notaire');
                writeLog($error, 'notaire.log');

                // send email
                reportError(CONST_EMAIL_ERROR_CONTENT, 'Notaire');
            }
        } catch (Exception $e) {
            // archive file
            if (isset($files[0]) && is_file($files[0])) {
                rename($files[0], str_replace(".csv", ".csv." . date('YmdHi'), $files[0]));
            }

            // write write into logfile
            writeLog($e, 'notaire.log');

            // send email
            reportError(CONST_EMAIL_ERROR_CATCH_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * Import data with ODBC Link
     *
     * @param bool $force : Update all notaries ? (default false)
     *
     * @throws Exception
     *
     * @return void
     */
    protected function importDataUsingDBconnect($force = false)
    {
        try {
            // query
            $sql = 'SELECT * FROM ' . CONST_DB_TABLE_NOTAIRE;
            $adapter = $this->adapter;
            // exec query
            $adapter->execute($sql);

            // prepare data
            while ($data = $adapter->fetchData()) {
                if (isset( $data[$adapter::NOTAIRE_CRPCEN] )) {
                    $data[$adapter::NOTAIRE_CRPCEN] = trim($data[$adapter::NOTAIRE_CRPCEN]);
                    if (!empty($data[$adapter::NOTAIRE_CRPCEN])) {
                        // the only unique key available is the "crpcen + web_password"
                        $uniqueKey = $data[$adapter::NOTAIRE_CRPCEN] . $data[$adapter::NOTAIRE_PWDWEB];
                        array_push($this->erpNotaireList, $uniqueKey);

                        // notaire data filter
                        $this->erpNotaireData[$uniqueKey] = $data;

                        // Fill list of ERP Etude
                        array_push($this->erpEtudeList, $data[$adapter::NOTAIRE_CRPCEN]);

                        // Etude data filter
                        $this->erpEtudeData[$data[$adapter::NOTAIRE_CRPCEN]] = $data;
                    }
                }
            }

            // set list of existing notaire
            $this->setSiteNotaireList();

            // insert or update data
            $this->manageNotaireData($force);

            // set list of existing notaire
            $this->setSiteEtudeList();

            // insert or update etude data
            $this->manageEtudeData();

            // Close Connection
            $adapter->closeConnection();

        } catch (\Exception $e) {
            // write into logfile
            writeLog($e, 'notaire.log');
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
                $uniqueKey = $items[$csv::NOTAIRE_CRPCEN] . $items[$csv::NOTAIRE_PWDWEB];
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
     * List of existing etude on Site
     *
     * @return void
     */
    protected function setSiteEtudeList()
    {
        // get list of existing etude
        $etudes = mvc_model('etude')->find();


        // fill list of existing etude on site with unique key (crpcen)
        foreach ($etudes as $etude) {
            array_push($this->siteEtudeList, $etude->crpcen);
        }
    }

    /**
     * Get list of new etude list
     *
     * @return array
     */
    protected function getNewEtudeList()
    {
        return array_diff($this->erpEtudeList, $this->siteEtudeList);
    }

    /**
     * List of etude to be updated from Site (intersect of Site and ERP)
     *
     * @return array
     */
    protected function getEtudeToBeUpdated()
    {
        // common values between Site and ERP
        $items = array_intersect($this->siteEtudeList, $this->erpEtudeList);

        // return filtered items with associated data from ERP
        return array_intersect_key($this->erpEtudeData, array_flip($items));
    }

    /**
     * Manage Notaire data (insert, update)
     *
     * @param bool $force : Update all notaries ? (default false)
     *
     * @return void
     */
    protected function manageNotaireData($force = false)
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
                        if ($force || ($newDate > $oldDate)) {
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
                                $updateInterCodeValues[]    = " id = {$currentData->id} THEN '" . esc_sql($newData[$adapter::NOTAIRE_INTERCODE]) . "' ";

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

                    $this->importSuccess = true;
                }

                if (count($updateNumclientValues) > 0) {
                    // client_number
                    $notaireQuery = ' SET `client_number` = CASE ';
                    $notaireQuery .= ' WHEN ' . implode(' WHEN ', $updateNumclientValues);
                    $notaireQuery .= ' ELSE `client_number` ';
                    $this->wpdb->query($queryStart . $notaireQuery . $queryEnd);

                    $this->importSuccess = true;
                }

                if (count($updateFirstnameValues) > 0) {
                    // first_name
                    $notaireQuery = ' SET `first_name` = CASE ';
                    $notaireQuery .= ' WHEN ' . implode(' WHEN ', $updateFirstnameValues);
                    $notaireQuery .= ' ELSE `first_name` ';
                    $this->wpdb->query($queryStart . $notaireQuery . $queryEnd);

                    $this->importSuccess = true;
                }

                if (count($updateLastnameValues) > 0) {
                    // last_name
                    $notaireQuery = ' SET `last_name` = CASE ';
                    $notaireQuery .= ' WHEN ' . implode(' WHEN ', $updateLastnameValues);
                    $notaireQuery .= ' ELSE `last_name` ';
                    $this->wpdb->query($queryStart . $notaireQuery . $queryEnd);

                    $this->importSuccess = true;
                }

                if (count($updatePwdtelValues) > 0) {
                    // tel_password
                    $notaireQuery = ' SET `tel_password` = CASE ';
                    $notaireQuery .= ' WHEN ' . implode(' WHEN ', $updatePwdtelValues);
                    $notaireQuery .= ' ELSE `tel_password` ';
                    $this->wpdb->query($queryStart . $notaireQuery . $queryEnd);

                    $this->importSuccess = true;
                }

                if (count($updateInterCodeValues) > 0) {
                    // code_interlocuteur
                    $notaireQuery = ' SET `code_interlocuteur` = CASE ';
                    $notaireQuery .= ' WHEN ' . implode(' WHEN ', $updateInterCodeValues);
                    $notaireQuery .= ' ELSE `code_interlocuteur` ';
                    $this->wpdb->query($queryStart . $notaireQuery . $queryEnd);

                    $this->importSuccess = true;
                }

                if (count($updateCivlitValues) > 0) {
                    // id_civilite
                    $notaireQuery = ' SET `id_civilite` = CASE ';
                    $notaireQuery .= ' WHEN ' . implode(' WHEN ', $updateCivlitValues);
                    $notaireQuery .= ' ELSE `id_civilite` ';
                    $this->wpdb->query($queryStart . $notaireQuery . $queryEnd);

                    $this->importSuccess = true;
                }

                if (count($updateEmailValues) > 0) {
                    // email_adress
                    $notaireQuery = ' SET `email_adress` = CASE ';
                    $notaireQuery .= ' WHEN ' . implode(' WHEN ', $updateEmailValues);
                    $notaireQuery .= ' ELSE `email_adress` ';
                    $this->wpdb->query($queryStart . $notaireQuery . $queryEnd);

                    $this->importSuccess = true;
                }

                if (count($updateFoncValues) > 0) {
                    // id_fonction
                    $notaireQuery = ' SET `id_fonction` = CASE ';
                    $notaireQuery .= ' WHEN ' . implode(' WHEN ', $updateFoncValues);
                    $notaireQuery .= ' ELSE `id_fonction` ';
                    $this->wpdb->query($queryStart . $notaireQuery . $queryEnd);

                    $this->importSuccess = true;
                }

                if (count($updateTelValues) > 0) {
                    // tel
                    $notaireQuery = ' SET `tel` = CASE ';
                    $notaireQuery .= ' WHEN ' . implode(' WHEN ', $updateTelValues);
                    $notaireQuery .= ' ELSE `tel` ';
                    $this->wpdb->query($queryStart . $notaireQuery . $queryEnd);

                    $this->importSuccess = true;
                }

                if (count($updateFaxValues) > 0) {
                    // fax
                    $notaireQuery = ' SET `fax` = CASE ';
                    $notaireQuery .= ' WHEN ' . implode(' WHEN ', $updateFaxValues);
                    $notaireQuery .= ' ELSE `fax` ';
                    $this->wpdb->query($queryStart . $notaireQuery . $queryEnd);

                    $this->importSuccess = true;
                }

                if (count($updateMobileValues) > 0) {
                    // tel_portable
                    $notaireQuery = ' SET `tel_portable` = CASE ';
                    $notaireQuery .= ' WHEN ' . implode(' WHEN ', $updateMobileValues);
                    $notaireQuery .= ' ELSE `tel_portable` ';
                    $this->wpdb->query($queryStart . $notaireQuery . $queryEnd);

                    $this->importSuccess = true;
                }

                if (count($updateDateModified) > 0) {
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
                        $value .= "'" . (isset($this->erpNotaireData[$notaire][$adapter::NOTAIRE_CRPCEN]) ? esc_sql($this->erpNotaireData[$notaire][$adapter::NOTAIRE_CRPCEN]) : '') . "', ";
                        $value .= "'" . (isset($this->erpNotaireData[$notaire][$adapter::NOTAIRE_PWDWEB]) ? esc_sql($this->erpNotaireData[$notaire][$adapter::NOTAIRE_PWDWEB]) : '') . "', ";
                        $value .= "'" . (isset($this->erpNotaireData[$notaire][$adapter::NOTAIRE_PWDTEL]) ? esc_sql($this->erpNotaireData[$notaire][$adapter::NOTAIRE_PWDTEL]) : '') . "', ";
                        $value .= "'" . (isset($this->erpNotaireData[$notaire][$adapter::NOTAIRE_INTERCODE]) ? esc_sql($this->erpNotaireData[$notaire][$adapter::NOTAIRE_INTERCODE]) : '') . "', ";
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
            writeLog($e, 'notaire.log');
            // send email
            reportError(CONST_EMAIL_ERROR_CATCH_EXCEPTION, $e->getMessage(), 'Cridon - Données notaire - Erreur mise à jour');
        }

        // import into wp_users table
        $this->insertOrUpdateWpUsers();
    }

    /**
     * Manage Etude data (insert, update)
     *
     * @return void
     */
    protected function manageEtudeData()
    {
        try {
            // instance of adapter
            $adapter = $this->adapter;

            // init list of values to be inserted
            $insertValues = array();

            // init list of values to be updated
            $updateSigleValues = $updateOfficenameValues = $updateAdress1Values = $updateAdress2Values = $updateAdress3Values = array();
            $updateCpValues = $updateCityValues = $updateEmail1Values = $updateEmail2Values = $updateEmail3Values = array();
            $updateTelValues = $updateFaxValues = array();

            // list of new data
            $newEtudes = $this->getNewEtudeList();

            // list of data for update
            $updateEtudeList = $this->getEtudeToBeUpdated();

            // etude table
            $etudeTable  = mvc_model('etude')->table;

            // update
            if (count($updateEtudeList) > 0) {
                // start/end query block
                $queryStart = " UPDATE `{$etudeTable}` ";
                $queryEnd   = ' END ';

                foreach(mvc_model('etude')->find() as $currentData) {
                    $key = $currentData->crpcen;

                    // start optimisation
                    if (array_key_exists($key, $updateEtudeList)) {
                        $newData = $updateEtudeList[$key];

                        // prepare all update   query
                        if (isset($newData[$adapter::NOTAIRE_SIGLE]))
                            $updateSigleValues[]        = " crpcen = {$currentData->crpcen} THEN '" . esc_sql($newData[$adapter::NOTAIRE_SIGLE]) . "' ";

                        if (isset($newData[$adapter::NOTAIRE_OFFICENAME]))
                            $updateOfficenameValues[]    = " crpcen = {$currentData->crpcen} THEN '" . esc_sql($newData[$adapter::NOTAIRE_OFFICENAME]) . "' ";

                        if (isset($newData[$adapter::NOTAIRE_ADRESS1]))
                            $updateAdress1Values[]    = " crpcen = {$currentData->crpcen} THEN '" . esc_sql($newData[$adapter::NOTAIRE_ADRESS1]) . "' ";

                        if (isset($newData[$adapter::NOTAIRE_ADRESS2]))
                            $updateAdress2Values[]     = " crpcen = {$currentData->crpcen} THEN '" . esc_sql($newData[$adapter::NOTAIRE_ADRESS2]) . "' ";

                        if (isset($newData[$adapter::NOTAIRE_ADRESS3]))
                            $updateAdress3Values[]       = " crpcen = {$currentData->crpcen} THEN '" . esc_sql($newData[$adapter::NOTAIRE_ADRESS3]) . "' ";

                        if (isset($newData[$adapter::NOTAIRE_CP]))
                            $updateCpValues[]    = " crpcen = {$currentData->crpcen} THEN '" . esc_sql($newData[$adapter::NOTAIRE_CP]) . "' ";

                        if (isset($newData[$adapter::NOTAIRE_CITY]))
                            $updateCityValues[]       = " crpcen = {$currentData->crpcen} THEN '" . esc_sql($newData[$adapter::NOTAIRE_CITY]) . "' ";

                        if (isset($newData[$adapter::NOTAIRE_MAIL1]))
                            $updateEmail1Values[]        = " crpcen = {$currentData->crpcen} THEN '" . esc_sql($newData[$adapter::NOTAIRE_MAIL1]) . "' ";

                        if (isset($newData[$adapter::NOTAIRE_MAIL2]))
                            $updateEmail2Values[]         = " crpcen = {$currentData->crpcen} THEN '" . esc_sql($newData[$adapter::NOTAIRE_MAIL2]) . "' ";

                        if (isset($newData[$adapter::NOTAIRE_MAIL3]))
                            $updateEmail3Values[]         = " crpcen = {$currentData->crpcen} THEN '" . esc_sql($newData[$adapter::NOTAIRE_MAIL3]) . "' ";

                        if (isset($newData[$adapter::NOTAIRE_OFFICETEL]))
                            $updateTelValues[]      = " crpcen = {$currentData->crpcen} THEN '" . esc_sql($newData[$adapter::NOTAIRE_OFFICETEL]) . "' ";

                        if (isset($newData[$adapter::NOTAIRE_OFFICEFAX]))
                            $updateFaxValues[]         = " crpcen = {$currentData->crpcen} THEN '" . esc_sql($newData[$adapter::NOTAIRE_OFFICEFAX]) . "' ";

                    }
                    // end optimisation
                }

                // execute update query
                if (count($updateSigleValues) > 0) {
                    // id_sigle
                    $etudeQuery = ' SET `id_sigle` = CASE ';
                    $etudeQuery .= ' WHEN ' . implode(' WHEN ', $updateSigleValues);
                    $etudeQuery .= ' ELSE `id_sigle` ';
                    $this->wpdb->query($queryStart . $etudeQuery . $queryEnd);
                    // office_name
                    $etudeQuery = ' SET `office_name` = CASE ';
                    $etudeQuery .= ' WHEN ' . implode(' WHEN ', $updateOfficenameValues);
                    $etudeQuery .= ' ELSE `office_name` ';
                    $this->wpdb->query($queryStart . $etudeQuery . $queryEnd);
                    // adress_1
                    $etudeQuery = ' SET `adress_1` = CASE ';
                    $etudeQuery .= ' WHEN ' . implode(' WHEN ', $updateAdress1Values);
                    $etudeQuery .= ' ELSE `adress_1` ';
                    $this->wpdb->query($queryStart . $etudeQuery . $queryEnd);
                    // adress_2
                    $etudeQuery = ' SET `adress_2` = CASE ';
                    $etudeQuery .= ' WHEN ' . implode(' WHEN ', $updateAdress2Values);
                    $etudeQuery .= ' ELSE `adress_2` ';
                    $this->wpdb->query($queryStart . $etudeQuery . $queryEnd);
                    // adress_3
                    $etudeQuery = ' SET `adress_3` = CASE ';
                    $etudeQuery .= ' WHEN ' . implode(' WHEN ', $updateAdress3Values);
                    $etudeQuery .= ' ELSE `adress_3` ';
                    $this->wpdb->query($queryStart . $etudeQuery . $queryEnd);
                    // cp
                    $etudeQuery = ' SET `cp` = CASE ';
                    $etudeQuery .= ' WHEN ' . implode(' WHEN ', $updateCpValues);
                    $etudeQuery .= ' ELSE `cp` ';
                    $this->wpdb->query($queryStart . $etudeQuery . $queryEnd);
                    // city
                    $etudeQuery = ' SET `city` = CASE ';
                    $etudeQuery .= ' WHEN ' . implode(' WHEN ', $updateCityValues);
                    $etudeQuery .= ' ELSE `city` ';
                    $this->wpdb->query($queryStart . $etudeQuery . $queryEnd);
                    // office_email_adress_1
                    $etudeQuery = ' SET `office_email_adress_1` = CASE ';
                    $etudeQuery .= ' WHEN ' . implode(' WHEN ', $updateEmail1Values);
                    $etudeQuery .= ' ELSE `office_email_adress_1` ';
                    $this->wpdb->query($queryStart . $etudeQuery . $queryEnd);
                    // office_email_adress_2
                    $etudeQuery = ' SET `office_email_adress_2` = CASE ';
                    $etudeQuery .= ' WHEN ' . implode(' WHEN ', $updateEmail2Values);
                    $etudeQuery .= ' ELSE `office_email_adress_2` ';
                    $this->wpdb->query($queryStart . $etudeQuery . $queryEnd);
                    // office_email_adress_3
                    $etudeQuery = ' SET `office_email_adress_3` = CASE ';
                    $etudeQuery .= ' WHEN ' . implode(' WHEN ', $updateEmail3Values);
                    $etudeQuery .= ' ELSE `office_email_adress_3` ';
                    $this->wpdb->query($queryStart . $etudeQuery . $queryEnd);
                    // tel
                    $etudeQuery = ' SET `tel` = CASE ';
                    $etudeQuery .= ' WHEN ' . implode(' WHEN ', $updateTelValues);
                    $etudeQuery .= ' ELSE `tel` ';
                    $this->wpdb->query($queryStart . $etudeQuery . $queryEnd);
                    // fax
                    $etudeQuery = ' SET `fax` = CASE ';
                    $etudeQuery .= ' WHEN ' . implode(' WHEN ', $updateFaxValues);
                    $etudeQuery .= ' ELSE `fax` ';
                    $this->wpdb->query($queryStart . $etudeQuery . $queryEnd);

                    $this->importSuccess = true;
                }
            }

            // insert new data
            if (count($newEtudes) > 0) {
                $options               = array();
                $options['table']      = 'etude';
                $options['attributes'] = 'crpcen, id_sigle, office_name, adress_1, adress_2, adress_3, cp, city, ';
                $options['attributes'] .= 'office_email_adress_1, office_email_adress_2, office_email_adress_3, tel, fax';

                // prepare multi rows data values
                foreach ($newEtudes as $etude) {

                    $value = "(";

                    $value .= "'" . (isset($this->erpEtudeData[$etude][$adapter::NOTAIRE_CRPCEN]) ? esc_sql($this->erpEtudeData[$etude][$adapter::NOTAIRE_CRPCEN]) : '') . "', ";
                    $value .= "'" . (isset($this->erpEtudeData[$etude][$adapter::NOTAIRE_SIGLE]) ? esc_sql($this->erpEtudeData[$etude][$adapter::NOTAIRE_SIGLE]) : '') . "', ";
                    $value .= "'" . (isset($this->erpEtudeData[$etude][$adapter::NOTAIRE_OFFICENAME]) ? esc_sql($this->erpEtudeData[$etude][$adapter::NOTAIRE_OFFICENAME]) : '') . "', ";
                    $value .= "'" . (isset($this->erpEtudeData[$etude][$adapter::NOTAIRE_ADRESS1]) ? esc_sql($this->erpEtudeData[$etude][$adapter::NOTAIRE_ADRESS1]) : '') . "', ";
                    $value .= "'" . (isset($this->erpEtudeData[$etude][$adapter::NOTAIRE_ADRESS2]) ? esc_sql($this->erpEtudeData[$etude][$adapter::NOTAIRE_ADRESS2]) : '') . "', ";
                    $value .= "'" . (isset($this->erpEtudeData[$etude][$adapter::NOTAIRE_ADRESS3]) ? esc_sql($this->erpEtudeData[$etude][$adapter::NOTAIRE_ADRESS3]) : '') . "', ";
                    $value .= "'" . (isset($this->erpEtudeData[$etude][$adapter::NOTAIRE_CP]) ? esc_sql($this->erpEtudeData[$etude][$adapter::NOTAIRE_CP]) : '') . "', ";
                    $value .= "'" . (isset($this->erpEtudeData[$etude][$adapter::NOTAIRE_CITY]) ? esc_sql($this->erpEtudeData[$etude][$adapter::NOTAIRE_CITY]) : '') . "', ";
                    $value .= "'" . (isset($this->erpEtudeData[$etude][$adapter::NOTAIRE_MAIL1]) ? esc_sql($this->erpEtudeData[$etude][$adapter::NOTAIRE_MAIL1]) : '') . "', ";
                    $value .= "'" . (isset($this->erpEtudeData[$etude][$adapter::NOTAIRE_MAIL2]) ? esc_sql($this->erpEtudeData[$etude][$adapter::NOTAIRE_MAIL2]) : '') . "', ";
                    $value .= "'" . (isset($this->erpEtudeData[$etude][$adapter::NOTAIRE_MAIL3]) ? esc_sql($this->erpEtudeData[$etude][$adapter::NOTAIRE_MAIL3]) : '') . "', ";
                    $value .= "'" . (isset($this->erpEtudeData[$etude][$adapter::NOTAIRE_OFFICETEL]) ? esc_sql($this->erpEtudeData[$etude][$adapter::NOTAIRE_OFFICETEL]) : '') . "', ";
                    $value .= "'" . (isset($this->erpEtudeData[$etude][$adapter::NOTAIRE_OFFICEFAX]) ? esc_sql($this->erpEtudeData[$etude][$adapter::NOTAIRE_OFFICEFAX]) : '') . "'";

                    $value .= ")";

                    $insertValues[] = $value;
                }

                if (count($insertValues) > 0) {
                    $queryBulder       = mvc_model('QueryBuilder');
                    $options['values'] = implode(', ', $insertValues);
                    // bulk insert
                    $queryBulder->insertMultiRows($options);
                }
            }

        } catch (\Exception $e) {
            // write into logfile
            writeLog($e, 'etude.log');
            // send email
            reportError(CONST_EMAIL_ERROR_CATCH_EXCEPTION, $e->getMessage(), 'Cridon - Données étude - Erreur mise à jour');
        }

    }

    /**
     * Set notaire role
     *
     * @return void
     */
    public function setNotaireRole()
    {
        foreach ($this->find() as $notary) {
            // get user by id
            $user = new WP_User($notary->id_wp_user);
            // user must be an instance of WP_User vs WP_Error
            if ($user instanceof WP_User) {
                // default role
                $user->add_role(CONST_NOTAIRE_ROLE);
                /**
                 * finance role
                 * to be matched in list of authorized user by function
                 *
                 * @see \Config::$canAccessFinance
                 */
                if (in_array($notary->id_fonction, Config::$canAccessFinance)) {
                    $user->add_role(CONST_FINANCE_ROLE);
                }
            }
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
            writeLog($e, 'notaire.log');
            // send email
            reportError(CONST_EMAIL_ERROR_CATCH_EXCEPTION, $e->getMessage(), 'Cridon - Données utilisateurs - Erreur mise à jour');
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
            writeLog($e, 'notaire.log');
            // send email
            reportError(CONST_EMAIL_ERROR_CATCH_EXCEPTION, $e->getMessage(),'Cridon - Données notaire - Erreur mise à jour id_wp_user');
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
        // base query
        $query = " SELECT `n`.`id`, `n`.`web_password`, `n`.`email_adress`, `n`.`crpcen`, `n`.`code_interlocuteur`,
                    `n`.`client_number`, `n`.`id_civilite`, `n`.`id_fonction`,
                    `n`.`first_name` AS prenom, `n`.`last_name` AS nom, `c`.`label` AS civilite FROM {$this->table} n ";
        $query .= " INNER JOIN `{$this->wpdb->users}` u ON u.`ID` = n.`id_wp_user`
                    LEFT JOIN `{$this->wpdb->prefix}civilite` c ON `c`.`id` = `n`.`id_civilite` ";
        $query .= " WHERE `crpcen` = %s
                    AND `email_adress` = %s
                    AND `user_status` = " . CONST_STATUS_ENABLED;

        // prepare query
        $query = $this->wpdb->prepare($query, $login, $email);

        // exec query and return result
        return $this->wpdb->get_row($query);
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
        // base query
        $query = " SELECT `n`.`id`, `n`.`code_interlocuteur`, `n`.`crpcen`, `n`.`client_number`, `n`.`web_password`, `n`.`first_name` AS prenom,
                    `n`.`last_name` AS nom, `c`.`label` AS civilite FROM {$this->table} n ";
        $query .= " INNER JOIN `{$this->wpdb->users}` u ON u.`ID` = n.`id_wp_user`
                    LEFT JOIN `{$this->wpdb->prefix}civilite` c ON `c`.`id` = `n`.`id_civilite` ";
        $query .= " WHERE `crpcen` = %s
                    AND `web_password` = %s
                    AND `user_status` = " . CONST_STATUS_ENABLED;

        // prepare query
        $query = $this->wpdb->prepare($query, $login, $pwd);

        // exec query and return result
        return $this->wpdb->get_row($query);
    }

    /**
     * Get connected user data
     *
     * @return mixed
     */
    public function getUserConnectedData()
    {
        global $current_user;

        if( self::$userConnectedData !== null && is_object(self::$userConnectedData) && self::$userConnectedData instanceof MvcModelObject ){
            $object = self::$userConnectedData;
        } else {
            $idWPoptions = array(
                'conditions' => array(
                    'Notaire.id_wp_user' => $current_user->ID,
                ),
                'joins' => array(
                    'Etude'
                ),
            );
            // exec query and return result as object
            $object = $this->find_one($idWPoptions);
        }

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
     * Action for reset notaire data into wp_users
     * Remove all entries corresponding to points consuming
     * Keep lines corresping to initial status
     */
    public function resetSolde()
    {
        $this->wpdb->query("DELETE FROM `{$this->wpdb->prefix}solde` where `type_support` != 0 ");
        return $this->logs;
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
                    writeLog($error, 'solde.log');

                    // send email
                    reportError(CONST_EMAIL_ERROR_CORRUPTED_FILE, 'Solde','Cridon - Solde notaire - Erreur mise à jour');
                }
            } else { // file doesn't exist
                // write into logfile
                $error = sprintf(CONST_EMAIL_ERROR_CONTENT, 'Solde');
                writeLog($error, 'solde.log');

                // send email
                reportError(CONST_EMAIL_ERROR_CONTENT, 'Solde','Cridon - Solde notaire - Erreur ouverture fichier');
            }
        } catch (Exception $e) {
            // archive file
            if (isset($files[0]) && is_file($files[0])) {
                rename($files[0], str_replace(".csv", ".csv." . date('YmdHi'), $files[0]));
            }

            // write write into logfile
            writeLog($e, 'solde.log');

            // send email
            reportError(CONST_EMAIL_ERROR_CATCH_EXCEPTION, $e->getMessage(),'Cridon - Solde notaire - Erreur fichier');
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
     * Check if users can access sensitive informations
     *
     * @return bool
     */
    public function userCanAccessSensitiveInfo()
    {
        global $current_user;

        $object = $this->getUserConnectedData();

        return (isset($object->category)
                && (strcasecmp($object->category, CONST_OFFICES_ROLE) === 0)
                && in_array(CONST_FINANCE_ROLE, (array) $current_user->roles)
        ) ? true : false;
    }

    //Start webservice

    /**
     * Generate token for webservice
     */
    public function generateToken( $id,$login,$password ){
        return $id.'!'.$this->encryption($login, $password).'~'.time();
    }

    /**
     * Construct encrypted value
     *
     * @param string $login
     * @param string $password
     * @return string
     */
    public function encryption( $login,$password ){
        $salt = wp_salt( 'secure_auth' );
        return sha1( $salt.$login.$password );
    }

    /**
     * Verify if token given is valid
     *
     * @return object
     */
    public function checkLastConnect( $token ){
        $notaire = $this->verify($token);
        //No model find
        if( !$notaire ){
            return false;
        }
        return $notaire;
    }

    /**
     * Compare two values
     *
     * @param mixed $v1
     * @param mixed $v2
     * @return boolean
     */
    private function compare( $v1,$v2 ){
        return ( $v1 == $v2);
    }

    /**
     * Compare two dates
     *
     * @param integer $timestamp
     * @return boolean
     */
    private function compareDate( $timestamp ){
        $date = new DateTime();
        $date->setTimestamp( $timestamp );//given
        $now = new DateTime();//today
        $interval = $date->diff($now ,true )->days;
        return ( $interval < Config::$tokenDuration );
    }

    /**
     * Determine values matched in preg_match
     *
     * @param array $matches
     * @return boolean
     */
    protected function checkMatched( $matches ){
        if( count( $matches ) != 4 ){
            return false;
        }
        if( !isset( $matches[1][0] ) || empty( $matches[1][0] ) ){
            return false;
        }
        if( !isset( $matches[2][0] ) || empty( $matches[2][0] ) ){
            return false;
        }
        if( !isset( $matches[3][0] ) || empty( $matches[3][0] ) ){
            return false;
        }
        return true;
    }

    /**
     * Check if current token given is valid
     * Three steps to valid this
     *
     * @param string $authToken
     * @return object|boolean
     */
    protected function verify( $authToken )
    {
        //Structure of token: [id]![encrypted value]~[timestamp]
        //encrypted value = salt + login + password encrypted in sha1 algorithm
        //Pattern regex to use 
        $pattern = "/([0-9]+)!([a-zA-Z0-9]+)~([0-9]+)/";
        if( preg_match_all( $pattern, $authToken, $matches ) ){
            //Check if id, encrypted value and timestamp exists in token given
            if( $this->checkMatched( $matches ) ){
                //Check if Notaire exist with this Id
                $notaire = $this->find_one_by_id( $matches[1][0] );
                if( $notaire ){
                    //Get encrypted value with the Notaire
                    $encryption = $this->encryption( $notaire->crpcen, $notaire->web_password );
                    //Check encrypted value given 
                    if( $this->compare($encryption, $matches[2][0] ) ){
                        //Check timestamp if duration exceeded
                        if( $this->compareDate($matches[3][0]) ){
                            return $notaire;
                        }
                    }
                }
            }
        }
        return false;
    }

    //End webservice

    //FRONT

    //Override function of pagination
    public function paginate($options = array()){
        global $wpdb;
        $options['page'] = empty($options['page']) ? 1 : intval($options['page']);//for limit
        $limit = $this->db_adapter->get_limit_sql($options);
        if(!is_admin()){
            $where = $this->getFilters($options);//Filter
            $query = $this->prepareQueryForFront($options['status'], $where, $limit);
            //Total query for pagination
            $query_count = $this->prepareQueryForCount($options['status'], $where);
            //convert pseudo query to sql
            $qs = new \App\Override\Model\QueryStringModel($query);
            $total_count = $wpdb->get_var($query_count);
            $objects = $qs->getResults();
            $objects = $this->processAppendDocuments($objects);
            $response = array(
                'objects' => $objects,
                'total_objects' => $total_count,
                'total_pages' => ceil($total_count/$options['per_page']),
                'page' => $options['page']
            );
            return $response;
        } else {
            $where = '';
            if(isset($options['conditions'])){
                $where = $this->getWhere($options,$options);
            }
            $query = '
            SELECT n,e
            FROM (SELECT N.*
                FROM Notaire N
                JOIN Etude AS E ON E.crpcen = N.crpcen
                 '.$where.'
                ORDER BY N.id ASC
                '.$limit.'
                 ) [Notaire] n
            JOIN Etude e ON e.crpcen = n.crpcen
                ';

            //Total query for pagination
            $query_count ='
                SELECT COUNT(*) AS count
                FROM '.$wpdb->prefix.'notaire AS N
                JOIN '.$wpdb->prefix.'etude AS E ON E.crpcen = N.crpcen
                '.$where.'
                ORDER BY N.id DESC
                ';
            //convert pseudo query to sql
            $qs = new \App\Override\Model\QueryStringModel($query);
            $total_count = $wpdb->get_var($query_count);
            $objects = $qs->getResults();
            $objects = $this->splitArray($objects,'id');
            $per_page = (!empty($options['per_page'])) ? $options['per_page'] : $this->per_page;
            $response = array(
                'objects' => $objects,
                'total_objects' => $total_count,
                'total_pages' => ceil($total_count/$per_page),
                'page' => $options['page']
            );
            return $response;
        }
    }

    /**
     * Get filter parameter in URL
     *
     * @param array $options
     * @return string
     */
    protected function getFilters($options){
        global $wpdb;
        $where = array();
        foreach ( $options as $k => $v ){
            if (empty($v)) {
                // Ne pas filtrer sur une valeur vide
                continue;
            }
            switch ($k) {
                case 'm':
                    // Matieres
                    $v = (array) esc_sql($v);
                    $v = array_map(function($value) use ($wpdb) {
                        return "'" . strip_tags($value) . "'";
                    }, $v);
                    $where[] = ' M.id = ('.implode(',', $v).')';
                    break;
                case 'd1':
                case 'd2':
                    // Dates (bornes)
                    $d = $this->convertToDateSql($v);
                    if ($d !== false) {
                        $where[] = " Q.creation_date " . ($k == 'd1' ? ">=" : "<=") . " '" . $d . "'";
                    }
                    break;
                case 'n':
                    // Nom/Prénom du notaire
                    $v = urldecode(esc_sql(strip_tags($v)));
                    $where[] = " CONCAT(N.first_name,N.last_name) LIKE '%{$v}%'";
                    break;
            }
        }
        return (empty($where)) ? '' : ' AND '.implode(' AND ',$where);
    }

    /**
     * Get questions pending
     *
     * @param array $options
     * @param array $status
     *
     * @return mixed
     */
    public function getPending($status){
        $query = $this->prepareQueryForFront( $status, '' );
        //convert pseudo query to sql
        $qs = new \App\Override\Model\QueryStringModel($query);
        $objects = $qs->getResults();
        $objects = $this->processAppendDocuments($objects);
        return $objects;
    }

    /**
     * Query used in front for list of questions
     *
     * @param array $status
     * @param string $where
     * @param string $limit
     * @return string
     */
    protected function prepareQueryForFront($status, $where, $limit = ''){
        $user = CriNotaireData();//get Notaire
        $condAffectation = (!is_array($status)) ? 'Q.id_affectation = '.$status : 'Q.id_affectation IN ('.implode(',',$status).')';
        $query = '
            SELECT d,q,s,m,c,n
            FROM (SELECT Q.*
                    FROM Question AS Q
                    JOIN Notaire AS N ON Q.client_number = N.client_number
                    JOIN Etude AS E ON E.crpcen = N.crpcen
                    LEFT JOIN Competence AS C ON Q.id_competence_1 = C.id
                    LEFT JOIN Matiere AS M ON M.code = C.code_matiere
                    WHERE '.$condAffectation.' AND E.crpcen = "'.$user->crpcen.'" '.$where.'
                    GROUP BY Q.id
                    ORDER BY Q.creation_date DESC
                    '.$limit.'
                 ) [Question] q
            LEFT JOIN Document d ON (d.id_externe = q.id AND d.type = "question" )
            LEFT JOIN Support s ON s.id = q.id_support
            LEFT JOIN Competence c ON c.id = q.id_competence_1
            LEFT JOIN Matiere m ON m.code = c.code_matiere
                ';
        return $query;
    }
    /**
     * Query used in front in order to preprare pagination for questions list
     *
     * @param array $status
     * @param string $where
     * @return string
     */
    protected function prepareQueryForCount($status, $where){
        global $wpdb;
        $user = CriNotaireData();//get Notaire
        $condAffectation = (!is_array($status)) ? 'Q.id_affectation = '.$status : 'Q.id_affectation IN ('.implode(',',$status).')';
        $query = '
                SELECT COUNT(DISTINCT (Q.id)) AS count
                FROM '.$wpdb->prefix.'question AS Q
                JOIN '.$wpdb->prefix.'notaire AS N ON Q.client_number = N.client_number
                JOIN '.$wpdb->prefix.'etude AS E ON E.crpcen = N.crpcen
                LEFT JOIN '.$wpdb->prefix.'competence AS C ON C.id = Q.id_competence_1
                LEFT JOIN '.$wpdb->prefix.'matiere AS M ON M.code = C.code_matiere
                WHERE '.$condAffectation.' AND E.crpcen = "'.$user->crpcen.'" '.$where.'
                ORDER BY Q.creation_date DESC
                ';
        return $query;
    }
    //End FRONT

    public function manageCollaborator($notary, $data)
    {
        // check id collaborator
        if (isset($data['collaborator_id']) && intval($data['collaborator_id']) > 0) { // update
            $this->updateCollaborator($data);
        } else { // create
            $this->addCollaborator($notary, $data);
        }

    }

    /**
     * Add new collaborator
     *
     * @param mixed $notary
     * @param array $data
     * @return void
     * @throws Exception
     */
    public function addCollaborator($notary, $data)
    {
        global $cri_container;

        // collaborator data
        $collaborator                              = array();
        $collaborator['first_name']                = isset($data['collaborator_first_name']) ? esc_sql($data['collaborator_first_name']) : '';
        $collaborator['last_name']                 = isset($data['collaborator_last_name']) ? esc_sql($data['collaborator_last_name']) : '';
        $collaborator['email_adress']              = isset($data['collaborator_email']) ? esc_sql($data['collaborator_email']) : '';
        $collaborator['tel']                       = isset($data['collaborator_tel']) ? esc_sql($data['collaborator_tel']) : '';
        $collaborator['tel_portable']              = isset($data['collaborator_tel_portable']) ? esc_sql($data['collaborator_tel_portable']) : '';
        $collaborator['id_fonction_collaborateur'] = isset($data['collaborator_function']) ? esc_sql($data['collaborator_function']) : 0;
        $collaborator['id_fonction']               = CONST_NOTAIRE_COLLABORATEUR;

        // @todo data from notary to be confirmed
        $collaborator['client_number'] = $notary->client_number;
        $collaborator['crpcen']        = $notary->crpcen;

        // insert into cri_notaire
        $collaboratorId = $this->create($collaborator);

        /**
         * insert into cri_users
         */
        // check if user already exist
        $existingUsers = $cri_container->get('tools')->getWpUsers();
        $userName      = $notary->crpcen . CONST_LOGIN_SEPARATOR . $collaboratorId;
        $displayName   = $collaborator['first_name'] . ' ' . $collaborator['last_name'];

        if (!in_array($userName, $existingUsers['username'])) {
            // query builder options
            $options               = array();
            $options['table']      = 'users';
            $options['attributes'] = 'user_login, user_nicename, user_email, user_registered, user_status,  display_name';

            // prepare values
            $value = "'" . esc_sql($userName) . "', ";
            $value .= "'" . sanitize_title($displayName) . "', ";
            $value .= "'" . $collaborator['email_adress'] . "', ";
            $value .= "'" . date('Y-m-d H:i:s') . "', ";
            $value .= CONST_STATUS_ENABLED . ", ";
            $value .= "'" . esc_sql($displayName) . "'";

            $options['values'] = $value;

            // insert data into cri_users
            mvc_model('QueryBuilder')->insert($options);

            // update id_wp_user
            $collaborator = new stdClass();
            $collaborator->id = $collaboratorId;
            $collaborator->crpcen = $notary->crpcen;
            $this->updateCriNotaireWpUserId(array($collaborator));

            // manage roles
            $user = $this->getAssociatedUserByNotaryId($collaboratorId);
            // add posted roles from data
            foreach (Config::$notaryRoles as $role => $label) {
                if (isset($data[$role])) {
                    $user->add_role($role);
                }
            }
            // add default role (Acs : accès aux bases de connaissance (par défaut par tout le monde))
            $user->add_role(CONST_NOTAIRE_ROLE);
        }
    }

    /**
     * Update collaborator data
     *
     * @param array $data
     * @return void
     */
    public function updateCollaborator($data)
    {
        // collaborator data
        $collaborator                              = array();
        $collaborator['first_name']                = isset($data['collaborator_first_name']) ? esc_sql($data['collaborator_first_name']) : '';
        $collaborator['last_name']                 = isset($data['collaborator_last_name']) ? esc_sql($data['collaborator_last_name']) : '';
        $collaborator['email_adress']              = isset($data['collaborator_email']) ? esc_sql($data['collaborator_email']) : '';
        $collaborator['tel']                       = isset($data['collaborator_tel']) ? esc_sql($data['collaborator_tel']) : '';
        $collaborator['tel_portable']              = isset($data['collaborator_tel_portable']) ? esc_sql($data['collaborator_tel_portable']) : '';
        $collaborator['id_fonction_collaborateur'] = isset($data['collaborator_function']) ? esc_sql($data['collaborator_function']) : 0;

        // update cri_notaire data
        $collaborator['id'] = $data['collaborator_id'];
        if ($this->save($collaborator)) { // successful update
            // manage roles
            $user = $this->getAssociatedUserByNotaryId($collaborator['id']);
            // reset all roles
            $this->resetUserRoles($user);

            // add new posted roles in data
            foreach (Config::$notaryRoles as $role => $label) {
                if (isset($data[$role])) {
                    $user->add_role($role);
                }
            }
        }
    }

    /**
     * Get associated user by notary id
     *
     * @param int $id
     * @return void|WP_User
     * @throws Exception
     */
    public function getAssociatedUserByNotaryId($id)
    {
        // get notary data
        $notary = mvc_model('QueryBuilder')->findOne('notaire',
                                                     array(
                                                         'fields' => 'id, id_wp_user, crpcen',
                                                         'conditions' => 'id = ' . $id,
                                                     )
        );
        // get notary associated user
        if (is_object($notary) && $notary->id_wp_user) {
            $user = new WP_User($notary->id_wp_user);

            // check if user is a WP_user vs WP_error
            if ($user instanceof WP_User && is_array($user->roles)) {
                return $user;
            }
        }
        return;
    }

    /**
     * Rest all user roles defined in \Config::$notaryRoles
     *
     * @param mixed $user
     * @return void
     */
    protected function resetUserRoles($user)
    {
        foreach (Config::$notaryRoles as $role => $label) {
            $user->remove_role($role);
        }
    }

    /**
     * Update notary and office data
     *
     * @param int    $id
     * @param string $crpcen
     * @throws Exception
     */
    public function updateProfil($id, $crpcen)
    {
        if ($id) {
            // flag for updating data
            $updateAction = false;
            // init  notary data
            $notary = array();
            // init  office data
            $office = array();
            // notary first_name
            if (isset($_POST['notary_first_name'])) {
                $notary['first_name'] = $_POST['notary_first_name'];
                $updateAction = true;
            }
            // notary last_name
            if (isset($_POST['notary_last_name'])) {
                $notary['last_name'] = $_POST['notary_last_name'];
                $updateAction = true;
            }
            // notary email_adress
            if (isset($_POST['notary_email_adress'])) {
                $notary['email_adress'] = $_POST['notary_email_adress'];
                $updateAction = true;
            }
            // notary tel
            if (isset($_POST['notary_tel'])) {
                $notary['tel'] = $_POST['notary_tel'];
                $updateAction = true;
            }
            // notary tel_portable
            if (isset($_POST['notary_tel_portable'])) {
                $notary['tel_portable'] = $_POST['notary_tel_portable'];
                $updateAction = true;
            }
            // notary fax
            if (isset($_POST['notary_fax'])) {
                $notary['fax'] = $_POST['notary_fax'];
                $updateAction = true;
            }

            // office adress_1
            if (isset($_POST['office_adress_1'])) {
                $office['adress_1'] = $_POST['office_adress_1'];
                $updateAction = true;
            }
            // office adress_2
            if (isset($_POST['office_adress_2'])) {
                $office['adress_2'] = $_POST['office_adress_2'];
                $updateAction = true;
            }
            // office adress_3
            if (isset($_POST['office_adress_3'])) {
                $office['adress_3'] = $_POST['office_adress_3'];
                $updateAction = true;
            }
            // office cp
            if (isset($_POST['office_cp'])) {
                $office['cp'] = $_POST['office_cp'];
                $updateAction = true;
            }
            // office city
            if (isset($_POST['office_city'])) {
                $office['city'] = $_POST['office_city'];
                $updateAction = true;
            }
            // office office_email_adress_1
            if (isset($_POST['office_email_adress_1'])) {
                $office['office_email_adress_1'] = $_POST['office_email_adress_1'];
                $updateAction = true;
            }
            // office tel
            if (isset($_POST['office_tel'])) {
                $office['tel'] = $_POST['office_tel'];
                $updateAction = true;
            }
            // office fax
            if (isset($_POST['office_fax'])) {
                $office['fax'] = $_POST['office_fax'];
                $updateAction = true;
            }
            // update data
            if ($updateAction) {
                // notary
                $notary['id'] = $id;
                $data = array(
                    'Notaire' => $notary
                );
                $this->save($data);

                // office
                $office['crpcen'] = $crpcen;
                $data = array(
                    'Etude' => $office
                );
                mvc_model('Etude')->save($data);
            }
        }
    }

    /**
     * Inscription/desinscription newsletter
     *
     * @param mixed $notaryData notary user data
     */
    public function newsletterSubscription($notaryData)
    {
        $disabled = $_POST['disabled'] == 1 ? 0 : 1;
        $update = array();
        $update['Notaire']['id'] = $notaryData->id;
        $update['Notaire']['newsletter'] = $disabled;
        $this->save($update);
    }

    /**
     * Gestion centre d'interet
     *
     * @param mixed $notaryData notary user data
     * @param array $data data to insert
     */
    public function manageInterest($notaryData, $data)
    {
        // post matieres
        $options = array(
            'conditions' => array(
                'Matiere.displayed' => 1
            )
        );
        $matieres = mvc_model('matiere')->find($options);
        //Clean $_POST before
        $toCompare = array();
        //Create array to compare Matiere in $_POST
        foreach ($matieres as $mat) {
            $toCompare[] = $mat->id;
        }
        $insert = array();
        $insert['Notaire']['id'] = $notaryData->id;
        $insert['Notaire']['Matiere']['ids'] = array();
        if (isset($data['matieres'])) {
            foreach ($data['matieres'] as $v) {
                //Check if current Matiere is valid
                if (in_array($v, $toCompare)) {
                    $insert['Notaire']['Matiere']['ids'][] = $v;
                }
            }
        }
        //Put in DB
        $this->save($insert);
    }

    /**
     * Find all notary by optimized query
     *
     * @param array $options
     * @return mixed
     * @throws Exception
     */
    public function find($options = array())
    {
        return (is_array($options) && count($options) > 0) ? parent::find($options) : mvc_model('QueryBuilder')->findAll('notaire');
    }

    /**
     * Get list of office members
     *
     * @param mixed $notary
     * @return mixed
     * @throws Exception
     */
    public function listOfficeMembers($notary)
    {
        $options = array(
            'conditions' => array(
                'cn.crpcen'      => $notary->crpcen,
                'cu.user_status' => CONST_STATUS_ENABLED
            ),
            'synonym'    => 'cn',
            'join'       => array(
                array(
                    'table'  => 'users cu',
                    'column' => ' cn.id_wp_user = cu.ID'
                )
            )
        );

        return mvc_model('QueryBuilder')->findAll('notaire', $options, 'cn.id');
    }

    /**
     * Check email changed
     *
     * @param int    $notary_id
     * @param string $new_email
     * @return bool
     * @throws Exception
     */
    public function isEmailChanged($notary_id, $new_email)
    {
        $options = array(
            'conditions' => array(
                'cn.id'      => $notary_id
            ),
            'synonym'    => 'cn'
        );

        $notary = mvc_model('QueryBuilder')->findOne('notaire', $options, 'cn.id');

        return (is_object($notary) && $notary->email_adress != $new_email);
    }
}
