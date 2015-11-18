<?php

/**
 * Description of cridon.odbc.lib.php
 *
 * @package wp_cridon
 * @author eTech
 * @contributor Joelio
 */
class CridonODBCAdapter implements DBConnect
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
    function connection()
    {
        $conn = odbc_connect(
            "Driver=" . CONST_ODBC_DRIVER . ";
				Server=" . CONST_DB_HOST . ";
				Database=" . CONST_DB_DATABASE,
            CONST_DB_USER,
            CONST_DB_PASSWORD
        );

        if (!$conn) {
            // message content
            $message =  sprintf(CONST_EMAIL_ERROR_CATCH_EXCEPTION, odbc_errormsg());

            // send email
            reportError($message, CONST_EMAIL_ERROR_SUBJECT);
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
    public function prepareData()
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