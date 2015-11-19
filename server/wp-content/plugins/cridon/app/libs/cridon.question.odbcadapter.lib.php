<?php

/**
 * Description of cridon.question.odbcadapter.lib.php
 *
 * @package wp_cridon
 * @author eTech
 * @contributor Joelio
 */

require_once 'cridon.odbcadapter.lib.php';

class CridonQuestionODBCAdapter extends CridonODBCAdapter
{
    
    /**
     * @var array
     */
    public $erpQuestList = array();

    /**
     * @var array
     */
    public $erpQuestData = array();

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
     * fetch ODBC Data
     *
     * @return array|false
     */
    public function fetchData()
    {
        return odbc_fetch_array($this->results);
    }

}