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

    public function index() {
        $this->generateError();
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
        $this->params['per_page'] = !empty($this->params['per_page']) ? $this->params['per_page'] : DEFAULT_QUESTION_PER_PAGE;        
        $this->params['treated']  = 2;//question answered: treated = 2
        $collection = $this->model->paginate($this->params);//Get questions answered        
        $this->set('objects', $collection['objects']);
        $this->set_pagination($collection);
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
        // @TODO to be completed with others notaire dynamic data
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
        //unsubscribe to newsletter
        if(isset($_POST['disabled'])){
            $disabled = $_POST['disabled'] == 1 ? 0 : 1;
            $notaire = $this->model->getUserConnectedData();
            $update = array();
            $update['Notaire']['id'] = $notaire->id;
            $update['Notaire']['newsletter'] = $disabled;
            $this->model->save($update);
            $options = array(
                'controller' => 'notaires',
                'action'     => 'profil',
                'id'         => $this->params['id']
            );
            $publicUrl  = MvcRouter::public_url($options);
            wp_redirect( $publicUrl, 302 );
            exit;
        }
        if (isset($_POST) && !empty($_POST)) {
            $notaire = $this->model->getUserConnectedData();
            if (!empty($notaire)) {
                $options = array(
                    'conditions' => array(
                        'Matiere.displayed' => 1
                    )
                );
                $matieres = mvc_model('matiere')->find($options);
                //Clean $_POST before
                $data = $this->clean($_POST);
                $toCompare = array();
                //Create array to compare Matiere in $_POST
                foreach ($matieres as $mat) {
                    $toCompare[] = $mat->id;
                }
                $insert = array();
                $insert['Notaire']['id'] = $notaire->id;
                $insert['Notaire']['Matiere']['ids'] = array();
                if (isset($data['matieres'])) {
                    foreach ($data['matieres'] as $v) {
                        //Check if current Matiere is valid
                        if (in_array($v, $toCompare)) {
                            $insert['Notaire']['Matiere']['ids'][] = $v;
                        }
                    }
                }
                //Put in DB
                $this->model->save($insert);
            }

        }
        // set template vars
        // @TODO to be completed with others notaire dynamic data
        $vars = $this->get_object();
        $this->set_vars($vars);
    }
    
    /**
     * Get questions pending
     * 
     * @return mixed
     */
    public function getPending(){ 
        return $this->model->getPending(array(0,1),$this->params);
    }
    
    /**
     * Get questions
     *
     * @return mixed
     */
    public function getQuestions(){        
        $this->params['per_page'] = DEFAULT_QUESTION_PER_PAGE;//set number per page
        $this->params['treated']  = array(0,1,2);//all questions
        $collection = $this->model->paginate($this->params);//Get questions 
        return $collection['objects'];
    }
    
    /**
     * Override
     * 
     * @param array $collection
     */
    public function set_pagination($collection) {
        if( !is_admin() ){
            $params = $this->params;
            unset($params['page']);
            unset($params['conditions']);
            $url = MvcRouter::public_url(
                    array(
                        'controller' => $this->name, 'action' => $this->action,
                        'id' => $this->params['id']
                    )
             );
            $this->pagination = array(
                'base' => $url.'%_%',
                'format' => '?page=%#%',
                'total' => $collection['total_pages'],
                'current' => $collection['page'],
                'add_args' => $params
            );

            unset($collection['objects']);
            $this->pagination = array_merge($collection, $this->pagination);            
        }else{
            parent::set_pagination($collection);
        }
    }   
    
}