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
     * Secure Access Page
     *
     * @return void
     */
    protected function secureAccess()
    {
        // get notaire by id_wp_user
        $notaireData = $this->model->find_one_by_id_wp_user($this->current_user->ID);

        // check if user is not logged in
        // or notaire id (url params) not equal to WP user session data
        if (!is_user_logged_in() || !$notaireData->id || $this->params['id'] !== $notaireData->id) {
            wp_logout();//logout current user
            // redirect user to home page
            $this->redirect(home_url());
        }
    }

    /**
     * Set template variables
     *
     * @param mixed $vars
     */
    protected function set_vars($vars)
    {
        $this->set('object', $vars);
        MvcObjectRegistry::add_object($this->model->name, $this->object);
    }

    /**
     * Get notaire object var
     *
     * @return mixed | bool
     */
    protected function get_object()
    {
        if (!empty($this->model->invalid_data)) {
            if (!empty($this->params['id']) && empty($this->model->invalid_data[$this->model->primary_key])) {
                $this->model->invalid_data[$this->model->primary_key] = $this->params['id'];
            }
            $object = $this->model->new_object($this->model->invalid_data);
        } else if (!empty($this->params['id'])) {
            $object = $this->model->find_by_id($this->params['id']);
        }
        if (!empty($object)) {
            return $object;
        }
        MvcError::warning('Object not found.');

        return false;
    }

    /**
     * Notaire dashboard page
     * Associated template : app/views/notaires/show.php
     *
     * @return void
     */
    public function show()
    {
        // access secured
        $this->secureAccess();

        // set template vars
        // @TODO to be completed with others notaire dynamic data
        $vars = $this->get_object();
        $this->set_vars($vars);
    }

    /**
     * Notaire Questions page
     * Associated template : app/views/notaires/questions.php
     *
     * @return void
     */
    public function questions()
    {
        // access secured
        $this->secureAccess();
    }

    /**
     * Notaire Profil page
     * Associated template : app/views/notaires/profil.php
     *
     * @return void
     */
    public function profil()
    {
        // access secured
        $this->secureAccess();

        // set template vars
        // @TODO to be completed with others notaire dynamic data
        $vars = $this->get_object();
        $this->set_vars($vars);
    }

    /**
     * Notaire Facturation page
     * Associated template : app/views/notaires/facturation.php
     *
     * @return void
     */
    public function facturation()
    {
        // access secured
        $this->secureAccess();
    }
}