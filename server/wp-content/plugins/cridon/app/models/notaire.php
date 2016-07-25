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
            // delete duplicate values
            $this->erpEtudeList = array_unique($this->erpEtudeList);

            // set list of existing notaire
            $this->setSiteNotaireList();

            // insert or update data
            $this->manageNotaireData($force);

            // set list of existing etude
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
                $queryStart = " UPDATE `{$this->table}` SET ";
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
                                $updateCategValues[]        = " {$currentData->id} THEN '" . esc_sql($newData[$adapter::NOTAIRE_CATEG]) . "' ";

                            if (isset($newData[$adapter::NOTAIRE_NUMCLIENT]))
                                $updateNumclientValues[]    = " {$currentData->id} THEN '" . esc_sql($newData[$adapter::NOTAIRE_NUMCLIENT]) . "' ";

                            if (isset($newData[$adapter::NOTAIRE_FNAME]))
                                $updateFirstnameValues[]    = " {$currentData->id} THEN '" . esc_sql($newData[$adapter::NOTAIRE_FNAME]) . "' ";

                            if (isset($newData[$adapter::NOTAIRE_LNAME]))
                                $updateLastnameValues[]     = " {$currentData->id} THEN '" . esc_sql($newData[$adapter::NOTAIRE_LNAME]) . "' ";

                            if (isset($newData[$adapter::NOTAIRE_PWDTEL]))
                                $updatePwdtelValues[]       = " {$currentData->id} THEN '" . esc_sql($newData[$adapter::NOTAIRE_PWDTEL]) . "' ";

                            if (isset($newData[$adapter::NOTAIRE_INTERCODE]))
                                $updateInterCodeValues[]    = " {$currentData->id} THEN '" . esc_sql($newData[$adapter::NOTAIRE_INTERCODE]) . "' ";

                            if (isset($newData[$adapter::NOTAIRE_CIVILIT]))
                                $updateCivlitValues[]       = " {$currentData->id} THEN '" . esc_sql($newData[$adapter::NOTAIRE_CIVILIT]) . "' ";

                            if (isset($newData[$adapter::NOTAIRE_EMAIL]))
                                $updateEmailValues[]        = " {$currentData->id} THEN '" . esc_sql($newData[$adapter::NOTAIRE_EMAIL]) . "' ";

                            if (isset($newData[$adapter::NOTAIRE_FONC]))
                                $updateFoncValues[]         = " {$currentData->id} THEN '" . esc_sql($newData[$adapter::NOTAIRE_FONC]) . "' ";

                            if (isset($newData[$adapter::NOTAIRE_TEL]))
                                $updateTelValues[]         = " {$currentData->id} THEN '" . esc_sql($newData[$adapter::NOTAIRE_TEL]) . "' ";

                            if (isset($newData[$adapter::NOTAIRE_FAX]))
                                $updateFaxValues[]         = " {$currentData->id} THEN '" . esc_sql($newData[$adapter::NOTAIRE_FAX]) . "' ";

                            if (isset($newData[$adapter::NOTAIRE_PORTABLE]))
                                $updateMobileValues[]      = " {$currentData->id} THEN '" . esc_sql($newData[$adapter::NOTAIRE_PORTABLE]) . "' ";

                            $updateDateModified[]          = " {$currentData->id} THEN '" . $dateModified . "' ";
                        }
                    }
                    // end optimisation
                }

                // execute update query
                $notaireQuery = array();
                if (count($updateCategValues) > 0) {
                    // category
                    $notaireQuery[] = ' `category` = CASE id WHEN ' . implode(' WHEN ', $updateCategValues) . ' ELSE `category` ';
                }

                if (count($updateNumclientValues) > 0) {
                    // client_number
                    $notaireQuery[] = ' `client_number` = CASE id WHEN ' . implode(' WHEN ', $updateNumclientValues) . ' ELSE `client_number` ';
                }

                if (count($updateFirstnameValues) > 0) {
                    // first_name
                    $notaireQuery[] = ' `first_name` = CASE id WHEN ' . implode(' WHEN ', $updateFirstnameValues) . ' ELSE `first_name` ';
                }

                if (count($updateLastnameValues) > 0) {
                    // last_name
                    $notaireQuery[] = ' `last_name` = CASE id WHEN ' . implode(' WHEN ', $updateLastnameValues) . ' ELSE `last_name` ';
                }

                if (count($updatePwdtelValues) > 0) {
                    // tel_password
                    $notaireQuery[] = ' `tel_password` = CASE id WHEN ' . implode(' WHEN ', $updatePwdtelValues) . ' ELSE `tel_password` ';
                }

                if (count($updateInterCodeValues) > 0) {
                    // code_interlocuteur
                    $notaireQuery[] = ' `code_interlocuteur` = CASE id WHEN ' . implode(' WHEN ', $updateInterCodeValues) . ' ELSE `code_interlocuteur` ';
                }

                if (count($updateCivlitValues) > 0) {
                    // id_civilite
                    $notaireQuery[] = ' `id_civilite` = CASE id WHEN ' . implode(' WHEN ', $updateCivlitValues) . ' ELSE `id_civilite` ';
                }

                if (count($updateEmailValues) > 0) {
                    // email_adress
                    $notaireQuery[] = ' `email_adress` = CASE id WHEN ' . implode(' WHEN ', $updateEmailValues) . ' ELSE `email_adress` ';
                }

                if (count($updateFoncValues) > 0) {
                    // id_fonction
                    $notaireQuery[] = ' `id_fonction` = CASE id WHEN ' . implode(' WHEN ', $updateFoncValues) . ' ELSE `id_fonction` ';
                }

                if (count($updateTelValues) > 0) {
                    // tel
                    $notaireQuery[] = ' `tel` = CASE id WHEN ' . implode(' WHEN ', $updateTelValues) . ' ELSE `tel` ';
                }

                if (count($updateFaxValues) > 0) {
                    // fax
                    $notaireQuery[] = ' `fax` = CASE id WHEN ' . implode(' WHEN ', $updateFaxValues) . ' ELSE `fax` ';
                }

                if (count($updateMobileValues) > 0) {
                    // tel_portable
                    $notaireQuery[] = ' `tel_portable` = CASE id WHEN ' . implode(' WHEN ', $updateMobileValues) . ' ELSE `tel_portable` ';
                }

                if (count($updateDateModified) > 0) {
                    // date_modified
                    $notaireQuery[] = ' `date_modified` = CASE id WHEN ' . implode(' WHEN ', $updateDateModified) . ' ELSE `date_modified` ';
                }

                // execute prepared query
                /**
                 * Sous la forme :
                 * UPDATE cri_notaire SET
                    `category` = CASE id WHEN id1 THEN 'OFF' WHEN id2 THEN 'DIV' ELSE `category` END,
                    `code_interlocuteur` = CASE id WHEN id1 THEN 'code1' WHEN id2 THEN 'whatever' ELSE `code_interlocuteur` END;
                 * @see http://stackoverflow.com/questions/13673890/mysql-case-to-update-multiple-columns
                 */
                if (count($notaireQuery) > 0) {
                    $this->wpdb->query($queryStart . implode(' END, ', $notaireQuery) . $queryEnd);

                    // log query
                    if (getenv('ENV') != PROD) {
                        writeLog($queryStart . implode(' END, ', $notaireQuery) . $queryEnd, '$notaireQuery.log');
                    }

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
                    // import only if empty YIDNOT_0 : to be sure for new Notary data
                    $notaryId = trim($this->erpNotaireData[$notaire][$adapter::NOTAIRE_YIDNOT_0]);
                    if (empty($notaryId)) {

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
                    } else { // changement de mot de passe
                        $newWebPwd   = $this->erpNotaireData[$notaire][$adapter::NOTAIRE_PWDWEB];
                        $newTelPwd   = $this->erpNotaireData[$notaire][$adapter::NOTAIRE_PWDTEL];
                        $this->updatePwd($notaryId, $newWebPwd, $newTelPwd);
                        // free vars
                        unset($newWebPwd);
                        unset($newTelPwd);
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
            $updateTelValues = $updateFaxValues = $updateLevelValues = $updateStartDateValues = $updateEndDateValues = array();
            $updateEcheanceDateValues = $updateNextLevelValues = array();

            // list of new data
            $newEtudes = $this->getNewEtudeList();

            // list of data for update
            $updateEtudeList = $this->getEtudeToBeUpdated();

            // etude table
            $etudeTable  = mvc_model('etude')->table;

            // update
            if (count($updateEtudeList) > 0) {
                // start/end query block
                $queryStart = " UPDATE `{$etudeTable}` SET ";
                $queryEnd   = ' END ';

                $options = array('group'=>'Etude.crpcen');

                foreach(mvc_model('etude')->find($options) as $currentData) {
                    $key = $currentData->crpcen;

                    // start optimisation
                    if (array_key_exists($key, $updateEtudeList)) {
                        $newData = $updateEtudeList[$key];

                        // prepare all update   query
                        if (isset($newData[$adapter::NOTAIRE_SIGLE]))
                            $updateSigleValues[]        = " {$currentData->crpcen} THEN '" . esc_sql($newData[$adapter::NOTAIRE_SIGLE]) . "' ";

                        if (isset($newData[$adapter::NOTAIRE_OFFICENAME]))
                            $updateOfficenameValues[]    = " {$currentData->crpcen} THEN '" . esc_sql($newData[$adapter::NOTAIRE_OFFICENAME]) . "' ";

                        if (isset($newData[$adapter::NOTAIRE_ADRESS1]))
                            $updateAdress1Values[]    = " {$currentData->crpcen} THEN '" . esc_sql($newData[$adapter::NOTAIRE_ADRESS1]) . "' ";

                        if (isset($newData[$adapter::NOTAIRE_ADRESS2]))
                            $updateAdress2Values[]     = " {$currentData->crpcen} THEN '" . esc_sql($newData[$adapter::NOTAIRE_ADRESS2]) . "' ";

                        if (isset($newData[$adapter::NOTAIRE_ADRESS3]))
                            $updateAdress3Values[]       = " {$currentData->crpcen} THEN '" . esc_sql($newData[$adapter::NOTAIRE_ADRESS3]) . "' ";

                        if (isset($newData[$adapter::NOTAIRE_CP]))
                            $updateCpValues[]    = " {$currentData->crpcen} THEN '" . esc_sql($newData[$adapter::NOTAIRE_CP]) . "' ";

                        if (isset($newData[$adapter::NOTAIRE_CITY]))
                            $updateCityValues[]       = " {$currentData->crpcen} THEN '" . esc_sql($newData[$adapter::NOTAIRE_CITY]) . "' ";

                        if (isset($newData[$adapter::NOTAIRE_MAIL1]))
                            $updateEmail1Values[]        = " {$currentData->crpcen} THEN '" . esc_sql($newData[$adapter::NOTAIRE_MAIL1]) . "' ";

                        if (isset($newData[$adapter::NOTAIRE_MAIL2]))
                            $updateEmail2Values[]         = " {$currentData->crpcen} THEN '" . esc_sql($newData[$adapter::NOTAIRE_MAIL2]) . "' ";

                        if (isset($newData[$adapter::NOTAIRE_MAIL3]))
                            $updateEmail3Values[]         = " {$currentData->crpcen} THEN '" . esc_sql($newData[$adapter::NOTAIRE_MAIL3]) . "' ";

                        if (isset($newData[$adapter::NOTAIRE_OFFICETEL]))
                            $updateTelValues[]      = " {$currentData->crpcen} THEN '" . esc_sql($newData[$adapter::NOTAIRE_OFFICETEL]) . "' ";

                        if (isset($newData[$adapter::NOTAIRE_OFFICEFAX]))
                            $updateFaxValues[]         = " {$currentData->crpcen} THEN '" . esc_sql($newData[$adapter::NOTAIRE_OFFICEFAX]) . "' ";

                        if (isset($newData[$adapter::NOTAIRE_YNIVEAU_0]) && $newData[$adapter::NOTAIRE_YNIVEAU_0] < $currentData->subscription_level){
                            if (isset($newData[$adapter::NOTAIRE_YVALDEB_0]) && date('Y-m-d',strtotime($newData[$adapter::NOTAIRE_YVALDEB_0])) >= $currentData->start_subscription_date){
                                if (!empty($newData[$adapter::NOTAIRE_YMOTIF_0])){
                                    if (in_array($newData[$adapter::NOTAIRE_YMOTIF_0],Config::$motiveImmediateUpdate)
                                        && isset($newData[$adapter::NOTAIRE_YVALFIN_0]) && isset($newData[$adapter::NOTAIRE_YDATECH_0])){
                                        $updateLevelValues[]         = " {$currentData->crpcen} THEN '" . esc_sql($newData[$adapter::NOTAIRE_YNIVEAU_0]) . "' ";
                                        $updateStartDateValues[]     = " {$currentData->crpcen} THEN '" . esc_sql(date('Y-m-d',strtotime($newData[$adapter::NOTAIRE_YVALDEB_0]))) . "' ";
                                        $updateEndDateValues[]       = " {$currentData->crpcen} THEN '" . esc_sql(date('Y-m-d',strtotime($newData[$adapter::NOTAIRE_YVALFIN_0]))) . "' ";
                                        $updateEcheanceDateValues[]  = " {$currentData->crpcen} THEN '" . esc_sql(date('Y-m-d',strtotime($newData[$adapter::NOTAIRE_YDATECH_0]))) . "' ";
                                    } else {
                                        $updateNextLevelValues[]     = " {$currentData->crpcen} THEN '" . esc_sql($newData[$adapter::NOTAIRE_YNIVEAU_0]) . "' ";
                                    }
                                }
                            }
                        }
                    }
                    // end optimisation
                }

                // execute update query
                $etudeQuery = array();
                if (count($updateSigleValues) > 0) {
                    // id_sigle
                    $etudeQuery[] = ' `id_sigle` = CASE crpcen WHEN ' . implode(' WHEN ', $updateSigleValues) . ' ELSE `id_sigle` ';
                    // office_name
                    $etudeQuery[] = ' `office_name` = CASE crpcen WHEN ' . implode(' WHEN ', $updateOfficenameValues) . ' ELSE `office_name` ';
                    // adress_1
                    $etudeQuery[] = ' `adress_1` = CASE crpcen WHEN ' . implode(' WHEN ', $updateAdress1Values) . ' ELSE `adress_1` ';
                    // adress_2
                    $etudeQuery[] = ' `adress_2` = CASE crpcen WHEN ' . implode(' WHEN ', $updateAdress2Values) . ' ELSE `adress_2` ';
                    // adress_3
                    $etudeQuery[] = ' `adress_3` = CASE crpcen WHEN ' . implode(' WHEN ', $updateAdress3Values) . ' ELSE `adress_3` ';
                    // cp
                    $etudeQuery[] = ' `cp` = CASE crpcen WHEN ' . implode(' WHEN ', $updateCpValues) . ' ELSE `cp` ';
                    // city
                    $etudeQuery[] = ' `city` = CASE crpcen WHEN ' . implode(' WHEN ', $updateCityValues) . ' ELSE `city` ';
                    // office_email_adress_1
                    $etudeQuery[] = ' `office_email_adress_1` = CASE crpcen WHEN ' . implode(' WHEN ', $updateEmail1Values) . ' ELSE `office_email_adress_1` ';
                    // office_email_adress_2
                    $etudeQuery[] = ' `office_email_adress_2` = CASE crpcen WHEN ' . implode(' WHEN ', $updateEmail2Values) . ' ELSE `office_email_adress_2` ';
                    // office_email_adress_3
                    $etudeQuery[] = ' `office_email_adress_3` = CASE crpcen WHEN ' . implode(' WHEN ', $updateEmail3Values) . ' ELSE `office_email_adress_3` ';
                    // tel
                    $etudeQuery[] = ' `tel` = CASE crpcen WHEN ' . implode(' WHEN ', $updateTelValues) . ' ELSE `tel` ';
                    // fax
                    $etudeQuery[] = ' `fax` = CASE crpcen WHEN ' . implode(' WHEN ', $updateFaxValues) . ' ELSE `fax` ';
                }
                if (count($updateLevelValues) > 0) {
                    // subscription_level
                    $etudeQuery[] = ' `subscription_level` = CASE crpcen WHEN ' . implode(' WHEN ', $updateLevelValues) . ' ELSE `subscription_level` ';
                }
                if (count($updateStartDateValues) > 0) {
                    // start_subscription_date
                    $etudeQuery[] = ' `start_subscription_date` = CASE crpcen WHEN ' . implode(' WHEN ', $updateStartDateValues) . ' ELSE `start_subscription_date` ';
                }
                if (count($updateEndDateValues) > 0) {
                    // end_subscription_date
                    $etudeQuery[] = ' `end_subscription_date` = CASE crpcen WHEN ' . implode(' WHEN ', $updateEndDateValues) . ' ELSE `end_subscription_date` ';
                }
                if (count($updateEcheanceDateValues) > 0) {
                    // echeance_subscription_date
                    $etudeQuery[] = ' `echeance_subscription_date` = CASE crpcen WHEN ' . implode(' WHEN ', $updateEcheanceDateValues) . ' ELSE `echeance_subscription_date` ';
                }
                if (count($updateNextLevelValues) > 0) {
                    // next_subscription_level
                    $etudeQuery[] = ' `next_subscription_level` = CASE crpcen WHEN ' . implode(' WHEN ', $updateNextLevelValues) . ' ELSE `next_subscription_level` ';
                }

                // execute prepared query
                /**
                 * Sous la forme :
                 * UPDATE cri_etude SET
                `id_sigle` = CASE crpcen WHEN 'crpcen1' THEN 5 WHEN 'crpcen2' THEN 3 ELSE `id_sigle` END,
                `subscription_level` = CASE crpcen WHEN 'crpcen1' THEN '' WHEN 'crpcen2' THEN 'whatever' ELSE `subscription_level` END;
                 * @see http://stackoverflow.com/questions/13673890/mysql-case-to-update-multiple-columns
                 */
                if (count($etudeQuery) > 0) {
                    $this->wpdb->query($queryStart . implode(' END, ', $etudeQuery) . $queryEnd);

                    // log query
                    if (getenv('ENV') != PROD) {
                        writeLog($queryStart . implode(' END, ', $etudeQuery) . $queryEnd, '$etudeQuery.log');
                    }

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
     * Set role for all notary
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
                    $user->add_role(CONST_COLLABORATEUR_TAB_ROLE);
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
            $options = array (
                'synonym' => 'n',
                'join' => array(
                    array(
                        'table'  => 'users u',
                        'column' => ' n.id_wp_user = u.id'
                    ),
                )
            );
            $notaires = mvc_model('QueryBuilder')->findAll('notaire', $options, 'n.id');

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

                // init list of new notary user
                $newNotaryUsers = array();

                foreach ($notaires as $notaire) {
                    // unique key
                    $uniqueKey = $notaire->crpcen . $notaire->web_password;

                    // check if user already exist
                    $userName = $notaire->crpcen . CONST_LOGIN_SEPARATOR . $notaire->id;

                    $displayName = $notaire->first_name . ' ' . $notaire->last_name;

                    // set user status
                    if (isset($this->erpNotaireData[$uniqueKey]) && isset($this->erpNotaireData[$uniqueKey][$adapter::NOTAIRE_STATUS])) {
                        $userStatus = $this->erpNotaireData[$uniqueKey][$adapter::NOTAIRE_STATUS];
                    } elseif (strtotime($notaire->user_registered. "+1 week") > time() && $notaire->user_status === CONST_STATUS_ENABLED) {
                        // don't deactivate a user if he's not created on the ERP yet
                        $userStatus = CONST_STATUS_ENABLED;
                    } else {
                        $userStatus = CONST_STATUS_DISABLED;
                    }

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

                        // get user level
                        $notaire->level = (!empty($this->erpNotaireData[$uniqueKey][$adapter::NOTAIRE_YNIVEAU])) ? $this->erpNotaireData[$uniqueKey][$adapter::NOTAIRE_YNIVEAU] : 0;
                        // fill list of new notaries
                        $newNotaryUsers[] = $notaire;

                    } else { // prepare the bulk update query
                        if ($notaire->id_wp_user) {
                            // pwd
                            $bulkPwdUpdate[] = " {$notaire->id_wp_user} THEN '" . wp_hash_password($notaire->web_password) . "' ";
                            // nicename
                            $bulkNiceNameUpdate[] = " {$notaire->id_wp_user} THEN '" . sanitize_title($displayName) . "' ";
                            // status
                            $bulkStatusUpdate[] = " {$notaire->id_wp_user} THEN " . $userStatus . " ";
                            // email
                            $bulkEmailUpdate[] = " {$notaire->id_wp_user} THEN '" . $notaire->email_adress . "' ";
                            // display name
                            $bulkDisplayNameUpdate[] = " ID = {$notaire->id_wp_user} THEN '" . esc_sql($displayName) . "' ";

                            // get user level
                            $notaire->level = (!empty($this->erpNotaireData[$uniqueKey][$adapter::NOTAIRE_YNIVEAU])) ? $this->erpNotaireData[$uniqueKey][$adapter::NOTAIRE_YNIVEAU] : 0;

                            // maj notary role
                            $this->majNotaireRole($notaire);
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

                    // set new notaries roles
                    $this->setNewNotaireRole($newNotaryUsers);

                    $this->importSuccess = true;
                }

                // execute the bulk update query
                $blockQuery = array();
                if (count($bulkPwdUpdate) > 0) {
                    // start/end query block
                    $queryStart = " UPDATE `{$this->wpdb->users}` SET ";
                    $queryEnd   = ' END ';

                    // pwd
                    $blockQuery[] = ' `user_pass` = CASE ID WHEN ' . implode(' WHEN ', $bulkPwdUpdate) . ' ELSE `user_pass` ';

                    // nicename
                    $blockQuery[] = ' `user_nicename` = CASE ID WHEN ' . implode(' WHEN ', $bulkNiceNameUpdate) . ' ELSE `user_nicename` ';

                    // status
                    $blockQuery[] = ' `user_status` = CASE ID WHEN ' . implode(' WHEN ', $bulkStatusUpdate) . ' ELSE `user_status` ';

                    // email
                    $blockQuery[] = ' `user_email` = CASE ID WHEN ' . implode(' WHEN ', $bulkEmailUpdate) . ' ELSE `user_email` ';

                    // display name
                    $blockQuery[] = ' `display_name` = CASE ID WHEN ' . implode(' WHEN ', $bulkDisplayNameUpdate) . ' ELSE `display_name` ';

                    // exec query block
                    $this->wpdb->query($queryStart . implode(' END, ', $blockQuery) . $queryEnd);

                    $this->importSuccess = true;
                }
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


    public function veillesSubscriptionManagement(){
        try {
            $options= array(
                'conditions' => array(
                    'Etude.a_transmettre' => CONST_CRIDONLINE_A_TRANSMETTRE_ERP
                ),
                'group' => 'Etude.crpcen'
            );
            $etudes   = mvc_model('Etude')->find($options);
            if (count($etudes)>0){
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
                $eList = array();

                // requete commune
                $queryStart  = " INTO " . CONST_DB_TABLE_ABONNE;
                $queryStart .= " (";
                $queryStart .= $adapter::YABONNE_YIDABONNE_0 . ", "; // YABONNE_YIDABONNE_0
                $queryStart .= $adapter::YABONNE_YCRPCEN_0 . ", "; // YABONNE_YCRPCEN_0
                $queryStart .= $adapter::YABONNE_YNIVEAU_0 . ", ";   // YABONNE_YNIVEAU_0
                $queryStart .= $adapter::YABONNE_YDATE_0 . ", ";   // YABONNE_YDATE_0
                $queryStart .= $adapter::YABONNE_YSTATUT_0 . ", ";   // YABONNE_YSTATUT_0
                $queryStart .= $adapter::YABONNE_YTARIF_0 . ", ";   // YABONNE_YTARIF_0
                $queryStart .= $adapter::YABONNE_YVALDEB_0 . ", ";   // YABONNE_YVALDEB_0
                $queryStart .= $adapter::YABONNE_YVALFIN_0 . ", ";   // YABONNE_YVALFIN_0
                $queryStart .= $adapter::YABONNE_YDATECH_0 . ", ";   // YABONNE_YDATECH_0
                $queryStart .= $adapter::YABONNE_YTRAITEE_0 . ", ";   // YABONNE_YTRAITEE_0
                $queryStart .= $adapter::YABONNE_YERR_0 . ", ";   // YABONNE_YERR_0
                $queryStart .= $adapter::YABONNE_YMESSERR_0;    // YABONNE_YMESSERR_0
                $queryStart .= ") ";
                $queryStart .= " VALUES ";

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
                        $updateTimestamp = time();
                        foreach ($etudes as $etude) {
                            // remplit la liste des études
                            $eList[] = $etude->crpcen;

                            $value  = $queryStart;
                            $value .= "(";

                            $value .= "'" . $etude->crpcen.' '.$updateTimestamp. "', "; // YABONNE_YIDABONNE_0
                            $value .= "'" . $etude->crpcen. "', "; // YABONNE_YCRPCEN_0
                            $value .= "'" . $etude->subscription_level. "', "; // YABONNE_YNIVEAU_0
                            $value .= "TO_DATE('" . date('d/m/Y') . "', 'dd/mm/yyyy'), "; // YABONNE_YDATE_0
                            $value .= "'1',"; // YABONNE_YSTATUT_0
                            $value .= "'" . $etude->subscription_price . "', "; // YABONNE_YTARIF_0
                            $value .= "TO_DATE('" . date('d/m/Y', strtotime($etude->start_subscription_date)) . "', 'dd/mm/yyyy'), "; // YABONNE_YVALDEB_0
                            $value .= "TO_DATE('" . date('d/m/Y', strtotime($etude->end_subscription_date)) . "', 'dd/mm/yyyy'), "; // YABONNE_YVALFIN_0
                            $value .= "TO_DATE('" . date('d/m/Y', strtotime($etude->echeance_subscription_date)) . "', 'dd/mm/yyyy'), "; // YABONNE_YDATECH_0
                            $value .= "'0',"; // YABONNE_YTRAITEE_0
                            $value .= "'0',"; // YABONNE_YERR_0
                            $value .= "' '"; // YABONNE_YMESSERR_0
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
                }
            }
            // execution requete
            if (!empty($query)) {
                if ($result = $this->adapter->execute($query) && !empty($eList)) {
                    // update cri_etude.a_transmettre
                    $sql = " UPDATE ". mvc_model('Etude')->table." SET a_transmettre = 0 WHERE crpcen IN (" . implode(', ', $eList) . ")";
                    $this->wpdb->query($sql);
                } else {
                    // log erreur
                    $error = sprintf(CONST_EXPORT_CRIDONLINE_ERROR, date('d/m/Y à H:i:s'));
                    writeLog($error, 'veillesSubscriptionManagement.log','Cridon - Export Cridonline');

                    // send email
                    reportError(CONST_EXPORT_CRIDONLINE_ERROR, $error);
                }
            }

            // status code
            return CONST_STATUS_CODE_OK;
        } catch (Exception $e) {
            // write into logfile
            writeLog($e, 'veillesSubscriptionManagement.log');
            // send email
            reportError(CONST_EMAIL_ERROR_CATCH_EXCEPTION, $e->getMessage(),'Cridon - Données notaire - Erreur mise à jour infos abonnement veilles');
        }
    }

    public function cridonlineEcheance (){
        try {
            $options= array(
                'conditions' => array(
                    'Etude.transmis_echeance' => CONST_CRIDONLINE_ECHEANCE_A_TRANSMETTRE_ERP,
                    'Etude.echeance_subscription_date <=' => date('Y-m-d')
                ),
                'group' => 'Etude.crpcen'
            );
            $etudes   = mvc_model('Etude')->find($options);
            if (count($etudes)>0){
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
                $eList = array();

                // requete commune
                $queryStart  = " INTO " . CONST_DB_TABLE_ABONNE;
                $queryStart .= " (";
                $queryStart .= $adapter::YABONNE_YIDABONNE_0 . ", "; // YABONNE_YIDABONNE_0
                $queryStart .= $adapter::YABONNE_YCRPCEN_0 . ", "; // YABONNE_YCRPCEN_0
                $queryStart .= $adapter::YABONNE_YNIVEAU_0 . ", ";   // YABONNE_YNIVEAU_0
                $queryStart .= $adapter::YABONNE_YDATE_0 . ", ";   // YABONNE_YDATE_0
                $queryStart .= $adapter::YABONNE_YSTATUT_0 . ", ";   // YABONNE_YSTATUT_0
                $queryStart .= $adapter::YABONNE_YTARIF_0 . ", ";   // YABONNE_YTARIF_0
                $queryStart .= $adapter::YABONNE_YVALDEB_0 . ", ";   // YABONNE_YVALDEB_0
                $queryStart .= $adapter::YABONNE_YVALFIN_0 . ", ";   // YABONNE_YVALFIN_0
                $queryStart .= $adapter::YABONNE_YDATECH_0 . ", ";   // YABONNE_YDATECH_0
                $queryStart .= $adapter::YABONNE_YTRAITEE_0 . ", ";   // YABONNE_YTRAITEE_0
                $queryStart .= $adapter::YABONNE_YERR_0 . ", ";   // YABONNE_YERR_0
                $queryStart .= $adapter::YABONNE_YMESSERR_0;    // YABONNE_YMESSERR_0
                $queryStart .= ") ";
                $queryStart .= " VALUES ";

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
                        $updateTimestamp = time();
                        foreach ($etudes as $etude) {
                            // remplit la liste des études
                            $eList[] = $etude->crpcen;

                            // Récupération niveau suivant + calcul tarif suivant le cas échéant
                            if (!empty($etude->next_subscription_level)){
                                $next_subscription_level = $etude->next_subscription_level;
                            } else {
                                $next_subscription_level = $etude->subscription_level;
                            }
                            $next_subscription_price = mvc_model('Etude')->getSubscriptionPrice($etude, true);

                            $start_subscription_date = date('Y-m-d', strtotime($etude->end_subscription_date));
                            $end_subscription_date = date('Y-m-d', strtotime($start_subscription_date.'+'. CONST_CRIDONLINE_SUBSCRIPTION_DURATION_DAYS . 'days'));
                            $echeance_subscription_date = date('Y-m-d', strtotime($end_subscription_date .'-'. CONST_CRIDONLINE_ECHEANCE_MONTH . 'month'));

                            $value  = $queryStart;
                            $value .= "(";

                            $value .= "'" . $etude->crpcen.' '.$updateTimestamp. "', "; // YABONNE_YIDABONNE_0
                            $value .= "'" . $etude->crpcen. "', "; // YABONNE_YCRPCEN_0
                            $value .= "'" . $next_subscription_level. "', "; // YABONNE_YNIVEAU_0
                            $value .= "TO_DATE('" . date('d/m/Y') . "', 'dd/mm/yyyy'), "; // YABONNE_YDATE_0
                            $value .= "'1',"; // YABONNE_YSTATUT_0
                            $value .= "'" . $next_subscription_price . "', "; // YABONNE_YTARIF_0
                            $value .= "TO_DATE('" . date('d/m/Y', strtotime($start_subscription_date)) . "', 'dd/mm/yyyy'), "; // YABONNE_YVALDEB_0
                            $value .= "TO_DATE('" . date('d/m/Y', strtotime($end_subscription_date)) . "', 'dd/mm/yyyy'), "; // YABONNE_YVALFIN_0
                            $value .= "TO_DATE('" . date('d/m/Y', strtotime($echeance_subscription_date)) . "', 'dd/mm/yyyy'), "; // YABONNE_YDATECH_0
                            $value .= "'0',"; // YABONNE_YTRAITEE_0
                            $value .= "'" . CONST_YTRAITEE_PAR_SITE . "', "; // YTRAITEE
                            $value .= "' '"; // YABONNE_YMESSERR_0
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
                }
            }
            // execution requete
            if (!empty($query)) {
                if ($result = $this->adapter->execute($query) && !empty($eList)) {
                    // update cri_etude.transmis_echeance
                    $sql = " UPDATE ". mvc_model('Etude')->table." SET transmis_echeance = 1 WHERE crpcen IN (" . implode(', ', $eList) . ")";
                    $this->wpdb->query($sql);
                } else {
                    // log erreur
                    $error = sprintf(CONST_EXPORT_CRIDONLINE_ERROR, date('d/m/Y à H:i:s'));
                    writeLog($error, 'cridonlineEcheance.log','Cridon - Export Cridonline');

                    // send email
                    reportError(CONST_EXPORT_CRIDONLINE_ERROR, $error);
                }
            }

            // status code
            return CONST_STATUS_CODE_OK;
        } catch (Exception $e) {
            // write into logfile
            writeLog($e, 'cridonlineEcheance.log');
            // send email
            reportError(CONST_EMAIL_ERROR_CATCH_EXCEPTION, $e->getMessage(),'Cridon - Données notaire - Erreur échéance infos abonnement veilles');
        }
    }

    public function notaireCridonlineUpdateAnnually(){
        try {
            // Update offices
            $options= array(
                'conditions' => array(
                    'Etude.end_subscription_date <=' => date('Y-m-d'),
                    'OR'=>array(
                        'Etude.subscription_level >=' => CONST_LOWEST_PAID_LEVEL_CRIDONLINE,
                        'Etude.next_subscription_level >=' => CONST_LOWEST_PAID_LEVEL_CRIDONLINE
                    )
                ),
            'group' => 'Etude.crpcen'
            );
            $etudes   = mvc_model('Etude')->find($options);

            if (count($etudes)>0){
                $updateLevelValues = $updatePriceValues = $updateStartDateValues = $updateEndDateValues = $updateEcheanceDateValues = $updateNextLevelValues = array();
                $updateTransmisEcheanceValues = array();
                $queryStart = " UPDATE `".mvc_model('Etude')->table."` SET ";
                $queryEnd   = ' END ';
                foreach ($etudes as $etude) {

                    $start_subscription_date    = date('Y-m-d', strtotime($etude->end_subscription_date));
                    $end_subscription_date      = date('Y-m-d', strtotime($start_subscription_date . '+' . CONST_CRIDONLINE_SUBSCRIPTION_DURATION_DAYS . 'days'));
                    $echeance_subscription_date = date('Y-m-d', strtotime($end_subscription_date . '-' . CONST_CRIDONLINE_ECHEANCE_MONTH . 'month'));

                    if (!empty($etude->offre_promo)){
                        $updateOffrePromoValues[] = " {$etude->crpcen} THEN NULL ";
                    }

                    if ($etude->offre_promo != CONST_PROMO_PRIVILEGE){
                        $updateStartDateValues[]        = " {$etude->crpcen} THEN '" . $start_subscription_date . "' ";
                    }

                    if (!empty($etude->next_subscription_level)) {
                        $updateLevelValues[] = " {$etude->crpcen} THEN '" . $etude->next_subscription_level . "' ";
                    }
                    $nextSubscriptionPrice          = mvc_model('Etude')->getSubscriptionPrice($etude, true);
                    $updatePriceValues[]            = " {$etude->crpcen} THEN '" . $nextSubscriptionPrice . "' ";
                    $updateEndDateValues[]          = " {$etude->crpcen} THEN '" . $end_subscription_date . "' ";
                    $updateEcheanceDateValues[]     = " {$etude->crpcen} THEN '" . $echeance_subscription_date . "' ";
                    $updateTransmisEcheanceValues[] = " {$etude->crpcen} THEN '0' ";
                }

                $etudeQuery = array();
                if (count($updateLevelValues)>0) {
                    // subscription level
                    $etudeQuery[] = ' `subscription_level` = CASE crpcen WHEN ' . implode(' WHEN ', $updateLevelValues) . ' ELSE `subscription_level` ';

                    // subscription price
                    $etudeQuery[] = ' `subscription_price` = CASE crpcen WHEN ' . implode(' WHEN ', $updatePriceValues) . ' ELSE `subscription_price` ';

                    // subscription start date
                    if (!empty($updateStartDateValues)){
                        $etudeQuery[] = ' `start_subscription_date` = CASE crpcen WHEN ' . implode(' WHEN ', $updateStartDateValues) . ' ELSE `start_subscription_date` ';
                    }

                    // subscription end date
                    $etudeQuery[] = ' `end_subscription_date` = CASE crpcen WHEN ' . implode(' WHEN ', $updateEndDateValues) . ' ELSE `end_subscription_date` ';

                    // subscription echeance date
                    $etudeQuery[] = ' `echeance_subscription_date` = CASE crpcen WHEN ' . implode(' WHEN ', $updateEcheanceDateValues) . ' ELSE `echeance_subscription_date` ';

                    // subscription transmis echeance
                    $etudeQuery[] = ' `transmis_echeance` = CASE crpcen WHEN ' . implode(' WHEN ', $updateTransmisEcheanceValues) . ' ELSE `transmis_echeance` ';

                    // subscription offre promo
                    if (!empty($updateOffrePromoValues)) {
                        $etudeQuery[] = ' `offre_promo` = CASE crpcen WHEN ' . implode(' WHEN ', $updateOffrePromoValues) . ' ELSE `offre_promo` ';
                    }

                    // exec query block
                    $this->wpdb->query($queryStart . implode(' END, ', $etudeQuery) . $queryEnd);
                }
            }
            // status code
            return CONST_STATUS_CODE_OK;
        } catch (Exception $e) {
            // write into logfile
            writeLog($e, 'notaireCridonlineUpdateAnnually.log');
            // send email
            reportError(CONST_EMAIL_ERROR_CATCH_EXCEPTION, $e->getMessage(),'Cridon - Données notaire - Erreur mise à jour annuelle niveau & tarif cridonline');
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
        $query = " SELECT `n`.`id`,`n`.`id_wp_user`, `n`.`code_interlocuteur`, `n`.`crpcen`, `n`.`client_number`, `n`.`web_password`, `n`.`first_name` AS prenom,
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
            $queryStart = " UPDATE `{$this->wpdb->prefix}solde` SET ";
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
                            $dateTime  = date_create_from_format('d/m/Y', $dateArret);
                            $dateArret = $dateTime->format('Y-m-d');
                        }
                    }
                    $newDate = new DateTime($dateArret);
                    $newDate = $newDate->format('Ymd');
                    $oldDate = new DateTime($currentData->date_arret);
                    $oldDate = $oldDate->format('Ymd');
                    if ($newDate > $oldDate) {
                        // prepare all update   query
                        if (isset($newData[$parser::SOLDE_QUOTA])) {
                            $updateQuotaValues[] = " {$currentData->id} THEN '" . esc_sql($newData[$parser::SOLDE_QUOTA]) . "' ";
                        }

                        if (isset($newData[$parser::SOLDE_NOMBRE])) {
                            $updateNombreValues[] = " {$currentData->id} THEN '" . esc_sql($newData[$parser::SOLDE_NOMBRE]) . "' ";
                        }

                        if (isset($newData[$parser::SOLDE_POINTS])) {
                            $updatePointsValues[] = " {$currentData->id} THEN '" . esc_sql($newData[$parser::SOLDE_POINTS]) . "' ";
                        }

                        $updateDateArret[] = " {$currentData->id} THEN '" . $dateArret . "' ";
                    }
                }

                $this->importSuccess = true;
            }

            // execute update query
            $soldeQuery = array();
            if (count($updateQuotaValues) > 0) {
                // quota
                $soldeQuery[] = ' `quota` = CASE id WHEN ' . implode(' WHEN ', $updateQuotaValues) . ' ELSE `quota` ';
                // nombre
                $soldeQuery[] = ' `nombre` = CASE id WHEN ' . implode(' WHEN ', $updateNombreValues) . ' ELSE `nombre` ';
                // points
                $soldeQuery[] = ' `points` = CASE id WHEN ' . implode(' WHEN ', $updatePointsValues) . ' ELSE `points` ';
                // date_arret
                $soldeQuery[] = ' `date_arret` = CASE id WHEN ' . implode(' WHEN ', $updateDateArret) . ' ELSE `date_arret` ';

                // exec query block
                $this->wpdb->query($queryStart . implode(' END, ', $soldeQuery) . $queryEnd);

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
     * @param string $role
     *
     * @return bool
     */
    public function userCanAccessSensitiveInfo($role)
    {
        global $current_user;
        return $this->userHasRole($current_user,$role);
    }

    /**
     * Return whether a user has a role or not
     *
     * @param object $user
     * @param string $role
     *
     * @return bool
     */
    public function userHasRole($user,$role){
        return in_array($role, (array) $user->roles);
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

    /**
     * Query used in front in order to preprare pagination for questions list
     *
     * @param object $notary
     * @param array $data
     * @param bool $roles
     *
     * @return string
     */
    public function manageCollaborator($notary, $data, $roles = false)
    {
        // check id collaborator
        if (isset($data['collaborator_id']) && intval($data['collaborator_id']) > 0) { // update
            if($this->updateCollaborator($data,$roles)){
                return true;
            };
        } else { // create
            if($this->addCollaborator($notary, $data)){
                return true;
            };
        }
        return false;
    }

    /**
     * Add new collaborator
     *
     * @param mixed $notary
     * @param array $data
     * @return bool
     * @throws Exception
     */
    public function addCollaborator($notary, $data)
    {
        global $cri_container;

        $collaborator = $this->fillCollaborator($data);
        $collaborator['client_number'] = $notary->client_number;
        $collaborator['crpcen']        = $notary->crpcen;

        // insert into cri_notaire
        $collaboratorId = $this->create($collaborator);
        $this->updateFlagERP($collaboratorId);
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
            return true;
        }
    }

    /**
     * Update collaborator data
     *
     * @param array $data
     * @param bool $roles
     *
     * @return bool
     */
    public function updateCollaborator($data, $roles)
    {
        $collaborator = $this->fillCollaborator($data);

        // update cri_notaire data
        $collaborator['id'] = isset($data['collaborator_id']) ? esc_sql($data['collaborator_id']) : '';
        if (!empty($collaborator['id']) && $this->save($collaborator)) { // successful update
            $this->updateFlagERP($collaborator['id']);
            if (!empty($roles) && $roles) {
                // manage roles
                $user = $this->getAssociatedUserByNotaryId($collaborator['id']);
                // reset all roles
                $this->resetUserRoles($user);

                // add new posted roles in data
                foreach (Config::$notaryRoles as $role => $label) {
                    if (isset($data[$role]) && $data[$role]) {
                        $user->add_role($role);
                    }
                }
            }
            return true;
        }
        return false;
    }

    public function fillCollaborator($data){
        // préparation des champs pour insertion dans la cri_notaire
        $collaborator                              = array();
        $collaborator['first_name']                = isset($data['collaborator_first_name']) ? $data['collaborator_first_name'] : '';
        $collaborator['last_name']                 = isset($data['collaborator_last_name']) ? $data['collaborator_last_name'] : '';
        $collaborator['email_adress']              = isset($data['collaborator_email']) ? $data['collaborator_email'] : '';
        $collaborator['tel']                       = isset($data['collaborator_tel']) ? $data['collaborator_tel'] : '';
        $collaborator['tel_portable']              = isset($data['collaborator_tel_portable']) ? $data['collaborator_tel_portable'] : '';
        $collaborator['fax']                       = isset($data['collaborator_fax']) ? $data['collaborator_fax'] : '';
        if (!empty($data['collaborator_id_function_collaborator'])) {
            $collaborator['id_fonction_collaborateur'] = $data['collaborator_id_function_collaborator'];
        }
        if (!empty($data['collaborator_id_function_notaire'])){
            $collaborator['id_fonction'] = $data['collaborator_id_function_notaire'];
            // set the id_fonction_collaborateur to 0 if the id_fonction is not the collaborateur id.
            if ($data['collaborator_id_function_notaire'] != CONST_NOTAIRE_COLLABORATEUR){
                $collaborator['id_fonction_collaborateur'] = 0;
            }
        }
        return $collaborator;
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
     * @param array $data
     * @return bool
     */
    public function updateOffice($data)
    {
        if (!empty($data['office_crpcen'])) {
            // init  office data
            $office = array();
            $office['crpcen'] = isset($data['office_crpcen']) ? $data['office_crpcen'] : '';
            $office['office_name'] = isset($data['office_name']) ? $data['office_name'] : '';
            $office['adress_1'] = isset($data['office_address_1']) ? $data['office_address_1'] : '';
            $office['adress_2'] = isset($data['office_address_2']) ? $data['office_address_2'] : '';
            $office['adress_3'] = isset($data['office_address_3']) ? $data['office_address_3'] : '';
            $office['cp'] = isset($data['office_postalcode']) ? $data['office_postalcode'] : '';
            $office['city'] = isset($data['office_city']) ? $data['office_city'] : '';
            $office['office_email_adress_1'] = isset($data['office_email']) ? $data['office_email'] : '';
            $office['tel'] = isset($data['office_phone']) ? $data['office_phone'] : '';
            $office['fax'] = isset($data['office_fax']) ? $data['office_fax'] : '';

            $etude = array('Etude' => $office);
            if (mvc_model('Etude')->save($etude)){
                $notary = $this->getUserConnectedData();
                $this->updateFlagERP($notary->id);
                return true;
            }
        }
        return false;
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
     * Delete collaborator
     *
     * @param int $id
     * @return boolean
     */
    public function deleteCollaborator($id)
    {
        // get associated WP_User
        $associated_wp_user = $this->getAssociatedUserByNotaryId($id);

        // check user data
        if ($associated_wp_user && $associated_wp_user->ID) {
            // set 'cron_delete' flag to on
            $this->manageCronFlag($id);
            $this->resetPwd($id,'off');
            $this->updateFlagERP($id,'off');
            // change user status to disabled
            $this->wpdb->update($this->wpdb->users,
                                array('user_status' => CONST_STATUS_DISABLED),
                                array(
                                    'ID' => $associated_wp_user->ID
                                )
            );

            return true;
        }

        return false;
    }

    /**
     * Manage cron_delete flag
     *
     * @param int $notaryId
     * @param string $action
     *
     * @return void
     */
    public function manageCronFlag($notaryId, $action = 'on')
    {
        $notary                           = array();
        $notary['Notaire']['id']          = $notaryId;
        $notary['Notaire']['cron_delete'] = ($action == 'on') ? 1 : 0; // flag pour notifier ERP ( immediatement RAZ par cron "cronDeleteCollaborator" )
        $this->save($notary);
    }

    /**
     * @return array|null|object
     */
    public function cronDeleteCollaborator()
    {
        try {
            $notaries = $this->wpdb->get_results("
                SELECT
                    `cn`.*,
                    `ce`.`adress_1`,
                    `ce`.`adress_2`,
                    `ce`.`adress_3`,
                    `ce`.`city`,
                    `ce`.`cp`,
                    `ce`.`tel` tel_office,
                    `ce`.`fax` fax_office,
                    `ce`.`office_email_adress_1`,
                    `cf`.`label` fonction,
                    `cfc`.`label` fonction_collaborateur
                FROM
                    `{$this->table}` cn
                LEFT JOIN
                    `{$this->wpdb->prefix}etude` ce
                ON
                    `ce`.`crpcen` = `cn`.`crpcen`
                LEFT JOIN
                    `{$this->wpdb->prefix}fonction` cf
                ON
                    `cf`.`id` = `cn`.`id_fonction`
                LEFT JOIN
                    `{$this->wpdb->prefix}fonction_collaborateur` cfc
                ON
                    `cfc`.`id` = `cn`.`id_fonction_collaborateur`
                WHERE
                    `cn`.`cron_delete` = 1
            ");

            // verification nb notaires à traiter
            if (is_array($notaries) && count($notaries) > 0) {
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
                // list des id notaires pour maj cri_notaire.cron_delete apres transfert
                $qList = array();

                // requette commune
                /**
                 * Tous les champs de YNOTAIRE sont NOT NULL ( YNOTAIRE.sql )
                 */
                $query  = " INTO " . CONST_DB_TABLE_YNOTAIRE;
                $query .= " (";
                $query .= $adapter::YIDCOLLAB . ", "; // YIDCOLLAB
                $query .= $adapter::YCRPCEN . ", "; // YCRPCEN
                $query .= $adapter::CNTLNA . ", "; // CNTLNA
                $query .= $adapter::CCNCRM . ", ";   // CCNCRM
                $query .= $adapter::YIDNOT . ", "; // YIDNOT
                $query .= $adapter::CNTFNA . ", ";   // CNTFNA
                $query .= $adapter::CNTFNC . ", ";   // CNTFNC
                $query .= $adapter::YTXTFNC . ", ";   // YTXTFNC
                $query .= $adapter::WEB . ", ";   // WEB
                $query .= $adapter::TEL . ", ";   // TEL
                $query .= $adapter::CNTMOB . ", ";   // CNTMOB
                $query .= $adapter::FAX . ", ";   // FAX
                $query .= $adapter::YFINPRE . ", ";   // YFINPRE
                $query .= $adapter::YMDPWEB . ", ";   // YMDPWEB
                $query .= $adapter::ZMDPTEL . ", ";   // ZMDPTEL
                $query .= $adapter::ADDLIG1 . ", ";   // ADDLIG1
                $query .= $adapter::ADDLIG2 . ", ";   // ADDLIG2
                $query .= $adapter::ADDLIG3 . ", ";   // ADDLIG3
                $query .= $adapter::POSCOD . ", ";   // POSCOD
                $query .= $adapter::CTY . ", ";   // CTY
                $query .= $adapter::TELOFF . ", ";   // TELOFF
                $query .= $adapter::FAXOFF . ", ";   // FAXOFF
                $query .= $adapter::WEBOFF . ", ";   // WEBOFF
                $query .= $adapter::YSREECR . ", ";   // YSREECR
                $query .= $adapter::YSRETEL . ", ";   // YSRETEL
                $query .= $adapter::YTRAITEE . ", ";   // YTRAITEE
                $query .= $adapter::YDDEMDPTEL . ", ";   // YDDEMDPTEL
                $query .= $adapter::YDDEMDPWEB . ", ";   // YDDEMDPWEB
                $query .= $adapter::YERR . ", ";   // YERR
                $query .= $adapter::YMESSERR . " ";   // YMESSERR
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
                        foreach ($notaries as $notary) {
                            // remplit la liste des notaires
                            $qList[] = $notary->id;

                            // value block
                            $value  = $query;
                            $value .= "(";

                            $value .= "'" . $notary->id . time() . "', "; // YIDCOLLAB
                            $value .= "'" . $notary->crpcen . "', "; // YCRPCEN
                            $value .= "'" . (empty($notary->last_name) ? ' ' : $this->replaceQuote($notary->last_name)) . "', "; // CNTLNA
                            $value .= "'" . (empty($notary->code_interlocuteur) ? ' ' : $notary->code_interlocuteur) . "', "; // CCNCRM
                            $value .= "'" . $notary->id . "', "; // YIDNOT
                            $value .= "'" . (empty($notary->first_name) ? ' ' : $this->replaceQuote($notary->first_name)) . "', "; // CNTFNA
                            $value .= "'" . $notary->id_fonction . "', "; // CNTFNC
                            if (!empty($notary->fonction_collaborateur)){
                                $value .= "'" . $this->replaceQuote($notary->fonction_collaborateur) . "', "; // CNTFNC
                            } else {
                                $value .= "'" . $this->replaceQuote($notary->fonction) . "', "; // CNTFNC
                            }
                            $value .= "'" . (empty($notary->email_adress) ? ' ' : $this->replaceQuote($notary->email_adress)) . "', "; // WEB
                            $value .= "'" . (empty($notary->tel) ? ' ' : $this->replaceQuote($notary->tel)) . "', "; // TEL
                            $value .= "'" . (empty($notary->tel_portable) ? ' ' : $this->replaceQuote($notary->tel_portable)) . "', "; // CNTMOB
                            $value .= "'" . (empty($notary->fax) ? ' ' : $this->replaceQuote($notary->fax)) . "', "; // FAX
                            $value .= "TO_DATE('" . date('d/m/Y') . "', 'dd/mm/yyyy'), "; // YFINPRE
                            $value .= "'" . (empty($notary->web_password) ? ' ' : $notary->web_password) . "', "; // YMDPWEB
                            $value .= "'" . (empty($notary->tel_password) ? ' ' : $notary->tel_password) . "', "; // ZMDPTEL
                            $value .= "'" . (empty($notary->adress_1) ? ' ' : $this->replaceQuote($notary->adress_1)) . "', "; // ADDLIG1
                            $value .= "'" . (empty($notary->adress_2) ? ' ' : $this->replaceQuote($notary->adress_2)) . "', "; // ADDLIG2
                            $value .= "'" . (empty($notary->adress_3) ? ' ' : $this->replaceQuote($notary->adress_3)) . "', "; // ADDLIG3
                            $value .= "'" . (empty($notary->cp) ? ' ' : $this->replaceQuote($notary->cp)) . "', "; // POSCOD
                            $value .= "'" . (empty($notary->city) ? ' ' : $this->replaceQuote($notary->city)) . "', "; // CTY
                            $value .= "'" . (empty($notary->tel_office) ? ' ' : $this->replaceQuote($notary->tel_office)) . "', "; // TELOFF
                            $value .= "'" . (empty($notary->fax_office) ? ' ' : $this->replaceQuote($notary->fax_office)) . "', "; // FAXOFF
                            $value .= "'" . (empty($notary->office_email_adress_1) ? ' ' : $this->replaceQuote($notary->office_email_adress_1)) . "', "; // WEBOFF
                            $value .= "'0', "; // YSREECR
                            $value .= "'0', "; // YSRETEL
                            $value .= "'0', "; // YTRAITEE
                            $value .= "'0', "; // YDDEMDPTEL
                            $value .= "'0', "; // YDDEMDPWEB
                            $value .= "'0', "; // YERR
                            $value .= "' '"; // YMESSERR

                            $value .= ")";

                            $queryBloc[] = $value;
                        }
                        // preparation requete en masse
                        if (count($queryBloc) > 0) {
                            $query = 'INSERT ALL ';
                            $query .= implode(' ', $queryBloc);
                            $query .= ' SELECT * FROM dual';
                            writeLog($query, 'query_deletecollaborator.log');
                        }
                        break;
                    case CONST_DB_DEFAULT:
                    default:
                        foreach ($notaries as $notary) {
                            // remplit la liste des notaires
                            $qList[] = $notary->id;

                            $value = "(";

                            $value .= "'" . $notary->id . time() . "', "; // YIDCOLLAB
                            $value .= "'" . $notary->crpcen . "', "; // YCRPCEN
                            $value .= "'" . $notary->first_name . "', "; // CNTLNA
                            $value .= "'" . $notary->code_interlocuteur . "', "; // CCNCRM
                            $value .= "'" . $notary->id . "', "; // YIDNOT
                            $value .= "'" . $notary->first_name . "', "; // CNTFNA
                            $value .= "'" . $notary->id_fonction . "', "; // CNTFNC
                            $value .= "' ', "; // YTXTFNC
                            $value .= "'" . $notary->email_adress . "', "; // WEB
                            $value .= "'" . $notary->tel . "', "; // TEL
                            $value .= "'" . $notary->tel_portable . "', "; // CNTMOB
                            $value .= "'" . $notary->fax . "', "; // FAX
                            $value .= "'" . date('Y-m-d') . "', "; // YFINPRE
                            $value .= "'" . $notary->web_password . "', "; // YMDPWEB
                            $value .= "'" . $notary->tel_password . "', "; // ZMDPTEL
                            $value .= "'" . $notary->adress_1 . "', "; // ADDLIG1
                            $value .= "'" . $notary->adress_2 . "', "; // ADDLIG2
                            $value .= "'" . $notary->adress_3 . "', "; // ADDLIG3
                            $value .= "'" . $notary->cp . "', "; // POSCOD
                            $value .= "'" . $notary->city . "', "; // CTY
                            $value .= "'" . $notary->tel_office . "', "; // TELOFF
                            $value .= "'" . $notary->fax_office . "', "; // FAXOFF
                            $value .= "'" . $notary->office_email_adress_1 . "', "; // WEBOFF
                            $value .= "'0', "; // YSREECR
                            $value .= "'0', "; // YSRETEL
                            $value .= "'0', "; // YTRAITEE
                            $value .= "'0', "; // YDDEMDPTEL
                            $value .= "'0', "; // YDDEMDPWEB
                            $value .= "'0', "; // YERR
                            $value .= "' '"; // YMESSERR

                            $value .= ")";

                            $queryBloc[] = $value;
                        }
                        // preparation requete en masse
                        if (count($queryBloc) > 0) {
                            $query = 'INSERT' . $query . implode(', ', $queryBloc);
                            writeLog($query, 'query_deletecollaborator.log');
                        }
                        break;
                }
            }

            // execution requete
            if (!empty($query)) {
                if ($result = $this->adapter->execute($query) && !empty($qList)) {
                    // update cri_notaire.cron_delete
                    $sql = " UPDATE {$this->table} SET cron_delete = 0 WHERE id IN (" . implode(', ', $qList) . ")";
                    $this->wpdb->query($sql);
                } else {
                    // log erreur
                    $error = sprintf(CONST_DELCOLLAB_ERROR, date('d/m/Y à H:i:s'));
                    writeLog($error, 'deletecollaborator.log', 'Cridon - Cron de suppression collaborateur');
                }
            }

            // status code
            return CONST_STATUS_CODE_OK;
        } catch(\Exception $e) {
            // write into logfile
            writeLog($e, 'deletecollaborator.log');

            // status code
            return CONST_STATUS_CODE_GONE;
        }
    }

    /**
     * Get list of office members
     *
     * @param mixed $notary
     * @param array $params
     * @return mixed
     * @throws Exception
     */
    public function listOfficeMembers($notary, $params)
    {
        $query_options = array(
            'fields'     => array('cn.*','cu.*','cf.label as notaire_fonction_label','cfc.label as collaborator_fonction_label'),
            'conditions' => array(
                'cn.crpcen'      => $notary->crpcen,
                'cu.user_status' => CONST_STATUS_ENABLED,
            ),
            'in' => array(
                'cn.id_fonction' => Config::$addableFunctions
            ),
            'not' => array(
                'cn.id'          => $notary->id,
            ),
            'synonym'    => 'cn',
            'join'       => array(
                array(
                    'table'  => 'users cu',
                    'column' => ' cn.id_wp_user = cu.ID'
                ),
                array(
                    'table'  => 'fonction cf',
                    'column' => ' cn.id_fonction = cf.id'
                ),
                array(
                    'type'   => 'left',
                    'table'  => 'fonction_collaborateur cfc',
                    'column' => ' cn.id_fonction_collaborateur = cfc.id'
                )
            ),
            'order'    => 'cn.last_name'
        );
        // page options
        $options['page']     = empty($params['page']) ? 1 : intval($params['page']);
        $options['per_page'] = !empty($params['per_page']) ? $params['per_page'] : DEFAULT_POST_PER_PAGE;

        $totalObjects = mvc_model('QueryBuilder')->findAll('notaire', $query_options, 'cn.id');
        $total_count = count($totalObjects);

        // formation bloc limit
        $limit = $this->db_adapter->get_limit_sql($options);
        $query_options['limit'] = $limit;

        $objects = mvc_model('QueryBuilder')->findAll('notaire', $query_options, 'cn.id');

        return array(
            'objects'       => $objects,
            'total_objects' => $total_count,
            'total_pages'   => ceil($total_count / $options['per_page']),
            'page'          => $options['page']
        );
    }


    /**
     * Set notaire role
     *
     * @param mixed $notaries
     * @return void
     */
    public function setNewNotaireRole($notaries)
    {
        foreach ($notaries as $notary) {
            if (!$notary->id_wp_user) {
                // get user by notary_id
                $user = $this->getAssociatedUserByNotaryId($notary->id);
            } else {
                // get user by id
                $user = new WP_User($notary->id_wp_user);
            }

            // user must be an instance of WP_User vs WP_Error
            if ($user instanceof WP_User) {
                // default role
                if (!in_array($notary->category, Config::$notaryNoDefaultOffice)) { // Categ OFF
                    $user->add_role(CONST_NOTAIRE_ROLE);
                } else {
                    if ($notary->category == CONST_CLIENTDIVERS_CATEG) { // Categ DIV
                        $user->add_role(CONST_NOTAIRE_DIV_ROLE);
                    } elseif ($notary->category == CONST_ORGANISMES_CATEG) { // Categ ORG
                        $user->add_role(CONST_NOTAIRE_ORG_ROLE);
                    }
                }
                $rolesNotaire = Config::$notaryRolesByFunction['notaries'];
                if (!empty($rolesNotaire[$notary->id_fonction])){
                    foreach ($rolesNotaire[$notary->id_fonction] as $role){
                        $user->add_role($role);
                    }
                }

                // disable admin bar
                $this->disableAdminBar($notary);
            }
        }
    }

    /**
     * Maj notaire role
     *
     * @param mixed $notary
     * @return void
     */
    public function majNotaireRole($notary)
    {
        if (!$notary->id_wp_user) {
            // get user by notary_id
            $user = $this->getAssociatedUserByNotaryId($notary->id);
        } else {
            // get user by id
            $user = new WP_User($notary->id_wp_user);
        }

        // user must be an instance of WP_User vs WP_Error
        if ($user instanceof WP_User
            && property_exists($notary, 'level')
            && intval($notary->level) > 0
        ) {
            /**
             * finance role
             * to be matched in list of authorized user by function
             *
             * @see \Config::$canAccessFinance
             */
            if (!in_array($notary->id_fonction, Config::$canAccessFinance)) {
                $user->remove_role(CONST_FINANCE_ROLE);
            }
        }
    }

    /**
     * Disable admin bar for notaries
     *
     * @param mixed $notaries
     * @return void
     */
    public function disableNotariesAdminBar($notaries)
    {
        if (is_array($notaries) && count($notaries) > 0) {
            foreach ($notaries as $notary) {
                $this->disableAdminBar($notary);
            }
        } elseif(is_object($notaries)) {
            $this->disableAdminBar($notaries);
        }
    }

    /**
     * @param object $notary
     * @throws Exception
     * @return void
     */
    protected function disableAdminBar($notary)
    {
        // peut être que $notary->id_wp_user est encore null (cas de nouvelle insertion via bulk insert)
        // cette valeur sera mise à jour après execution bulk update via updateCriNotaireWpUserId
        if (!$notary->id_wp_user) {
            $notary = mvc_model('QueryBuilder')->findOne('notaire',
                array(
                    'fields'     => 'id_wp_user',
                    'conditions' => 'id = ' . $notary->id,
                )
            );
        }
        // insert or update user_meta
        update_user_meta($notary->id_wp_user, 'show_admin_bar_front', 'false');
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

    /**
     * Check if users can reset password
     *
     * @return bool
     */
    public function userCanResetPwd()
    {
        $object = $this->getUserConnectedData();

        /**
         * ACs :
         * - notaire connecté dispose d'une adresse email perso
         * - Seuls les notaires de fonctions 1, 2, 3, 6, 7, 8, 9, 10 peuvent accéder à la fonction
         */
        return (is_object($object)
                && property_exists($object, 'email_adress')
                && !empty($object->email_adress) && filter_var($object->email_adress, FILTER_VALIDATE_EMAIL)
                && property_exists($object, 'id_fonction')
                && in_array($object->id_fonction, Config::$allowedNotaryFunction)
        ) ? true : false;
    }

    /**
     * Set reset pwd flag
     *
     * @param int $notaryId
     * @param string $action
     *
     * @return void
     */
    public function resetPwd($notaryId, $action = 'on')
    {
        $notary                            = array();
        $notary['Notaire']['id']           = $notaryId;
        $notary['Notaire']['renew_pwd']    = ($action == 'on') ? 1 : 0; // flag pour notifier ERP ( immediatement RAZ par cron "cronExportNotary" )
        $this->save($notary);
    }

    /**
     * @return array|null|object
     */
    public function cronExportNotary()
    {
        try {
            $notaries = $this->wpdb->get_results("
                SELECT
                    `cn`.*,
                    `ce`.`adress_1`,
                    `ce`.`adress_2`,
                    `ce`.`adress_3`,
                    `ce`.`city`,
                    `ce`.`cp`,
                    `ce`.`tel` tel_office,
                    `ce`.`fax` fax_office,
                    `ce`.`office_email_adress_1`,
                    `cf`.`label` fonction,
                    `cfc`.`label` fonction_collaborateur
                FROM
                    `{$this->table}` cn
                LEFT JOIN
                    `{$this->wpdb->prefix}etude` ce
                ON
                    `ce`.`crpcen` = `cn`.`crpcen`
                LEFT JOIN
                    `{$this->wpdb->prefix}fonction` cf
                ON
                    `cf`.`id` = `cn`.`id_fonction`
                LEFT JOIN
                    `{$this->wpdb->prefix}fonction_collaborateur` cfc
                ON
                    `cfc`.`id` = `cn`.`id_fonction_collaborateur`
                WHERE
                    `cn`.`renew_pwd` = 1 OR `cn`.`cron_update_erp` = 1

            ");

            // verification nb notaires à traiter
            if (is_array($notaries) && count($notaries) > 0) {
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
                // list des id notaires pour maj cri_notaire apres export
                $qListResetPWD = $qListCRUD = array();

                // requette commune
                $query  = " INTO " . CONST_DB_TABLE_YNOTAIRE;
                $query .= " (";
                $query .= $adapter::YIDCOLLAB . ", "; // YIDCOLLAB
                $query .= $adapter::YCRPCEN . ", "; // YCRPCEN
                $query .= $adapter::CNTLNA . ", "; // CNTLNA
                $query .= $adapter::CCNCRM . ", ";   // CCNCRM
                $query .= $adapter::YIDNOT . ", "; // YIDNOT
                $query .= $adapter::CNTFNA . ", ";   // CNTFNA
                $query .= $adapter::CNTFNC . ", ";   // CNTFNC
                $query .= $adapter::YTXTFNC . ", ";   // YTXTFNC
                $query .= $adapter::WEB . ", ";   // WEB
                $query .= $adapter::TEL . ", ";   // TEL
                $query .= $adapter::CNTMOB . ", ";   // CNTMOB
                $query .= $adapter::FAX . ", ";   // FAX
                $query .= $adapter::YFINPRE . ", ";   // YFINPRE
                $query .= $adapter::YMDPWEB . ", ";   // YMDPWEB
                $query .= $adapter::ZMDPTEL . ", ";   // ZMDPTEL
                $query .= $adapter::ADDLIG1 . ", ";   // ADDLIG1
                $query .= $adapter::ADDLIG2 . ", ";   // ADDLIG2
                $query .= $adapter::ADDLIG3 . ", ";   // ADDLIG3
                $query .= $adapter::POSCOD . ", ";   // POSCOD
                $query .= $adapter::CTY . ", ";   // CTY
                $query .= $adapter::TELOFF . ", ";   // TELOFF
                $query .= $adapter::FAXOFF . ", ";   // FAXOFF
                $query .= $adapter::WEBOFF . ", ";   // WEBOFF
                $query .= $adapter::YSREECR . ", ";   // YSREECR
                $query .= $adapter::YSRETEL . ", ";   // YSRETEL
                $query .= $adapter::YTRAITEE . ", ";   // YTRAITEE
                $query .= $adapter::YDDEMDPTEL . ", ";   // YDDEMDPTEL
                $query .= $adapter::YDDEMDPWEB . ", ";   // YDDEMDPWEB
                $query .= $adapter::YERR . ", ";   // YERR
                $query .= $adapter::YMESSERR . " ";   // YMESSERR
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
                        foreach ($notaries as $notary) {
                            $user = new WP_User($notary->id_wp_user);
                            // recuperation droit question ecrite et telephonique
                            $droitQuestEcrite = $this->userHasRole($user,CONST_QUESTIONECRITES_ROLE) ? CONST_YSREECR_ON : CONST_YSREECR_OFF;
                            $droitQuestTel    = $this->userHasRole($user,CONST_QUESTIONTELEPHONIQUES_ROLE) ? CONST_YSRETEL_ON : CONST_YSRETEL_OFF;

                            // value block
                            $value  = $query;
                            $value .= "(";

                            $value .= "'" . $notary->id . time() . "', "; // YIDCOLLAB
                            $value .= "'" . $notary->crpcen . "', "; // YCRPCEN
                            $value .= "'" . (empty($notary->last_name) ? ' ' : $this->replaceQuote($notary->last_name)) . "', "; // CNTLNA
                            $value .= "'" . (empty($notary->code_interlocuteur) ? ' ' : $notary->code_interlocuteur) . "', "; // CCNCRM
                            $value .= "'" . $notary->id . "', "; // YIDNOT
                            $value .= "'" . (empty($notary->first_name) ? ' ' : $this->replaceQuote($notary->first_name)) . "', "; // CNTFNA
                            $value .= "'" . $notary->id_fonction . "', "; // CNTFNC
                            if (!empty($notary->fonction_collaborateur)){
                                $value .= "'" . $this->replaceQuote($notary->fonction_collaborateur) . "', "; // CNTFNC
                            } else {
                                $value .= "'" . $this->replaceQuote($notary->fonction) . "', "; // CNTFNC
                            }
                            $value .= "'" . (empty($notary->email_adress) ? ' ' : $this->replaceQuote($notary->email_adress)) . "', "; // WEB
                            $value .= "'" . (empty($notary->tel) ? ' ' : $this->replaceQuote($notary->tel)) . "', "; // TEL
                            $value .= "'" . (empty($notary->tel_portable) ? ' ' : $this->replaceQuote($notary->tel_portable)) . "', "; // CNTMOB
                            $value .= "'" . (empty($notary->fax) ? ' ' : $this->replaceQuote($notary->fax)) . "', "; // FAX
                            $value .= "TO_DATE('" . CONST_DATE_NULL_ORACLE . "', 'dd/mm/yyyy'), "; // YFINPRE
                            $value .= "'" . (empty($notary->web_password) ? ' ' : $notary->web_password) . "', "; // YMDPWEB
                            $value .= "'" . (empty($notary->tel_password) ? ' ' : $notary->tel_password) . "', "; // ZMDPTEL
                            $value .= "'" . (empty($notary->adress_1) ? ' ' : $this->replaceQuote($notary->adress_1)) . "', "; // ADDLIG1
                            $value .= "'" . (empty($notary->adress_2) ? ' ' : $this->replaceQuote($notary->adress_2)) . "', "; // ADDLIG2
                            $value .= "'" . (empty($notary->adress_3) ? ' ' : $this->replaceQuote($notary->adress_3)) . "', "; // ADDLIG3
                            $value .= "'" . (empty($notary->cp) ? ' ' : $this->replaceQuote($notary->cp)) . "', "; // POSCOD
                            $value .= "'" . (empty($notary->city) ? ' ' : $this->replaceQuote($notary->city)) . "', "; // CTY
                            $value .= "'" . (empty($notary->tel_office) ? ' ' : $this->replaceQuote($notary->tel_office)) . "', "; // TELOFF
                            $value .= "'" . (empty($notary->fax_office) ? ' ' : $this->replaceQuote($notary->fax_office)) . "', "; // FAXOFF
                            $value .= "'" . (empty($notary->office_email_adress_1) ? ' ' : $this->replaceQuote($notary->office_email_adress_1)) . "', "; // WEBOFF
                            $value .= "'" . $droitQuestEcrite . "', "; // YSREECR
                            $value .= "'" . $droitQuestTel . "', "; // YSRETEL
                            $value .= "'" . CONST_YTRAITEE_PAR_SITE . "', "; // YTRAITEE

                            if ($notary->renew_pwd == 1) { // demande de renouvellement de MDP
                                // liste resetPwd
                                $qListResetPWD[] = $notary->id;
                                $value .= "'" . CONST_YDDEMDPTEL_RESETPWD_ON . "', "; // YDDEMDPTEL
                                $value .= "'" . CONST_YDDEMDPWEB_RESETPWD_ON . "', "; // YDDEMDPWEB
                            } else { // sans demande  de renouvellement MDP
                                // list CRUD user
                                $qListCRUD[] = $notary->id;
                                // on demande uniquement un nouveau mdp si champs vide (nouveau user)
                                $value .= "'" . (empty($notary->tel_password) ? CONST_YDDEMDPTEL_RESETPWD_ON : CONST_YDDEMDPTEL_RESETPWD_OFF) . "', "; // YDDEMDPTEL
                                $value .= "'" . (empty($notary->web_password) ? CONST_YDDEMDPWEB_RESETPWD_ON : CONST_YDDEMDPWEB_RESETPWD_OFF) . "', "; // YDDEMDPWEB
                            }

                            $value .= "'0', "; // YERR
                            $value .= "' '"; // YMESSERR

                            $value .= ")";

                            $queryBloc[] = $value;
                        }
                        // preparation requete en masse
                        if (count($queryBloc) > 0) {
                            $query = 'INSERT ALL ';
                            $query .= implode(' ', $queryBloc);
                            $query .= ' SELECT * FROM dual';
                        }
                        break;
                    case CONST_DB_DEFAULT:
                    default:
                        foreach ($notaries as $notary) {
                            $user = new WP_User($notary->id_wp_user);
                            // recuperation droit question ecrite et telephonique
                            $droitQuestEcrite = $this->userHasRole($user,CONST_QUESTIONECRITES_ROLE) ? CONST_YSREECR_ON : CONST_YSREECR_OFF;
                            $droitQuestTel    = $this->userHasRole($user,CONST_QUESTIONTELEPHONIQUES_ROLE) ? CONST_YSRETEL_ON : CONST_YSRETEL_OFF;

                            $value = "(";

                            $value .= "'" . $notary->id . time() . "', "; // YIDCOLLAB
                            $value .= "'" . $notary->crpcen . "', "; // YCRPCEN
                            $value .= "'" . $notary->last_name . "', "; // CNTLNA
                            $value .= "'" . $notary->code_interlocuteur . "', "; // CCNCRM
                            $value .= "'" . $notary->id . "', "; // YIDNOT
                            $value .= "'" . $notary->first_name . "', "; // CNTFNA
                            $value .= "'" . $notary->id_fonction . "', "; // CNTFNC
                            $value .= "'" . $notary->fonction . "', "; // CNTFNC
                            $value .= "'" . $notary->email_adress . "', "; // WEB
                            $value .= "'" . $notary->tel . "', "; // TEL
                            $value .= "'" . $notary->tel_portable . "', "; // CNTMOB
                            $value .= "'" . $notary->fax . "', "; // FAX
                            $value .= "' ', "; // YFINPRE
                            $value .= "'" . $notary->web_password . "', "; // YMDPWEB
                            $value .= "'" . $notary->tel_password . "', "; // ZMDPTEL
                            $value .= "'" . $notary->adress_1 . "', "; // ADDLIG1
                            $value .= "'" . $notary->adress_2 . "', "; // ADDLIG2
                            $value .= "'" . $notary->adress_3 . "', "; // ADDLIG3
                            $value .= "'" . $notary->cp . "', "; // POSCOD
                            $value .= "'" . $notary->city . "', "; // CTY
                            $value .= "'" . $notary->tel_office . "', "; // TELOFF
                            $value .= "'" . $notary->fax_office . "', "; // FAXOFF
                            $value .= "'" . $notary->office_email_adress_1 . "', "; // WEBOFF
                            $value .= "'" . $droitQuestEcrite . "', "; // YSREECR
                            $value .= "'" . $droitQuestTel . "', "; // YSRETEL
                            $value .= "'" . CONST_YTRAITEE_PAR_SITE . "', "; // YTRAITEE

                            if ($notary->renew_pwd > 0) { // demande de renouvellement de MDP
                                // liste resetPwd
                                $qListResetPWD[] = $notary->id;
                                $value .= "'" . CONST_YDDEMDPTEL_RESETPWD_ON . "', "; // YDDEMDPTEL
                                $value .= "'" . CONST_YDDEMDPWEB_RESETPWD_ON . "', "; // YDDEMDPWEB
                            } else {
                                // list CRUD user
                                $qListCRUD[] = $notary->id;
                                $value .= "'" . CONST_YDDEMDPTEL_RESETPWD_OFF . "', "; // YDDEMDPTEL
                                $value .= "'" . CONST_YDDEMDPWEB_RESETPWD_OFF . "', "; // YDDEMDPWEB
                            }

                            $value .= "'0', "; // YERR
                            $value .= "' '"; // YMESSERR

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
                if ($result = $this->adapter->execute($query)) {
                    // update cri_notaire.renew_pwd
                    if (count($qListResetPWD) > 0) {
                        $sql = " UPDATE {$this->table} SET renew_pwd = 0 WHERE id IN (" . implode(', ', $qListResetPWD) . ")";
                        $this->wpdb->query($sql);
                    }
                    // update cri_notaire.cron_update
                    if (count($qListCRUD) > 0) {
                        $sql = " UPDATE {$this->table} SET cron_update_erp = 0 WHERE id IN (" . implode(', ', $qListCRUD) . ")";
                        $this->wpdb->query($sql);
                    }
                } else {
                    // log erreur
                    $error = sprintf(CONST_UPDATEERP_ERROR, date('d/m/Y à H:i:s'));
                    writeLog($error, 'query_updateerp.log');
                }
            }

            // status code
            return CONST_STATUS_CODE_OK;
        } catch(\Exception $e) {
            // write into logfile
            if (!empty($query)) {
                writeLog($query, 'query.log');
            }
            writeLog($e, 'query_updateerp.log');

            // status code
            return CONST_STATUS_CODE_GONE;
        }
    }

    /**
     * @param $field
     * @return string
     */
    protected function replaceQuote ($field){
        return str_replace('\'', '\'\'', $field);
    }

    /**
     * Update notary PWD
     *
     * @param int $notaryId
     * @param string $newWebPwd
     * @param string $newTelPwd
     */
    public function updatePwd($notaryId, $newWebPwd, $newTelPwd)
    {
        // recuperation wp_user associé
        $user = $this->getAssociatedUserByNotaryId($notaryId);
        if ($user instanceof WP_User) {
            // On récupère le notaire pour les anciens mdp
            $notaire = $this->find_by_id($notaryId);

            // mettre à jour la table notaire
            $query = " UPDATE {$this->table}
                       SET web_password = %s,
                       tel_password = %s
                       WHERE id = %d ";
            $this->wpdb->query($this->wpdb->prepare($query, $newWebPwd, $newTelPwd, $notaryId));

            // mettre à jour cri_users
            wp_set_password($newWebPwd, $user->ID);

            // On n'envoie le nouveau de mdp téléphonique uniquement si le notaire a le droit de poser une question par téléphone et que le mdp est nouveau.
            if (!$this->userHasRole($user,CONST_QUESTIONTELEPHONIQUES_ROLE) || $notaire->tel_password == $newTelPwd){
                $newTelPwd = '';
            }
            // envoie email
            $this->sendEmailForPwdChanged($notaryId, $newWebPwd, $newTelPwd);

        }
        unset($keys);
    }

    /**
     * Sending new PWDto notary  by email
     *
     * @param int    $id
     * @param string $newWebPwd
     * @param string $newTelPwd
     * @throws Exception
     */
    protected function sendEmailForPwdChanged($id, $newWebPwd, $newTelPwd)
    {
        $notary = mvc_model('QueryBuilder')->findOne('notaire',
                                                     array(
                                                         'fields' => 'id, first_name, last_name, crpcen, email_adress',
                                                         'conditions' => 'id = ' . $id,
                                                     )
        );

        if (is_object($notary)) {
            // email headers
            $headers = array('Content-Type: text/html; charset=UTF-8');

            // get
            $etude = mvc_model('Etude')->find_one_by_crpcen($notary->crpcen);
            // check environnement
            $env = getenv('ENV');
            if (empty($env)|| ($env !== 'PROD')) {
                if ($env === 'PREPROD') {
                    $dest = Config::$notificationAddressPreprod;
                } else {
                    $dest = Config::$notificationAddressDev;
                }
            } else {
                $dest = $notary->email_adress;
                if (!$dest) { // notary email is empty
                    // send email to the office
                    if (is_object($etude) && $etude->office_email_adress_1) {
                        $dest = $etude->office_email_adress_1;
                    } elseif (is_object($etude) && $etude->office_email_adress_2) {
                        $dest = $etude->office_email_adress_2;
                    } elseif (is_object($etude) && $etude->office_email_adress_3) {
                        $dest = $etude->office_email_adress_3;
                    }
                }
            }

            // dest must be set
            if ($dest) {
                // prepare message
                $subject = sprintf(Config::$mailPasswordChange['subject'], $notary->first_name . ' ' . $notary->last_name);
                $vars    = array(
                    'webPassword' => $newWebPwd,
                    'telPassword' => $newTelPwd,
                    'notary'      => $notary,
                    'etude'       => $etude
                );
                $message = CriRenderView('mail_notification_password', $vars, 'custom', false);

                // send email
                if (wp_mail($dest, $subject, $message, $headers)) {
                    // reset all flag
                    $this->resetPwd($notary->id, 'off');
                } else {
                    writeLog($notary, 'majnotarypwd.log');
                }
            }
        }
        // free vars
        unset($notary);
    }

    public function sendCridonlineConfirmationMail($etude,$subscription_info,$B2B_B2C) {
        if ($subscription_info['subscription_level'] == 2){
            $level_label = CONST_CRIDONLINE_LABEL_LEVEL_2;
        } else {
            $level_label = CONST_CRIDONLINE_LABEL_LEVEL_3;
        }
        $vars = array (
            'etude'                  => $etude,
            'level_label'            => $level_label,
            'price'                  => $subscription_info['subscription_price'],
            'start_subscription_date'=> $subscription_info['start_subscription_date'],
            'end_subscription_date'  => $subscription_info['end_subscription_date'],
            'id_sepa'                => $subscription_info['id_sepa'],
            'B2B_B2C'                => $B2B_B2C,
            //'urlCGUV'                => mvc_model('Document')->generatePublicUrl
        );

        $documents = array(
            CONST_CRIDONLINE_DOCUMENT_CGUV_URL
        );
        if ($B2B_B2C == 'B2B'){
            $documents[] = CONST_CRIDONLINE_DOCUMENT_MANDAT_SEPA_B2B_URL;
        } else {
            $documents[] = CONST_CRIDONLINE_DOCUMENT_MANDAT_SEPA_B2C_URL;
        }

        $message = CriRenderView('mail_notification_cridonline', $vars, 'custom', false);

        $headers = array('Content-Type: text/html; charset=UTF-8');
        $env = getenv('ENV');
        if (empty($env)|| ($env !== 'PROD')) {
            if ($env === 'PREPROD') {
                $dest = Config::$notificationAddressPreprod;
            } else {
                $dest = Config::$notificationAddressDev;
            }
            $email = wp_mail( $dest , Config::$mailSubjectCridonline, $message, $headers, $documents );
            writeLog("not Prod: " . $email . "\n", "mailog.txt");
        } else {
            $headers[] = 'BCC:'.Config::$notificationAddressCridon;
            if (!empty($etude->office_email_adress_1)){
                $destinataire = $etude->office_email_adress_1;
            } elseif (!empty($etude->office_email_adress_2)){
                $destinataire = $etude->etude->office_email_adress_2;
            } elseif (!empty($etude->office_email_adress_3)){
                $destinataire = $etude->office_email_adress_3;
            }
            if (!empty($destinataire)) {
                wp_mail($destinataire, Config::$mailSubjectCridonline, $message, $headers, $documents );
            }
        }
    }

    /**
     * Set reset update flag
     *
     * @param int $notaryId
     * @param string $action
     *
     * @return void
     */
    public function updateFlagERP($notaryId, $action = 'on')
    {
        $notary                            = array();
        $notary['Notaire']['id']           = $notaryId;
        $notary['Notaire']['cron_update_erp']  = ($action == 'on') ? 1 : 0; // flag pour notifier ERP ( immediatement RAZ par cron "cronExportNotary" )
        $this->save($notary);
    }
}
