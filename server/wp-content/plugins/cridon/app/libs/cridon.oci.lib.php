<?php

/**
 * Description of cridon.odbc.lib.php
 *
 * @package wp_cridon
 * @author eTech
 * @contributor Joelio
 */
class CridonOCIAdapter implements DBConnect
{

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
    public function connection()
    {
        $conf = "
(
  DESCRIPTION = (
    ADDRESS = (
      PROTOCOL = TCP
    )
    (HOST =".CONST_DB_HOST.")
    (PORT = ".CONST_DB_PORT.")
  )
  (CONNECT_DATA =
    (SERVER = DEDICATED)
    (SERVICE_NAME = ".CONST_DB_DATABASE.")
    (INSTANCE_NAME = ".CONST_DB_DATABASE.")
  )
)";
        $conn = oci_connect(CONST_DB_USER, CONST_DB_PASSWORD, $conf);

        if (!$conn) {
            // message content
            $message =  sprintf(CONST_EMAIL_ERROR_CATCH_EXCEPTION, oci_error());

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
        //parse and prepare query
        $query = oci_parse($this->conn, $sql);
        oci_execute($query);

        return $this;
    }

    /**
     * Prepare ODBC Data
     *
     * @return $this
     */
    public function prepareData()
    {
        while ($data = oci_fetch_array($this->conn)) {
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
        oci_free_statement($this->conn);

        // Close Connection
        oci_close($this->conn);
    }

}