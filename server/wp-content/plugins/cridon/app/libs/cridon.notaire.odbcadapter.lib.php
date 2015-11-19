<?php
/**
 * Created by PhpStorm.
 * User: valbert
 * Date: 19/11/2015
 * Time: 19:29
 */

class CridonNotaireODBCAdapter extends CridonODBCAdapter
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
     * fetch Data
     *
     * @return $this
     */
    public function fetchData()
    {
        return odbc_fetch_array($this->results);
    }
}