<?php

/**
 * Class NotairesController
 * @author Etech
 * @contributor Joelio
 */
class NotairesController extends BasePublicController
{

	/**
	 * @var mixed
	 */
	public $current_user;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		global $current_user;

		$this->current_user = $current_user;

		parent::__construct();
	}

    /**
     * Import Notaire into wp_users
     */
    public function import()
    {
        $rets = $this->model->importIntoWpUsers();

        $this->set('rets', $rets);
    }

    /**
     * Notaire dashbord
     */
    public function show()
    {
        // check if user is not logged in
        if ( !is_user_logged_in() ) {
            // redirect user to home page
            $this->redirect( home_url() );
        } else {
            // notaire data verification
            $notaireId = $this->params['id'];

            $notaireData = $this->model->find_one_by_id_wp_user($this->current_user->ID);

            // notaire id (url params) should be matched with WP user session data
            if (!$notaireData->id || $notaireId != $notaireData->id) {
                // redirect user to home page
                $this->redirect( home_url() );
            }
        }

        parent::show();
    }
}