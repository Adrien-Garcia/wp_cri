<?php

/**
 * Cridon ODBC Adapter
 *
 * @package wp_cridon
 * @author eTech
 * @contributor Joelio
 */
class CridonODBC
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

//        echo '<pre>' . $sql . '</pre><br>';
//
//        echo '<pre>' . var_dump(odbc_num_rows($this->results)) . '</pre><br>';

        return $this->results;
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