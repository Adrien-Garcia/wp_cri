<?php

/**
 * Class NotairesController
 * @author Etech
 * @contributor Joelio
 */
class NotairesController extends BasePublicController
{

    /**
     * @var object Notaire
     */
    protected $current_notaire;

    /**
     * Secure Access Page
     *
     * @return void
     */
    protected function secureAccess()
    {
        global $mvc_params;

        // check if user is logged in
        if (!is_user_logged_in()) { // need to be redirected to the right page after login
            CriRefuseAccess();
        } elseif (!in_array(CONST_NOTAIRE_ROLE, (array) $this->current_user->roles)) { // user is not allowed to access the content
            // logout current user
            wp_logout();
            // redirect user to home page
            $this->redirect(home_url());
        } elseif (isset($mvc_params['action'])
                  && (in_array($mvc_params['action'],Config::$protected_pages))
                  && !$this->model->userCanAccessSensitiveInfo()
        ) { // check if is page sensitive information && notary can access
            // redirect to profil page
            $url = mvc_public_url(array('controller' => 'notaires', 'action' => 'show'));
            $url.='?error=FONCTION_NON_AUTORISE';
            $this->redirect($url);
        }

        // get current notary data
        $this->current_notaire = $this->model->find_one_by_id_wp_user($this->current_user->ID);

        // set notary id in params
        // needed to retrieve notary data by the MVC system
        if (!in_array($this->params['action'], Config::$exceptedActionForRedirect301)) {
            $this->params['id'] = $this->current_notaire->id;
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
        if (empty($this->current_notaire)) {
            if (!empty($this->model->invalid_data)) {
                if (!empty($this->params['id']) && empty($this->model->invalid_data[$this->model->primary_key])) {
                    $this->model->invalid_data[$this->model->primary_key] = $this->params['id'];
                }
                $this->current_notaire = $this->model->new_object($this->model->invalid_data);
            } else if (!empty($this->params['id'])) {
                $this->current_notaire = $this->model->find_one_by_id_wp_user($this->params['id']);
            }
        }
        if (!empty($this->current_notaire)) {
            return $this->current_notaire;
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
        $this->show();
        $vars = $this->view_vars;
        $vars['is_ajax'] = true;
        $vars['controller'] = $vars['this']; //mandatory due to variable name changes in page-mon-compte.php "this" -> "controller"
        CriRenderView('contentdashboard', $vars,'notaires');
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
        $questions = $this->getQuestions();
        $this->set('questions', $questions);
        $notaire = CriNotaireData();
        $this->set('notaire', $notaire);
        $this->set('messageError', '');
        if (isset($_REQUEST['error'])){
            if ($_REQUEST['error'] == 'FONCTION_NON_AUTORISE'){
                $this->set('messageError', CONST_ERROR_MSG_FONCTION_NON_AUTORISE);
            }
            unset ($_REQUEST['error']);
        }

        // tab rank
        $this->set('onglet', CONST_ONGLET_DASHBOARD);
    }

    /**
     * Notaire Questions Content Block (AJAX Friendly)
     * Associated template : app/views/notaires/contentquestions.php
     *
     * @return void
     */
    public function contentquestions()
    {
        //$this->prepareSecureAccess();
        $this->questions();
        $vars = $this->view_vars;
        $vars['is_ajax'] = true;
        $vars['controller'] = $vars['this']; //mandatory due to variable name changes in page-mon-compte.php "this" -> "controller"
        CriRenderView('contentquestions', $vars,'notaires');
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
        $this->params['status'] = CONST_QUEST_ANSWERED;
        $collection = $this->model->paginate($this->params);//Get questions answered
        $answered = $collection['objects'];
        $this->set('answered', $answered);
        $pending = $this->getPending();
        $this->set('pending', $pending);
        $juristesPending = Question::getJuristeAndAssistantFromQuestions($pending);
        $this->set('juristesPending',$juristesPending);
        $juristesAnswered = Question::getJuristeAndAssistantFromQuestions($answered);
        $this->set('juristesAnswered',$juristesAnswered);
        $matieres = getMatieresByQuestionNotaire();
        $this->set('matieres',$matieres);
        $notaire = CriNotaireData();
        $this->set('notaire',$notaire);

        $this->set_pagination($collection);

        // tab rank
        $this->set('onglet', CONST_ONGLET_QUESTION);
    }

    /**
     * Notaire Profil Content Block (AJAX Friendly)
     * Associated template : app/views/notaires/contentprofil.php
     *
     * @return void
     */
    public function contentprofil()
    {
        $this->profil();
        $vars = $this->view_vars;
        $vars['is_ajax'] = true;
        $vars['controller'] = $vars['this']; //mandatory due to variable name changes in page-mon-compte.php "this" -> "controller"
        CriRenderView('contentprofil', $vars,'notaires');
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
        $this->cridonline();
        $message = '';
        if (isset($_REQUEST['message'])){
            if ($_REQUEST['message'] == 'modifyprofil'){
                $message = CONST_PROFIL_MODIFY_SUCCESS_MSG;
            } elseif ($_REQUEST['message'] == 'modifyoffice') {
                $message = CONST_PROFIL_OFFICE_MODIFY_SUCCESS_MSG;
            } elseif ($_REQUEST['message'] == 'modifypassword') {
                $message = CONST_PROFIL_PASSWORD_SUCCESS_MSG;
            }
            unset ($_REQUEST['message']);
        }
        $this->set('message', $message);
        $this->set('matieres', getMatieresByNotaire());
        // tab rank
        $this->set('onglet', CONST_ONGLET_PROFIL);
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
        $this->facturation();
        $vars = $this->view_vars;
        $vars['is_ajax'] = true;
        $vars['controller'] = $vars['this']; //mandatory due to variable name changes in page-mon-compte.php "this" -> "controller"
        CriRenderView('contentfacturation', $vars,'notaires');
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
        $notaire = CriNotaireData();
        $this->set('notaire',$notaire);
        $content = get_post(CONST_FACTURATION_PAGE_ID)->post_content;
        $this->set('content',$content);

        // tab rank
        $this->set('onglet', CONST_ONGLET_FACTURATION);
    }

    /**
     * Notaire CridOnline Content Block (AJAX Friendly)
     * Associated template : app/views/notaires/contentcridonline.php
     *
     * @return void
     */
    public function contentcridonline()
    {
        // access secured
        $this->cridonline();
        $vars = $this->view_vars;
        $vars['is_ajax'] = true;
        $vars['controller'] = $vars['this']; //mandatory due to variable name changes in page-mon-compte.php "this" -> "controller"
        CriRenderView('contentcridonline', $vars,'notaires');
        die();
    }
    /**
     * Notaire cridonline page
     * Associated template : app/views/notaires/cridonline.php
     *
     * @return void
     */
    public function cridonline()
    {
        $this->prepareSecureAccess();
        $notaire = CriNotaireData();
        $this->set('notaire', $notaire);

        $options = array('conditions' => array('crpcen' => $notaire->crpcen));
        /** @var $etude Etude*/
        $etude   = mvc_model('Etude')->find_one($options);
        $subscriptionInfos = mvc_model('Etude')->getRelatedPrices($etude);
        if (is_array($subscriptionInfos) && count($subscriptionInfos) > 0) {
            foreach ($subscriptionInfos as $level => $price) {
                //set name of variable
                $priceVeilleLevelx = 'priceVeilleLevel' . $level;
                $this->set($priceVeilleLevelx, $price);
            }
        }

        if (isset($_REQUEST['error'])){
            if ($_REQUEST['error'] == 'NIVEAU_VEILLE_INSUFFISANT'){
                $this->set('messageError', CONST_ERROR_MSG_NIV_VEILLE_INSUFFISANT);
            }
            unset ($_REQUEST['error']);
        }

        // tab rank
        $this->set('onglet', CONST_ONGLET_CRIDONLINE);
    }
    /**
     * Notaire CridOnlineValidation Content Block (AJAX Friendly)
     * Associated template : app/views/notaires/contentcridonlineetape2.php
     *
     * @return void
     */
    public function contentcridonlineetape2()
    {
        // access secured
        $this->cridonlinevalidation();
        $vars = $this->view_vars;
        $vars['is_ajax'] = true;
        $vars['controller'] = $vars['this']; //mandatory due to variable name changes in page-mon-compte.php "this" -> "controller"
        CriRenderView('contentcridonlineetape2', $vars,'notaires');
        die();
    }
    /**
     * Notaire cridonline page
     * Associated template : app/views/notaires/cridonline.php
     *
     * @return void
     */
    public function cridonlinevalidation()
    {
        $this->prepareSecureAccess();
        if (!empty($_GET['level']) && !empty($_GET['price']) && !empty($_GET['crpcen'])) {
            $this->set('level',$_GET['level']);
            $this->set('price',$_GET['price']);
            $this->set('crpcen',$_GET['crpcen']);
        }
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

    public function ajaxnewslettersubscription()
    {
        // init response
        $ret = 'invalidemail';

        // Verify that the nonce is valid.
        if (isset($_REQUEST['token']) && wp_verify_nonce($_REQUEST['token'], 'process_newsletter_nonce') && isset($_REQUEST['email'])) {
            // find the notaire email
            $notaire = $this->model->find_one_by_email_adress($_REQUEST['email']);

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


    public function ajaxVeilleSubscription()
    {
        $ret = 'cgvNotAccepted';
        if ((!empty($_REQUEST['CGV'])) && ($_REQUEST['CGV'] === 'true') ) {
            // Verify that the nonce is valid.
            if (isset($_REQUEST['token']) && wp_verify_nonce($_REQUEST['token'], 'process_cridonline_nonce') && !empty($_REQUEST['crpcen'])) {
                // find the office
                $etude = mvc_model('Etude')->find_one_by_crpcen($_REQUEST['crpcen']);
                if (!empty($etude) && !empty($_REQUEST['level']) && intval($_REQUEST['level']) > $etude->subscription_level && !empty($_REQUEST['price'])) {
                    $start_subscription_date = date('Y-m-d');
                    $end_subscription_date = date('Y-m-d', strtotime('+' . CONST_CRIDONLINE_SUBSCRIPTION_DURATION_DAYS . 'days'));
                    $echeance_subscription_date = date('Y-m-d', strtotime($end_subscription_date .'-'. CONST_CRIDONLINE_ECHEANCE_MONTH . 'month'));
                    $office = array(
                        'Etude' => array(
                            'crpcen' => $_REQUEST['crpcen'],
                            'subscription_level' => $_REQUEST['level'],
                            'start_subscription_date' => $start_subscription_date,
                            'echeance_subscription_date' => $echeance_subscription_date,
                            'end_subscription_date' => $end_subscription_date,
                            'subscription_price' => intval($_REQUEST['price']),
                            'a_transmettre' => CONST_CRIDONLINE_A_TRANSMETTRE_ERP
                        )
                    );
                    if (mvc_model('Etude')->save($office)) {
                        $this->model->sendCridonlineConfirmationMail($etude, $office['Etude']);
                        $ret = 'success';
                    }
                }
            }
        }
        echo json_encode($ret);
        die;
    }


    protected function prepareDashboard()
    {
        $this->prepareSecureAccess();

        // set template vars
        $vars = $this->get_object();
        $this->set_vars($vars);
        return $vars;
    }

    protected function prepareSecureAccess()
    {
        $this->secureAccess();
    }

    protected function prepareProfil()
    {
        $this->prepareSecureAccess();

        if (isset($_POST)
            && !empty($_POST)
            && !empty($this->current_notaire)
        ) {
            global $current_user;
            // newsletter
            if(isset($_POST['disabled'])) {
                $this->model->newsletterSubscription($this->current_notaire);
            }

            // centre d'interets
            if (isset($_POST['matieres'])) {
                //Clean $_POST before
                $data = $this->clean($_POST);
                $this->model->manageInterest($this->current_notaire, $data);
            }
        }
        // set template vars
        $vars = $this->get_object();
        $this->set_vars($vars);
    }
    /**
     * Get questions pending
     *
     * @return mixed
     */
    public function getPending(){
        return $this->model->getPending(Config::$questionPendingStatus);
    }

    /**
     * Get questions
     *
     * @return mixed
     */
    public function getQuestions(){
        $allStatus = Config::$questionPendingStatus;
        $allStatus[] = CONST_QUEST_ANSWERED;
        $this->params['per_page'] = DEFAULT_QUESTION_PER_PAGE;//set number per page
        $this->params['status'] = $allStatus;
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

    /**
     * Notaire Collaborator Content Block (AJAX Friendly)
     * Associated template : app/views/notaires/collaborateur.php
     *
     * @return void
     */
    public function collaborateur()
    {
        // access secured
        $this->prepareSecureAccess();
        //show every member of an office
        $collection = $this->model->listOfficeMembers($this->current_notaire, $this->params);
        $this->set('liste', $collection['objects']);
        $message = '';
        if (isset($_REQUEST['message'])){
            if ($_REQUEST['message'] == 'add'){
                $message = CONST_COLLABORATEUR_ADD_SUCCESS_MSG;
            } elseif ($_REQUEST['message'] == 'modify'){
                $message = CONST_COLLABORATEUR_MODIFY_SUCCESS_MSG;
            } elseif ($_REQUEST['message'] == 'delete'){
                $message = CONST_COLLABORATEUR_DELETE_SUCCESS_MSG;
            } elseif ($_REQUEST['message'] == 'modifyprofil'){
                $message = CONST_PROFIL_MODIFY_SUCCESS_MSG;
            }
            unset ($_REQUEST['message']);
        }
        $this->set('message', $message);
        // tab rank
        $this->set('onglet', CONST_ONGLET_COLLABORATEUR);
    }

    /**
     * Notaire Collaborator Content Block (AJAX Friendly)
     * Associated template : app/views/notaires/contentcollaborateur.php
     *
     * @return void
     */
    public function contentcollaborateur()
    {
        // access secured
        $this->collaborateur();
        $vars = $this->view_vars;
        $vars['is_ajax'] = true;
        $vars['controller'] = $vars['this']; //mandatory due to variable name changes in page-mon-compte.php "this" -> "controller"
        $data = CriRenderView('contentcollaborateur', $vars,'notaires',false);
        $json = array(
            'view' => $data,
        );
        echo json_encode($json);
        die();
    }

    public function gestioncollaborateur(){
        if (!empty($_GET['action']) ) {
            $collaborator = array();
            $collaborator['id'] = empty($_GET['collaborator_id']) ? '' : $_GET['collaborator_id'] ;
            $collaborator['action'] = empty($_GET['action']) ? '' : $_GET['action'] ;
            $collaborator['lastname'] = empty($_GET['collaborator_lastname']) ? '' : $_GET['collaborator_lastname'] ;
            $collaborator['firstname'] = empty($_GET['collaborator_firstname']) ? '' : $_GET['collaborator_firstname'] ;
            $collaborator['phone'] = empty($_GET['collaborator_phone']) ? '' : trim($_GET['collaborator_phone']) ;
            $collaborator['mobilephone'] = empty($_GET['collaborator_mobilephone']) ? '' : trim($_GET['collaborator_mobilephone']) ;
            $collaborator['emailaddress'] = empty($_GET['collaborator_emailaddress']) ? '' : $_GET['collaborator_emailaddress'] ;
            $collaborator['notairefunction'] = empty($_GET['collaborator_notairefunction']) ? '' : $_GET['collaborator_notairefunction'];
            $collaborator['collaboratorfunction'] = empty($_GET['collaborator_collaboratorfunction']) ? '' : $_GET['collaborator_collaboratorfunction'];
            $options = array(
                'conditions' => array(
                    'id' => $collaborator['id']
                )
            );
            $collab   = mvc_model('Notaire')->find_one($options);
            if (!empty($collab)){
                $collaborator['capabilities'] = CriGetCollaboratorRoles($collab);
            }

            if (!in_array($_GET['action'],Config::$collaborateurActions)){
                $collaborator['fax'] = empty($_GET['collaborator_fax']) ? '' : trim($_GET['collaborator_fax']) ;
                $fonctions = array();
            } else {
                $fonctions = Config::$addableFunctions;
            }
            $notaire_functions = $this->tools->getNotaireFunctions($fonctions);
            // set list of notaire functions
            $this->set('notaire_functions', $notaire_functions);

            $collaborateur_functions = $this->tools->getCollaboratorFunctions();
            // set list of collaborator functions
            $this->set('collaborateur_functions', $collaborateur_functions);

            $this->set('collaborator',$collaborator);


            $vars = $this->view_vars;
            $vars['is_ajax'] = true;
            $vars['controller'] = $vars['this'];

            if (in_array($_GET['action'],Config::$collaborateurActions)) {
                $data = CriRenderView('collaborateurajoutpopup', $vars, 'notaires', false);
            } else {
                $data = CriRenderView('contentupdateprofilpopup', $vars, 'notaires', false);
            }

            $json = array(
                'view' => $data,
            );
            echo json_encode($json);
            die();
        }
        if (!empty($_POST['action'])){
            if (isset($_REQUEST['token']) && wp_verify_nonce($_REQUEST['token'], 'process_crud_nonce')) {
                // Clean $_POST before
                $data = $this->tools->clean($_POST);
                // capabilities
                if (isset($data['collaborator_cap_finance']) && $data['collaborator_cap_finance'] == 'true'){
                    $data[CONST_FINANCE_ROLE] = true;
                }
                if (isset($data['collaborator_cap_questionsecrites']) && $data['collaborator_cap_questionsecrites'] == 'true'){
                    $data[CONST_QUESTIONECRITES_ROLE] = true;
                }
                if (isset($data['collaborator_cap_questionstel']) && $data['collaborator_cap_questionstel'] == 'true'){
                    $data[CONST_QUESTIONTELEPHONIQUES_ROLE] = true;
                }
                if (isset($data['collaborator_cap_connaissances']) && $data['collaborator_cap_connaissances'] == 'true'){
                    $data[CONST_CONNAISANCE_ROLE] = true;
                }
                //get current notaire
                $this->current_notaire = $this->model->find_one_by_id_wp_user($this->current_user->ID);
                $action = 'collaborateur';
                switch ($_POST['action']){
                    case CONST_CREATE_USER:
                        $this->addCollaborateur($this->current_notaire,$data);
                        $message='add';
                        break;
                    case CONST_MODIFY_USER:
                        $this->modifyCollaborateur($this->current_notaire,$data);
                        $message='modify';
                        break;
                    case CONST_DELETE_USER:
                        $this->deleteCollaborateur($this->current_notaire,$data);
                        $message='delete';
                        break;
                    case CONST_PROFIL_MODIFY_USER:
                        $this->modifyCollaborateur($this->current_notaire,$data);
                        $message='modifyprofil';
                        $action = 'profil';
                        break;
                }
                $url = mvc_public_url(array('controller' => 'notaires','action' => $action));
                $url.='?message='.$message;
                echo json_encode(array('view' => $url));
                die();
            }
        }
    }


    public function gestionetude(){
        if (!empty($_GET['office_crpcen']) ) {
            $office = array();
            $office['office_crpcen'] = empty($_GET['office_crpcen']) ? '' : trim($_GET['office_crpcen']) ;
            $office['office_name'] = empty($_GET['office_name']) ? '' : trim($_GET['office_name']) ;
            $office['office_address_1'] = empty($_GET['office_address_1']) ? '' : trim($_GET['office_address_1']) ;
            $office['office_address_2'] = empty($_GET['office_address_2']) ? '' : trim($_GET['office_address_2']) ;
            $office['office_address_3'] = empty($_GET['office_address_3']) ? '' : trim($_GET['office_address_3']) ;
            $office['office_postalcode'] = empty($_GET['office_postalcode']) ? '' : trim($_GET['office_postalcode']) ;
            $office['office_city'] = empty($_GET['office_city']) ? '' : trim($_GET['office_city']) ;
            $office['office_email'] = empty($_GET['office_email']) ? '' : trim($_GET['office_email']) ;
            $office['office_phone'] = empty($_GET['office_phone']) ? '' : trim($_GET['office_phone']);
            $office['office_fax'] = empty($_GET['office_fax']) ? '' : trim($_GET['office_fax']);

            $this->set('office',$office);

            $vars = $this->view_vars;
            $vars['is_ajax'] = true;
            $vars['controller'] = $vars['this'];

            $data = CriRenderView('contentupdateetudepopup', $vars, 'notaires', false);

            $json = array(
                'view' => $data,
            );
            echo json_encode($json);
            die();
        }
        if (!empty($_POST['office_crpcen'])){
            if (isset($_REQUEST['token']) && wp_verify_nonce($_REQUEST['token'], 'process_office_crud_nonce')) {
                // Clean $_POST before
                $data = $this->tools->clean($_POST);

                // get current notary data
                $this->current_notaire = $this->model->find_one_by_id_wp_user($this->current_user->ID);
                // maj données d'etude
                if (in_array($this->current_notaire->id_fonction, Config::$allowedNotaryFunction)) {
                    // update profil
                    if (!$this->model->updateOffice($data)){
                        echo json_encode(array('error' => CONST_PROFIL_OFFICE_MODIFY_ERROR_MSG));
                        die();
                    };
                }

                $url = mvc_public_url(array('controller' => 'notaires','action' => 'profil'));
                $url.='?message=modifyoffice';
                echo json_encode(array('view' => $url));
                die();
            }
        }
    }

    public function gestionPassword (){
        $error = CONST_PROFIL_PASSWORD_ERROR_MSG;
        if (isset($_REQUEST['token']) && wp_verify_nonce($_REQUEST['token'], 'process_password_nonce') && !empty($_POST['email'])) {
            $data = $this->tools->clean($_POST);
            $notaire = CriNotaireData();
            if (in_array($notaire->id_fonction, Config::$allowedNotaryFunction)) {
                /**
                 * Renouvellement mot de passe :
                 * - notaire connecté dispose d'une adresse email perso
                 * - seuls les notaires de fonctions 1, 2, 3, 6, 7, 8, 9, 10 peuvent accéder à la fonction
                 * @see \Config::$allowedNotaryFunction
                 */
                if (!empty($notaire->email_adress)
                    && filter_var($notaire->email_adress, FILTER_VALIDATE_EMAIL)
                    && $data['email'] == $data['email_validation']
                    && $data['email'] == $notaire->email_adress
                ) {
                    $this->model->resetPwd($notaire->id);
                    $url = mvc_public_url(array('controller' => 'notaires','action' => 'profil'));
                    $url.='?message=modifypassword';
                    echo json_encode(array('view' => $url));
                    die();
                } else {
                    $error = CONST_PROFIL_PASSWORD_EMAIL_ERROR_MSG;
                }
            }
        }
        echo json_encode(array('error' => $error));
        die();
    }

    /**
     * Add new Notaire Collaborator Content Block (AJAX Friendly)
     * Associated template : app/views/notaires/collaborateurajoutpopup.php
     * @param object Notaire $current_notaire
     * @param array $data data of collaborator to modify
     *
     * @return void
     */
    public function addCollaborateur($current_notaire,$data){
        if(!$this->model->manageCollaborator($current_notaire, $data)){
            echo json_encode(array('error' => CONST_COLLABORATEUR_ADD_ERROR_MSG));
            die();
        };
    }
    /**
     * Modify Notaire Collaborator Content Block (AJAX Friendly)
     * Associated template : app/views/notaires/collaborateurajoutpopup.php
     * @param object Notaire $current_notaire
     * @param array $data data of collaborator to modify
     *
     * @return void
     */
    public function modifyCollaborateur($current_notaire,$data){
        if(!$this->model->manageCollaborator($current_notaire, $data)){
            echo json_encode(array('error' => CONST_COLLABORATEUR_MODIFY_ERROR_MSG));
            die();
        };
    }

    /**
     * Delete Notaire Collaborator Content Block (AJAX Friendly)
     * Associated template : app/views/notaires/deletecollaborateur.php
     * @param object Notaire $current_notaire
     * @param array $data id of collaborator to delete
     *
     * @return void
     */
    public function deleteCollaborateur($current_notaire,$data)
    {
        $collaborator_id = $data['collaborator_id'];
        if (!empty ($collaborator_id)) {
            // check if user can manage collaborator
            if (!in_array($current_notaire->id_fonction, Config::$allowedNotaryFunction)
                || !$this->tools->isSameOffice($collaborator_id, $current_notaire)
            ) {
                // redirect to profil page
                $this->redirect(mvc_public_url(
                        array(
                            'controller' => 'notaires',
                            'action' => 'profil'
                        )
                    )
                );
            }
            if (!$this->model->deleteCollaborator($collaborator_id)) {
                $json = array('error' => CONST_COLLABORATEUR_DELETE_ERROR_MSG);
                echo json_encode($json);
                die();
            }
        }
    }
}
