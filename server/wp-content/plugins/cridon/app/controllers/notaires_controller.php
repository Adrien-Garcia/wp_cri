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
     * @var mixed
     */
    protected $notaryData;

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
     * Generate error
     *
     * @global \WP_Query $wp_query
     */
    private function generateError(){
        global $wp_query;
        header("HTTP/1.0 404 Not Found - Archive Empty");
        $wp_query->set_404();
        if( file_exists(TEMPLATEPATH.'/404.php') ){
            require TEMPLATEPATH.'/404.php';
        }
        exit;
    }

    /**
     * Secure Access Page
     *
     * @return void
     */
    protected function secureAccess()
    {
        // get notaire by id_wp_user
        $this->notaryData = $this->model->find_one_by_id_wp_user($this->current_user->ID);

        // check if user is not logged in
        // or notaire id (url params) not equal to WP user session data
        if (!is_user_logged_in()
            || !$this->notaryData->id
            || (isset($this->params['id']) && $this->params['id'] !== $this->notaryData->id)) {
            wp_logout();//logout current user
            // redirect user to home page
            $this->redirect(home_url());
        }

        // set notary id in params
        // needed to retrieve notary data by the MVC system
        $this->params['id'] = $this->notaryData->id;
    }

    /**
     * Set template variables
     *
     * @param mixed $vars
     */
    protected function set_vars($vars)
    {
        if (is_object($vars) && property_exists($vars, 'client_number')) {
            $datas = mvc_model('solde')->getSoldeByClientNumber($vars->client_number);

            // init data
            $vars->nbAppel = $vars->nbCourrier = $vars->quota = $vars->pointConsomme = $vars->solde = 0;
            $vars->date = '';

            // quota, pointCosomme, solde
            if (isset($datas[0])) {
                $vars->quota = $datas[0]->quota;
                $vars->pointConsomme = $datas[0]->totalPoint;
                $vars->solde = intval($datas[0]->quota) - intval($datas[0]->totalPoint);
                $vars->date = date('d/m/Y', strtotime($datas[0]->date_arret));
            }

            // fill nbAppel && nbCourrier
            foreach($datas as $data) {
                if ($data->type_support == CONST_SUPPORT_APPEL_ID) {
                    $vars->nbAppel += $data->nombre;
                } elseif ($data->type_support == CONST_SUPPORT_COURRIER_ID) {
                    $vars->nbCourrier += $data->nombre;
                }
            }

        }
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
     * Notaire dashboard Content Block (AJAX Friendly)
     * Associated template : app/views/notaires/contentdashboard.php
     *
     * @return void
     */
    public function contentdashboard()
    {
        $this->prepareDashboard();
        $is_ajax = true;
        CriRenderView('contentdashboard', get_defined_vars(),'notaires');
        die();
    }

    /**
     * Notaire dashboard page
     * Associated template : app/views/notaires/show.php
     *
     * @return void
     */
    public function show()
    {
        $this->prepareDashboard();
    }

    /**
     * Notaire Questions Content Block (AJAX Friendly)
     * Associated template : app/views/notaires/contentquestions.php
     *
     * @return void
     */
    public function contentquestions()
    {
        $this->prepareSecureAccess();
        $is_ajax = true;
        CriRenderView('contentquestions', get_defined_vars(),'notaires');
        die();
    }

    /**
     * Notaire Questions page
     * Associated template : app/views/notaires/questions.php
     *
     * @return void
     */
    public function questions()
    {
        $this->prepareSecureAccess();

    }

    /**
     * Notaire Profil Content Block (AJAX Friendly)
     * Associated template : app/views/notaires/contentprofil.php
     *
     * @return void
     */
    public function contentprofil()
    {
        $this->prepareProfil();
        $is_ajax = true;
        CriRenderView('contentprofil', get_defined_vars(),'notaires');
        die();
    }

    /**
     * Notaire Profil page
     * Associated template : app/views/notaires/profil.php
     *
     * @return void
     */
    public function profil()
    {
        $this->prepareProfil();
    }

    /**
     * Notaire Facturation Content Block (AJAX Friendly)
     * Associated template : app/views/notaires/contentfacturation.php
     *
     * @return void
     */
    public function contentfacturation()
    {
        // access secured
        $this->prepareSecureAccess();
        $is_ajax = true;
        CriRenderView('contentfacturation', get_defined_vars(),'notaires');
        die();
    }
    /**
     * Notaire Facturation page
     * Associated template : app/views/notaires/facturation.php
     *
     * @return void
     */
    public function facturation()
    {
        $this->prepareSecureAccess();
    }

    /**
     * Cleaning data
     * 
     * @param mixed $data
     * @return mixed
     */
    private function clean( $data ){
        $clean_input = Array();
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $clean_input[$k] = $this->clean( $v );
            }
        } else {
            $clean_input = trim( strip_tags( $data ) );
        }
        return $clean_input;
    }

    public function newsletterSubscription()
    {
        // init response
        $ret = 'invalidemail';

        // Verify that the nonce is valid.
        if (isset($_REQUEST['token']) && wp_verify_nonce($_REQUEST['token'], 'process_newsletter_nonce') && isset($_REQUEST['email'])) {
            // find the notaire email
            $notaire = $this->model->find_one_by_email_adress($_REQUEST['email']);
//            echo '<pre>'; die(print_r($notaire));
//
            // only an individual email is valid
            if (is_object($notaire) && $notaire->id && isset($_REQUEST['state'])) {
                // update notaire newsletter
                $notaires = array(
                    'Notaire' => array(
                        'id'         => $notaire->id,
                        'newsletter' => intval($_REQUEST['state'])
                    )
                );
                $this->model->save($notaires);
                $ret = 'success';
            }
        }

        echo json_encode($ret);

        die;
    }

    protected function prepareDashboard()
    {
        // access secured
        $this->prepareSecureAccess();

        // set template vars
        $vars = $this->get_object();
        $this->set_vars($vars);
        return $vars;
    }

    protected function prepareSecureAccess()
    {
// access secured
        $this->secureAccess();
    }

    protected function prepareProfil()
    {
        // access secured
        $this->prepareSecureAccess();

        if (isset($_POST)
            && !empty($_POST)
            && !empty($this->notaryData)
        ) {
            // newsletter
            if(isset($_POST['disabled'])) {
                $this->model->newsletterSubscription($this->notaryData);
            }

            // centre d'interets
            if (isset($_POST['matieres'])) {
                //Clean $_POST before
                $data = $this->clean($_POST);
                $this->model->manageInterest($this->notaryData, $data);
            }

            // maj profil et/ou données d'etude
            if (in_array($this->notaryData->id_fonction, Config::$allowedNotaryFunctionToEditProfil)) {
                $this->model->updateProfil($this->notaryData->id, $this->notaryData->crpcen);
            }
        }
        // set template vars
        $vars = $this->get_object();
        $this->set_vars($vars);
    }
}