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
    public $display_field = 'first_name';

    /**
     * @var string
     */
    public $table = '{prefix}notaire';

    /**
     * @var string
     */
    protected $logs;

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
     */
    public function importIntoWpUsers()
    {
        $this->importDataFromLocalTable();
        return $this->logs;
    }

    /**
     * Action for importing data from cri_notaire into wp_users
     */
    public function importDataFromLocalTable()
    {
        $this->logs = array();
        $notaires   = $this->find();
        foreach ($notaires as $notaire) {
            // check if user already exist
            $userName  = $notaire->crpcen;
            $userId    = username_exists($userName);
            $userDatas = array(
                'user_login' => $userName,
                'user_pass'  => $notaire->web_password,
                'user_email' => $notaire->email_adress,
                'first_name' => $notaire->first_name,
                'last_name'  => $notaire->last_name,
            );
            if (!$userId) {
                $userDatas['role'] = CONST_NOTAIRE_ROLE;

                add_filter('sanitize_user', array($this, 'custom_sanitize_user'), 10, 3);
                $userId = wp_insert_user($userDatas);
                remove_filter('sanitize_user', array($this, 'custom_sanitize_user'), 10);

                // On success
                if (!is_wp_error($userId)) {
                    array_push($this->logs, $userName . ' / userId : ' . $userId);

                    // @TODO needed only if notaire data before wp_users
                    // update notaire.id_wp_user
                    $this->update($notaire->id, array('id_wp_user' => $userId));
                }
            } else {
                $userDatas['ID'] = $userId;
                $userDatas['user_nicename'] = sanitize_user($notaire->first_name . '_' . $notaire->last_name);
                $userDatas['display_name']  = $notaire->first_name . ' ' . $notaire->last_name;
                wp_update_user($userDatas);
            }
        }
    }

}