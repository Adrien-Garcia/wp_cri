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
    public function dashbord()
    {
        // check if user is not logged in
        // or he's not a notaire
        if ( !is_user_logged_in()
             || ( !in_array( CONST_NOTAIRE_ROLE, $this->current_user->roles ) )
        ) {
            $adminUrl = get_bloginfo( 'url' ) . '/login/';
            $this->redirect( $adminUrl );
        }

        $this->set('users', $this->current_user);
    }
}