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
     * @param string $option
     * @return mixed
     */
    public function importIntoWpUsers($option = '')
    {
        // init logs
        $this->logs = array();

        switch (strtolower($option)) {
            case self::IMPORT_ODBC_OPTION:
                // @TODO action for ODBC option
                break;
            default : // csv option by default
                $this->importFromCsvFile();
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

                /*foreach($csv->data as $items) {
                    if (isset($items[$csv::NOTAIRE_CRPCEN_OFFSET]) && $items[$csv::NOTAIRE_CRPCEN_OFFSET]) { // valid login
                        $notaires = $this->find_one_by_crpcen($items[$csv::NOTAIRE_CRPCEN_OFFSET]);
                        if (!is_object($notaires)) { // user not already exist

                        } else { // user already exist

                        }

                    }
                    echo '<pre>';
                    print_r($items);
                }*/
            }
        }
    }

    /**
     * Prepare data for listing existing notaire on Site and new from ERP
     */
    private function prepareNotairedata()
    {
        // get list of existing notaire
        $notaires = $this->find();

        // instance of CridonCsvParser
        $csv = $this->csvParser;

        // fill list of existing notaire on site with unique key (crpcen + passwd)
        foreach ($notaires as $notaire) {
            array_push($this->siteNotaireList, $notaire->crpcen . $notaire->web_password);
        }

        // fill list of notaire from ERP (crpcen + passwd)
        foreach ($this->getCsvData() as $items) {
            // only notaire having CRPCEN
            if (isset($items[$csv::NOTAIRE_CRPCEN_OFFSET]) && $items[$csv::NOTAIRE_CRPCEN_OFFSET]) {
                // the only unique key available is the "crpcen + web_password"
                $uniqueKey = intval($items[$csv::NOTAIRE_CRPCEN_OFFSET]) . $items[$csv::NOTAIRE_PWDWEB_OFFSET];
                array_push($this->erpNotaireList, $uniqueKey);

                // notaire data filter
                $this->erpNotaireData[$uniqueKey] = $items;
            }
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
            // @TODO need confirmation if all users not listed in ERP data should be deleted or disabled
            // deleted : call removeUsersNotInList
            // disabled : call disableUserNotInList

            // list of values to be inserted
            $insertValues = array();

            // list of new data
            $newNotaires = $this->getNewNotaireList();

            // instance of CridonCsvParser
            $csvParser = $this->csvParser;

            // insert new data
            if (count($newNotaires) > 0) {
                $options               = array();
                $options['table']      = 'notaire';
                $options['attributes'] = 'category, client_number, first_name, last_name, crpcen, web_password, tel_password, code_interlocuteur, ';
                $options['attributes'] .= 'id_civilite, email_adress, id_fonction, date_modified';

                // prepare multi rows data values
                foreach ($newNotaires as $notaire) {
                    // format date
                    $dateModified = date("Y-m-d",
                                         strtotime(
                                             str_replace(
                                                 array('/', '"'),
                                                 array('-', ''),
                                                 $this->erpNotaireData[$notaire][$csvParser::NOTAIRE_DATEMODIF_OFFSET]
                                             )
                                         )
                    );

                    $value = "(";
                    $value .= "'" . mysql_real_escape_string($this->erpNotaireData[$notaire][$csvParser::NOTAIRE_CATEG_OFFSET]) . "', ";
                    $value .= "'" . mysql_real_escape_string($this->erpNotaireData[$notaire][$csvParser::NOTAIRE_NUMCLIENT_OFFSET]) . "', ";
                    $value .= "'" . mysql_real_escape_string($this->erpNotaireData[$notaire][$csvParser::NOTAIRE_FNAME_OFFSET]) . "', ";
                    $value .= "'" . mysql_real_escape_string($this->erpNotaireData[$notaire][$csvParser::NOTAIRE_LNAME_OFFSET]) . "', ";
                    $value .= "'" . mysql_real_escape_string(intval($this->erpNotaireData[$notaire][$csvParser::NOTAIRE_CRPCEN_OFFSET])) . "', ";
                    $value .= "'" . mysql_real_escape_string($this->erpNotaireData[$notaire][$csvParser::NOTAIRE_PWDWEB_OFFSET]) . "', ";
                    $value .= "'" . mysql_real_escape_string($this->erpNotaireData[$notaire][$csvParser::NOTAIRE_PWDTEL_OFFSET]) . "', ";
                    $value .= "'" . mysql_real_escape_string(intval($this->erpNotaireData[$notaire][$csvParser::NOTAIRE_INTERCODE_OFFSET])) . "', ";
                    $value .= mysql_real_escape_string($this->erpNotaireData[$notaire][$csvParser::NOTAIRE_CIVILIT_OFFSET]) . ", ";
                    $value .= "'" . mysql_real_escape_string($this->erpNotaireData[$notaire][$csvParser::NOTAIRE_EMAIL_OFFSET]) . "', ";
                    $value .= mysql_real_escape_string($this->erpNotaireData[$notaire][$csvParser::NOTAIRE_FONC_OFFSET]) . ", ";
                    $value .= "'" . $dateModified . "'";
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
        } catch (Exception $e) {
            echo 'Exception reçue : ' .  $e->getMessage() . "\n";
        }

        // import into wp_users table
        $this->insertOrUpdateWpUsers();
    }

    /**
     * Remove users not match on ERP data list
     */
    private function removeUsersNotInList()
    {
        try {
            // list of users
            $users = $this->getNotaireToBeDeleted();
            if (count($users) > 0) {
                $queryBulder = mvc_model('QueryBuilder');
                $elements    = "'" . implode("', '", $users) . "'";

                // delete data from cri_notaire
                $options['table']      = 'notaire';
                $options['conditions'] = "CONCAT(`crpcen`, `web_password`) IN ($elements)";
                $queryBulder->delete($options);

                // delete from wp_users
                $options               = array();
                $options['table']      = 'users';
                $options['conditions'] = "`{$this->wpdb->users}`.`ID` IN (
                                    SELECT `id_wp_user` FROM `{$this->table}`
                                    WHERE CONCAT(`crpcen`, `web_password`) IN ($elements)
                                    )";
                $queryBulder->delete($options);
            }
        } catch (Exception $e) {
            echo 'Exception reçue : ' .  $e->getMessage() . "\n";
        }
    }

    /**
     * Action for disable users in wp_users not match on the ERP data list
     */
    private function disableUserNotInList()
    {

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
                // list of values to be inserted
                $insertValues = array();

                // instance of cridon tools
                $criTools = new CridonTools();

                // bulk update separate
                // @TODO to be completed with other field needed for update
                // it's concerned only a specific data in wp_users
                $bulkPwdUpdate = $bulkNiceNameUpdate = $bulkEmailUpdate = $bulkDisplayNameUpdate = array();

                // query builder options
                $options               = array();
                $options['table']      = 'users';
                $options['attributes'] = 'user_login, user_pass, user_nicename, user_email, user_registered, user_status,  display_name';

                foreach ($notaires as $notaire) {
                    // check if user already exist
                    $userName = $notaire->crpcen . CONST_LOGIN_SEPARATOR . $notaire->id;
                    // @TODO to be optimized
                    $userId = $criTools->isUserExist($userName)->ID;

                    $displayName = $notaire->first_name . ' ' . $notaire->last_name;

                    if (!$userId) { // prepare the bulk insert query
                        $value = "(";

                        $value .= "'" . mysql_real_escape_string($userName) . "', ";
                        $value .= "'" . wp_hash_password($notaire->web_password) . "', ";
                        $value .= "'" . sanitize_title($displayName) . "', ";
                        $value .= "'" . $notaire->email_adress . "', ";
                        $value .= "'" . date('Y-m-d H:i:s') . "', ";
                        $value .= CONST_STATUS_ENABLED . ", ";
                        $value .= "'" . mysql_real_escape_string($displayName) . "'";

                        $value .= ")";

                        $insertValues[] = $value;

                    } else { // prepare the bulk update query
                        if ($notaire->id_wp_user) {
                            // pwd
                            $bulkPwdUpdate[] = " ID = {$notaire->id_wp_user} THEN '" . wp_hash_password($notaire->web_password) . "' ";
                            // nicename
                            $bulkNiceNameUpdate[] = " ID = {$notaire->id_wp_user} THEN '" . sanitize_title($displayName) . "' ";
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
                }
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
                $update = "`{$this->table}`.`crpcen` = '" . $notaire->crpcen . "' AND `cri_notaire`.`id` = " . $notaire->id;
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