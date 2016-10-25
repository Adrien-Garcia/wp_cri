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
    protected $statementId;

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
        $conn = oci_connect(CONST_DB_USER, CONST_DB_PASSWORD, $conf, 'AL32UTF8');

        if (!$conn || !is_resource($conn)) {
            $error = oci_error();
            $error = empty($error) ? CONST_CONNECTION_FAILED : $error;
            writeLog($error, 'connexion.log');
            reportError(CONST_EMAIL_ERROR_CATCH_EXCEPTION, $error['message'],'Cridon - '.CONST_CONNECTION_FAILED);
            throw new Exception($error['message'], $error['code']);
        }

        return $conn;
    }

    /**
     * Get result
     *
     * @param string $sql
     * @return resource
     */
    public function execute($sql)
    {
        if (!empty($this->statementId)) {
            oci_free_statement($this->statementId);
        }

        //remove potential ending semi-colon.
        if (substr($sql, -1) == ';') {
            $sql = substr($sql, -1);
        }
        //parse and prepare query
        $this->statementId = oci_parse($this->conn, $sql);
        $isExec = oci_execute($this->statementId);
        if (!$isExec) {
            $error = oci_error();
            $error = empty($error) ? CONST_CONNECTION_FAILED."\n".$sql : $error['message'];
            $error .= "\n".$sql;
            writeLog($error, 'execute.log');
            reportError(CONST_EMAIL_ERROR_CATCH_EXCEPTION, $error,'Cridon - '.CONST_CONNECTION_FAILED);
            throw new Exception($error);
        }
        return $this;
    }

    /**
     * Prepare OCI Data
     *
     * @return $this
     */
    public function fetchData()
    {
        return oci_fetch_array($this->statementId);
    }

    /**
     * Close the connection
     */
    public function closeConnection()
    {
        // Free Result
        oci_free_statement($this->statementId);

        // Close Connection
        oci_close($this->conn);
    }

}