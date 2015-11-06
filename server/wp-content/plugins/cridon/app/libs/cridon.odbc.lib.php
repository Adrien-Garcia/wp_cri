<?php

/**
 * Description of cridon.odbc.lib.php
 *
 * @package wp_cridon
 * @author eTech
 * @contributor Joelio
 */
class CridonODBCAdapter
{

    /**
     * @var string : category field in Output data
     */
    const NOTAIRE_CATEG      = 'BCGCOD_0';

    /**
     * @var string : client_number field in Output data
     */
    const NOTAIRE_NUMCLIENT  = 'BPCNUM_0';

    /**
     * @var string : crpcen field in Output data
     */
    const NOTAIRE_CRPCEN     = 'YCRPCEN_0';

    /**
     * @var string : Web Code field in Output data
     */
    const NOTAIRE_CODEWEB    = 'YCODWEB_0';

    /**
     * @var string : web_password field in Output data
     */
    const NOTAIRE_PWDWEB     = 'YMDPWEB_0';

    /**
     * @var string : tel_password field in Output data
     */
    const NOTAIRE_PWDTEL     = 'ZMDPTEL_0';

    /**
     * @var string : id_sigle field in Output data
     */
    const NOTAIRE_SIGLE      = 'BPRLOG_0';

    /**
     * @var string : office_name field in Output data
     */
    const NOTAIRE_OFFICENAME = 'BPRNAM_0';

    /**
     * @var string : status field in Output data
     */
    const NOTAIRE_STATUS  = 'YACTIF_0';

    /**
     * @var string : code_interlocuteur field in Output data
     */
    const NOTAIRE_INTERCODE  = 'CCNCRM_0';

    /**
     * @var string : id_civilite field in Output data
     */
    const NOTAIRE_CIVILIT    = 'CNTTTL_0';

    /**
     * @var string : first_name field in Output data
     */
    const NOTAIRE_FNAME      = 'CNTFNA_0';

    /**
     * @var string : last_name field in Output data
     */
    const NOTAIRE_LNAME      = 'CNTLNA_0';

    /**
     * @var string : tel field in Output data
     */
    const NOTAIRE_TEL      = 'TELNOT_0';

    /**
     * @var string : last_name field in Output data
     */
    const NOTAIRE_FAX        = 'FAXNOT_0';

    /**
     * @var string : mobile field in Output data
     */
    const NOTAIRE_PORTABLE   = 'CNTMOB_0';

    /**
     * @var string : email_adress field in Output data
     */
    const NOTAIRE_EMAIL      = 'WEBNOT_0';

    /**
     * @var string : id_fonction field in Output data
     */
    const NOTAIRE_FONC       = 'CNTFNC_0';

    /**
     * @var string : adress_1 field in Output data
     */
    const NOTAIRE_ADRESS1    = 'BPAADDLIG1_0';

    /**
     * @var string : adress_2 field in Output data
     */
    const NOTAIRE_ADRESS2    = 'BPAADDLIG2_0';

    /**
     * @var string : adress_3 field in Output data
     */
    const NOTAIRE_ADRESS3    = 'BPAADDLIG3_0';

    /**
     * @var string : cp field in Output data
     */
    const NOTAIRE_CP         = 'POSCOD_0';

    /**
     * @var string : city field in Output data
     */
    const NOTAIRE_CITY       = 'CTY_0';

    /**
     * @var string : office_email_adress_1 field in Output data
     */
    const NOTAIRE_MAIL1      = 'WEB_0';

    /**
     * @var string : office_email_adress_2 field in Output data
     */
    const NOTAIRE_MAIL2      = 'ZMAIL2_0';

    /**
     * @var string : office_email_adress_3 field in Output data
     */
    const NOTAIRE_MAIL3      = 'ZMAIL3_0';

    /**
     * @var string : date_modified field in Output data
     */
    const NOTAIRE_DATEMODIF  = 'UPDDAT_0';

    /**
     * @var array
     */
    public $erpNotaireList = array();

    /**
     * @var array
     */
    public $erpNotaireData = array();

    /**
     * @var resource
     */
    protected $conn;

    /**
     * @var resource
     */
    protected $results;

    /**
     * @var mixed
     */
    protected static $instance;

    private function __construct()
    {
        $this->conn = $this->connection();
    }

    /**
     * Get instance
     *
     * @return CridonODBCAdapter|mixed
     */
    public static function getInstance()
    {
        if (!isset(self::$instance))
        {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * ODBC Connexion
     *
     * @return resource
     */
    protected function connection()
    {
        $conn = odbc_connect(
            "Driver=" . CONST_ODBC_DRIVER . ";
				Server=" . CONST_ODBC_HOST . ";
				Database=" . CONST_ODBC_DATABASE,
            CONST_ODBC_USER,
            CONST_ODBC_PASSWORD
        );

        if (!$conn) {
            // message content
            $message =  sprintf(CONST_EMAIL_ERROR_CATCH_EXCEPTION, odbc_errormsg());

            // send email
            $multiple_recipients = array(
                CONST_EMAIL_ERROR_CONTACT,
                CONST_EMAIL_ERROR_CONTACT_CC
            );
            wp_mail($multiple_recipients, CONST_EMAIL_ERROR_SUBJECT, $message);
        }

        return $conn;
    }

    /**
     * Get result
     *
     * @param string $sql
     * @return resource
     */
    public function getResults($sql)
    {
        $this->results = odbc_exec($this->conn, $sql);

        return $this;
    }

    /**
     * Prepare ODBC Data
     *
     * @return $this
     */
    public function prepareODBCData()
    {
        while ($data = odbc_fetch_array($this->results)) {
            if (isset( $data[self::NOTAIRE_CRPCEN] ) && intval($data[self::NOTAIRE_CRPCEN]) > 0) { // valid login
                // the only unique key available is the "crpcen + web_password"
                $uniqueKey = intval($data[self::NOTAIRE_CRPCEN]) . $data[self::NOTAIRE_PWDWEB];
                array_push($this->erpNotaireList, $uniqueKey);

                // notaire data filter
                $this->erpNotaireData[$uniqueKey] = $data;
            }
        }

        return $this;
    }

    /**
     * Close the connection
     */
    public function closeConnection()
    {
        // Free Result
        odbc_free_result($this->results);

        // Close Connection
        odbc_close($this->conn);
    }

}