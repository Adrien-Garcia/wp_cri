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
     * Constructor
     */
    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;

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
        // instance of Cridon parser
        $csv = new CridonCsvParser();
        $csv->enclosure = '';
        $csv->encoding(null, 'UTF-8');
        $csv->auto(CONST_IMPORT_CSV_NOTAIRE_FILE_PATH);

        // no error was found
        if (property_exists($csv, 'data') && $csv->error <= 0) {
            // stuff for importing data

        }
    }

}