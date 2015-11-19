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
     * @var resource
     */
    protected $conn;

    /**
     * @var resource
     */
    protected $results;

    /**
     * @var array
     */
    public $erpNotaireList = array();

    /**
     * @var array
     */
    public $erpNotaireData = array();

    /**
     * @var mixed
     */
    protected static $instance;

    protected function __construct()
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
            // send email
            reportError(CONST_EMAIL_ERROR_CATCH_EXCEPTION, odbc_errormsg());
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
     * fetch ODBC Data
     *
     * @return array|false
     */
    public function fetchData()
    {
        return odbc_fetch_array($this->results);
    }


    /**
     * Prepare OCI Data
     *
     * @return $this
     */
    public function countData()
    {
        return odbc_num_rows($this->results);
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