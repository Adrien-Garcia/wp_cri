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
     * @return mixed
     */
    public function importIntoWpUsers()
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
                    $this->importDataUsingDBconnect();
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
     * @throws Exception
     *
     * @return void
     */
    protected function importDataUsingDBconnect()
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
            $this->manageNotaireData();

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
            reportError(CONST_EMAIL_ERROR_CATCH_EXCEPTION, $e->getMessage());
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

                        if (isset($newData[$adapter::NOTAIRE_FAX]))
                            $updateTelValues[]         = " crpcen = {$currentData->crpcen} THEN '" . esc_sql($newData[$adapter::NOTAIRE_FAX]) . "' ";

                        if (isset($newData[$adapter::NOTAIRE_PORTABLE]))
                            $updateFaxValues[]      = " crpcen = {$currentData->crpcen} THEN '" . esc_sql($newData[$adapter::NOTAIRE_PORTABLE]) . "' ";
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
            reportError(CONST_EMAIL_ERROR_CATCH_EXCEPTION, $e->getMessage());
        }

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
            writeLog($e, 'notaire.log');
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
            writeLog($e, 'notaire.log');
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

        if( self::$userConnectedData !== null && is_object(self::$userConnectedData) && self::$userConnectedData instanceof MvcModelObject ){
            $object = self::$userConnectedData;
        } else {
            $object = $this->find_one_by_id_wp_user($current_user->ID);
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
                    reportError(CONST_EMAIL_ERROR_CORRUPTED_FILE, 'Solde');
                }
            } else { // file doesn't exist
                // write into logfile
                $error = sprintf(CONST_EMAIL_ERROR_CONTENT, 'Solde');
                writeLog($error, 'solde.log');

                // send email
                reportError(CONST_EMAIL_ERROR_CONTENT, 'Solde');
            }
        } catch (Exception $e) {
            // archive file
            if (isset($files[0]) && is_file($files[0])) {
                rename($files[0], str_replace(".csv", ".csv." . date('YmdHi'), $files[0]));
            }

            // write write into logfile
            writeLog($e, 'solde.log');

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
                && (strcasecmp($object->category, CONST_OFFICES_ROLE) === 0)
                && isset($object->id_fonction)
                && in_array($object->id_fonction, Config::$canAccessFinance)
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
        return true;
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
    public function paginate($options = array(),$status){
        global $wpdb;
        $options['page'] = empty($options['page']) ? 1 : intval($options['page']);//for limit
        $limit = $this->db_adapter->get_limit_sql($options);      
        if(!is_admin()){
            $user = CriNotaireData();//get Notaire
            $where = $this->getFilters($options);//Filter
            $query = $this->prepareQueryForFront($status,$where, $limit);
            //Total query for pagination
            $query_count ='
                SELECT COUNT(*) AS count 
                FROM '.$wpdb->prefix.'question AS Q
                JOIN '.$wpdb->prefix.'notaire AS N ON Q.client_number = N.client_number
                JOIN '.$wpdb->prefix.'etude AS E ON E.crpcen = N.crpcen 
                LEFT JOIN '.$wpdb->prefix.'competence AS C ON C.id = Q.id_competence_1 
                LEFT JOIN '.$wpdb->prefix.'matiere AS M ON M.code = C.code_matiere
                WHERE Q.treated  = 2 AND E.crpcen = "'.$user->crpcen.'" '.$where.'
                ORDER BY Q.creation_date DESC 
                '; 
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
        $where = array();
        foreach ( $options as $k => $v ){
            $v = esc_sql(strip_tags($v));
            //Filtre par matière (id)
            if( $k == 'm' && !empty($v) && is_numeric($v)){
                $where[] = ' M.id = "'.$v.'"';continue;
            }
            
            //Filtre par date de création
            if( in_array($k,array('d1','d2'))&& !empty($v)){
                $d = $this->convertToDateSql($v);
                if( !$d ) continue;

                if( $k == 'd1' ){
                    $date = " Q.creation_date >= '{$d}'";
                }else{
                    $date = " Q.creation_date <= '{$d}'";
                }
                $where[] = $date;continue;
            }

            //Filtre par nom de notaire
            if( $k == 'n' && !empty($v)){
                $v = urldecode($v);
                $where[] = " CONCAT(N.first_name,N.last_name) LIKE '%{$v}%'";
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
    public function getPending($options,$status){
        $where = $this->getFilters($options);//Filter
        $query = $this->prepareQueryForFront( $status,$where );
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
    protected function prepareQueryForFront($status,$where,$limit = ''){
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
                    ORDER BY Q.creation_date DESC 
                    '.$limit.'
                 ) [Question] q
            LEFT JOIN Document d ON (d.id_externe = q.id AND d.type = "question" ) 
            LEFT JOIN Support s ON s.id = q.id_support
            LEFT JOIN Competence c ON c.id = q.id_competence_1 
            LEFT JOIN Matiere m ON m.code = c.code_matiere
            JOIN Notaire n ON n.client_number = q.client_number
                ';
        return $query;
    }
    //End FRONT
}