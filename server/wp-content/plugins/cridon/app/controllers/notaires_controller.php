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
        if( isset( $_POST ) && !empty( $_POST ) && isset( $_POST['matieres'] ) ){
            $notaire = $this->model->getUserConnectedData();
            if( !empty( $notaire ) ){
                $options = array(
                    'conditions' => array(
                        'Matiere.displayed' => 1
                    )
                );
                $matieres = mvc_model('matiere')->find( $options );
                //Clean $_POST before
                $data = $this->clean( $_POST ); 
                $toCompare = array();
                //Create array to compare Matiere in $_POST
                foreach ( $matieres as $mat ){
                    $toCompare[] = $mat->id;
                }
                $insert = array();
                $insert['Notaire']['id'] = $notaire->id;
                foreach( $data['matieres'] as $v ){
                    //Check if current Matiere is valid
                    if( in_array( $v, $toCompare ) ){
                        $insert['Notaire']['Matiere']['ids'][] = $v;                        
                    }
                }
                //Put in DB
                $this->model->save( $insert );               
            }
           
        }
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
}