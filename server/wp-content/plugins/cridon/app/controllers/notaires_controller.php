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
     * @param array $role -> role needed to access page
     *
     * @return void
     */
    protected function secureAccess($role = array())
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
                  && !$this->model->userCanAccessSensitiveInfo($role)
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
        $vars = $this->model->traitement_data_solde($vars);
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
        $this->renderView('contentdashboard', true);
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
        $this->renderView('contentquestions', true);
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
        $this->renderView('contentprofil', true);
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
        $this->cridonlineData();
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
        $this->renderView('contentfacturation', true);
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
        $this->prepareSecureAccess(CONST_FINANCE_ROLE);
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
        $this->renderView('contentcridonline', true);
        die();
    }
    /**
     * Notaire CridOnline PROMO Content Block (AJAX Friendly)
     * Associated template : app/views/notaires/contentcridonlinepromo.php
     *
     * @return void
     */
    public function contentcridonlinepromo()
    {
        // access secured
        $this->cridonline();
        $this->renderView('contentcridonlinepromo', true);
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
        $this->prepareSecureAccess(CONST_CRIDONLINESUBSCRIPTION_ROLE);
        $this->cridonlineData();
        // tab rank
        $this->set('onglet', CONST_ONGLET_CRIDONLINE);
    }

    protected function cridonlineData()
    {
        $notaire = CriNotaireData();
        $this->set('notaire', $notaire);

        $options = array('conditions' => array('crpcen' => $notaire->crpcen));
        /** @var $entite Entite*/
        $entite   = mvc_model('Entite')->find_one($options);
        $subscriptionInfos = mvc_model('Entite')->getRelatedPrices($entite);
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
        $this->renderView('contentcridonlineetape2', true);
        die();
    }
    /**
     * Notaire CridOnlineValidation Promo Content Block (AJAX Friendly)
     * Associated template : app/views/notaires/contentcridonlineetape2.php
     *
     * @return void
     */
    public function contentcridonlineetape2promo()
    {
        // access secured
        $this->cridonlinevalidation();
        $this->set('promo',$_GET['promo']);
        $this->renderView('contentcridonlineetape2promo', true);
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
        $this->prepareSecureAccess(CONST_CRIDONLINESUBSCRIPTION_ROLE);
        if (!empty($_GET['level']) && !empty($_GET['price'])) {
            $this->set('level',$_GET['level']);
            $this->set('price',$_GET['price']);
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
        $data = '';

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

                // Generate new page
                $this->profil();
                $data = $this->renderView('contentprofil', false);
            }
        }

        $json = array(
            'returnValue' => $ret,
            'view' => $data
        );

        echo json_encode($json, JSON_HEX_QUOT | JSON_HEX_TAG);

        die;
    }


    public function ajaxVeilleSubscription()
    {
        $notaire = CriNotaireData();
        if (!$this->validateSubscriptionData($_REQUEST,$notaire)){
            echo json_encode(array('error' => CONST_CRIDONLINE_CGV_ERROR_MSG));
            die();
        }
        // find the office
        $entite = mvc_model('Entite')->find_one_by_crpcen($notaire->crpcen);
        if (!empty($entite) && !empty($_REQUEST['level']) && intval($_REQUEST['level']) > $entite->subscription_level) {
            $subscriptionInfos = mvc_model('Entite')->getRelatedPrices($entite);
            $subscription_price = $subscriptionInfos[$_REQUEST['level']];
            $start_subscription_date = date('Y-m-d');
            $end_subscription_date = date('Y-m-d', strtotime('+' . CONST_CRIDONLINE_SUBSCRIPTION_DURATION_DAYS . 'days'));
            $echeance_subscription_date = date('Y-m-d', strtotime($end_subscription_date .'-'. CONST_CRIDONLINE_ECHEANCE_MONTH . 'month'));
            $office = array(
                'Entite' => array(
                    'crpcen'                     => $notaire->crpcen,
                    'subscription_level'         => $_REQUEST['level'],
                    'start_subscription_date'    => $start_subscription_date,
                    'echeance_subscription_date' => $echeance_subscription_date,
                    'end_subscription_date'      => $end_subscription_date,
                    'subscription_price'         => $subscription_price,
                    'id_sepa'                    => $this->calculateSepaId($notaire,$entite),
                    'a_transmettre'              => CONST_CRIDONLINE_A_TRANSMETTRE_ERP
                )
            );
            if (mvc_model('Entite')->save($office)) {
                $this->model->sendCridonlineConfirmationMail($notaire, $entite, $office['Entite'],$_REQUEST['B2B_B2C']);

                $this->set('B2B_B2C',$_REQUEST['B2B_B2C']);

                $data = $this->renderView('contentcridonlinevalidationpopup', false);

                $json = array(
                    'view' => $data,
                );
                echo json_encode($json, JSON_HEX_QUOT | JSON_HEX_TAG);
                die();
            }
        }
        echo json_encode(array('error' => CONST_CRIDONLINE_CGV_ERROR_MSG));
        die();
    }

    protected function validateSubscriptionData($request,$notaire){
        //Validate level
        if (empty($request['level']) || !in_array($request['level'],Config::$cridonlineLevels)){
            return false;
        }
        //Validate role
        if (!in_array(CONST_CRIDONLINESUBSCRIPTION_ROLE,CriGetCollaboratorRoles($notaire))){
            return false;
        }
        //Validate CGV
        if (!(!empty($request['CGV']) && ($request['CGV'] === 'true') )) {
            return false;
        }
        // Verify that the nonce is valid.
        if (!(isset($request['token']) && wp_verify_nonce($request['token'], 'process_cridonline_nonce'))) {
            return false;
        }
        return true;
    }

    protected function calculateSepaId($notaire,$entite){
        $start = 'CRI'.$notaire->client_number;
        if (!empty($entite->id_sepa)){
            return $start.(sprintf('%05d',substr($entite->id_sepa,-5) + 1));
        } else {
            return $start.'00001';
        }
    }

    // Function only used for promo time
    public function ajaxVeilleSubscriptionPromo()
    {
        $notaire = CriNotaireData();
        if (!$this->validateSubscriptionDataPromo($_REQUEST,$notaire)){
            echo json_encode(array('error' => CONST_CRIDONLINE_CGV_ERROR_MSG));
            die();
        }
        $entite = mvc_model('Entite')->find_one_by_crpcen($notaire->crpcen);
        if (!empty($entite) && intval($_REQUEST['level']) > $entite->subscription_level) {
            if ($_REQUEST['promo'] == CONST_PROMO_CHOC){
                $subscriptionInfos = mvc_model('Entite')->getRelatedPrices($entite);
                $subscription_price = $subscriptionInfos[$_REQUEST['level']];
                $start_subscription_date = CONST_START_SUBSCRIPTION_PROMO_CHOC;
                $end_subscription_date = CONST_END_SUBSCRIPTION_PROMO_CHOC;
                $echeance_subscription_date = CONST_ECHEANCE_SUBSCRIPTION_PROMO_CHOC;
                $offre_promo = CONST_PROMO_CHOC;
            } elseif ($_REQUEST['promo'] == CONST_PROMO_PRIVILEGE){
                $subscriptionInfos = mvc_model('Entite')->getRelatedPrices($entite);
                $subscription_price = $subscriptionInfos[CONST_CRIDONLINE_LEVEL_2];
                $start_subscription_date = date('Y-m-d');
                $end_subscription_date = date('Y-m-d', strtotime('+' . CONST_CRIDONLINE_SUBSCRIPTION_DURATION_DAYS . 'days'));
                $echeance_subscription_date = date('Y-m-d', strtotime($end_subscription_date .'-'. CONST_CRIDONLINE_ECHEANCE_MONTH . 'month'));
                $offre_promo = CONST_PROMO_PRIVILEGE;
            } else {
                echo json_encode(array('error' => CONST_CRIDONLINE_CGV_ERROR_MSG));
                die();
            }
            $office = array(
                'Entite' => array(
                    'crpcen'                     => $notaire->crpcen,
                    'subscription_level'         => $_REQUEST['level'],
                    'start_subscription_date'    => $start_subscription_date,
                    'echeance_subscription_date' => $echeance_subscription_date,
                    'end_subscription_date'      => $end_subscription_date,
                    'subscription_price'         => $subscription_price,
                    'id_sepa'                    => $this->calculateSepaId($notaire,$entite),
                    'offre_promo'                => $offre_promo,
                    'a_transmettre'              => CONST_CRIDONLINE_A_TRANSMETTRE_ERP
                )
            );
            if (mvc_model('Entite')->save($office)) {
                $this->model->sendCridonlineConfirmationMail($notaire, $entite, $office['Entite'],$_REQUEST['B2B_B2C']);

                $this->set('B2B_B2C',$_REQUEST['B2B_B2C']);
                $data = $this->renderView('contentcridonlinevalidationpopup', false);

                $json = array(
                    'view' => $data,
                );
                echo json_encode($json, JSON_HEX_QUOT | JSON_HEX_TAG);
                die();
            }
        }
        echo json_encode(array('error' => CONST_CRIDONLINE_CGV_ERROR_MSG));
        die();
    }

    protected function validateSubscriptionDataPromo($request,$notaire){

        //Validate promo
        if (!isPromoActive()){
            return false;
        }
        if (!in_array($request['promo'],array(CONST_PROMO_CHOC,CONST_PROMO_PRIVILEGE))){
            return false;
        }
        return $this->validateSubscriptionData($request,$notaire);
    }

    protected function prepareDashboard()
    {
        $this->prepareSecureAccess();

        // set template vars
        $vars = $this->get_object();
        $this->set_vars($vars);
        return $vars;
    }

    protected function prepareSecureAccess($role = array())
    {
        $this->secureAccess($role);
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
        $this->prepareSecureAccess(CONST_COLLABORATEUR_TAB_ROLE);
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
        //show every member of an office
        $collection = $this->model->listOfficeMembers($this->current_notaire, $this->params);
        $this->set('liste', $collection['objects']);
        // pagination
        $this->set_pagination($collection);
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
        $data = $this->renderView('contentcollaborateur', false);
        $json = array(
            'view' => $data,
        );
        echo json_encode($json, JSON_HEX_QUOT | JSON_HEX_TAG);
        die();
    }

    public function gestioncollaborateur(){
        if (!empty($_GET['action']) ) {
            $collaborator = array();
            $collaborator['action'] = $_GET['action'];
            if (!empty($_GET['collaborator_id'])){
                $collaborator['id'] = $_GET['collaborator_id'];
                $options = array(
                    'conditions' => array(
                        'id' => $collaborator['id']
                    )
                );
                $collab   = mvc_model('Notaire')->find_one($options);
                if (!empty($collab)){
                    $collaborator['capabilities'] = CriGetCollaboratorRoles($collab);
                    $collaborator['lastname'] = empty($collab->last_name) ? '' : $collab->last_name ;
                    $collaborator['firstname'] = empty($collab->first_name) ? '' : $collab->first_name ;
                    $collaborator['phone'] = empty($collab->tel) ? '' : trim($collab->tel) ;
                    $collaborator['mobilephone'] = empty($collab->tel_portable) ? '' : trim($collab->tel_portable) ;
                    $collaborator['emailaddress'] = empty($collab->email_adress) ? '' : trim($collab->email_adress) ;
                    $collaborator['notairefunction'] = empty($collab->id_fonction) ? '' : $collab->id_fonction;
                    $collaborator['collaboratorfunction'] = empty($collab->id_fonction_collaborateur) ? '' : $collab->id_fonction_collaborateur;
                    $collaborator['fax'] = empty($collab->fax) ? '' : trim($collab->fax) ;
                }
            }

            if (in_array($_GET['action'],Config::$collaborateurActions)){
                // Only show functions that are addable by a notaire (notaire salarie(e) + collab)
                $fonctions = Config::$addableFunctions;
            } else {
                // Used to display the label of the function
                $fonctions = $collaborator['notairefunction'];
            }
            $notaire_functions = $this->tools->getNotaireFunctions($fonctions);
            // set list of notaire functions
            $this->set('notaire_functions', $notaire_functions);

            $collaborateur_functions = $this->tools->getCollaboratorFunctions();
            // set list of collaborator functions
            $this->set('collaborateur_functions', $collaborateur_functions);

            $this->set('collaborator',$collaborator);

            if (in_array($_GET['action'],Config::$collaborateurActions)) {
                $data = $this->renderView('collaborateurajoutpopup', false);
            } else {
                $data = $this->renderView('contentupdateprofilpopup', false);
            }

            $json = array(
                'view' => $data,
            );
            echo json_encode($json, JSON_HEX_QUOT | JSON_HEX_TAG);
            die();
        }
        if (!empty($_POST['action'])){
            if (isset($_REQUEST['token']) && wp_verify_nonce($_REQUEST['token'], 'process_crud_nonce')) {
                // Clean $_POST before
                $data = $this->tools->clean($_POST);
                // capabilities
                if ($_POST['action'] == CONST_CREATE_USER || $_POST['action'] == CONST_MODIFY_USER) {
                    if (isset($data['collaborator_cap_finance']) && $data['collaborator_cap_finance'] == 'true') {
                        $data[CONST_FINANCE_ROLE] = true;
                    }
                    if (isset($data['collaborator_cap_questionsecrites']) && $data['collaborator_cap_questionsecrites'] == 'true') {
                        $data[CONST_QUESTIONECRITES_ROLE] = true;
                    }
                    if (isset($data['collaborator_cap_questionstel']) && $data['collaborator_cap_questionstel'] == 'true') {
                        $data[CONST_QUESTIONTELEPHONIQUES_ROLE] = true;
                    }
                    if (isset($data['collaborator_cap_connaissances']) && $data['collaborator_cap_connaissances'] == 'true') {
                        $data[CONST_CONNAISANCE_ROLE] = true;
                    }
                    if (isset($data['collaborator_cap_modifyoffice']) && $data['collaborator_cap_modifyoffice'] == 'true') {
                        $data[CONST_MODIFYOFFICE_ROLE] = true;
                    }
                    if (isset($data['collaborator_cap_cridonlinesubscription']) && $data['collaborator_cap_cridonlinesubscription'] == 'true') {
                        $data[CONST_CRIDONLINESUBSCRIPTION_ROLE] = true;
                    }
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
                        $this->modifyCollaborateur($this->current_notaire,$data, true);
                        $message='modify';
                        break;
                    case CONST_DELETE_USER:
                        $this->deleteCollaborateur($this->current_notaire,$data);
                        $message='delete';
                        break;
                    case CONST_PROFIL_MODIFY_USER:
                        $this->modifyCollaborateur($this->current_notaire,$data, false);
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


    public function gestionentite(){
        if (empty($_POST['office_crpcen']) ) {
            $notaire = CriNotaireData();
            $office['office_crpcen'] = $notaire->crpcen;
            $options = array(
                'conditions' => array(
                    'crpcen' => $office['office_crpcen']
                )
            );
            $entite   = mvc_model('Entite')->find_one($options);
            $office['office_name'] = empty($entite->office_name) ? '' : trim($entite->office_name) ;
            $office['office_address_1'] = empty($entite->adress_1) ? '' : trim($entite->adress_1) ;
            $office['office_address_2'] = empty($entite->adress_2) ? '' : trim($entite->adress_2) ;
            $office['office_address_3'] = empty($entite->adress_3) ? '' : trim($entite->adress_3) ;
            $office['office_postalcode'] = empty($entite->cp) ? '' : trim($entite->cp) ;
            $office['office_city'] = empty($entite->city) ? '' : trim($entite->city) ;
            $office['office_email'] = empty($entite->office_email_adress_1) ? '' : trim($entite->office_email_adress_1) ;
            $office['office_phone'] = empty($entite->tel) ? '' : trim($entite->tel);
            $office['office_fax'] = empty($entite->fax) ? '' : trim($entite->fax);

            $this->set('office',$office);

            $view = $this->renderView('contentupdateentitepopup', false);

            $json = array(
                'view' => $view,
            );
            echo json_encode($json, JSON_HEX_QUOT | JSON_HEX_TAG);
            die();
        } else {
            if (isset($_REQUEST['token']) && wp_verify_nonce($_REQUEST['token'], 'process_office_crud_nonce')) {
                // Clean $_POST before
                $data = $this->tools->clean($_POST);

                // get current notary data
                $this->current_notaire = $this->model->find_one_by_id_wp_user($this->current_user->ID);
                // maj données d'entite
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
        if (isset($_REQUEST['token']) && wp_verify_nonce($_REQUEST['token'], 'process_password_nonce') && !empty($_POST['email'])) {
            $data = $this->tools->clean($_POST);
            $notaire = CriNotaireData();

            if (empty($notaire->email_adress) || !filter_var($notaire->email_adress, FILTER_VALIDATE_EMAIL)){
                echo json_encode(array('error' => CONST_PROFIL_PASSWORD_MISSING_EMAIL_ERROR_MSG));
                die();
            }
            if ($data['email'] != $data['email_validation']){
                echo json_encode(array('error' => CONST_PROFIL_PASSWORD_DIFFERENT_EMAIL_ERROR_MSG));
                die();
            }
            if ($data['email'] != $notaire->email_adress){
                echo json_encode(array('error' => CONST_PROFIL_PASSWORD_DIFFERENT_PROFIL_EMAIL_ERROR_MSG));
                die();
            }
            $this->model->resetPwd($notaire->id);
            $url = mvc_public_url(array('controller' => 'notaires','action' => 'profil'));
            $url.='?message=modifypassword';
            echo json_encode(array('view' => $url));
            die();
        } else {
            echo json_encode(array('error' => CONST_PROFIL_PASSWORD_ERROR_MSG));
            die();
        }
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
     * @param $roles bool if roles has to be modified
     *
     * @return void
     */
    public function modifyCollaborateur($current_notaire,$data, $roles){
        if(!$this->model->manageCollaborator($current_notaire, $data, $roles)){
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

    /**
     * Notaire Mes Factures Content Block (AJAX Friendly)
     * Associated template : app/views/notaires/contentmesfactures.php
     *
     * @return void
     */
    public function contentmesfactures()
    {
        // access secured
        $this->mesfactures();
        $this->renderView('contentmesfactures', true);
        die();
    }
    /**
     * Notaire Mes factures page
     * Associated template : app/views/notaires/mesfactures.php
     *
     * @return void
     */
    public function mesfactures()
    {
        $this->prepareSecureAccess(CONST_FINANCE_ROLE);
        $notaire = CriNotaireData();
        $this->set('notaire',$notaire);
        $factures = $this->model->getFactures($notaire, CONST_DOC_TYPE_FACTURE);
        usort($factures,array($this,'factureSort'));
        $this->set('factures', $factures);

        // tab rank
        $this->set('onglet', CONST_ONGLET_MES_FACTURES);
    }

    /**
     * Fonction permettant de trier les factures en front
     * Ordre demandé : Trier par année et mois décroissant puis par type_facture : Cotisation générale ; crionline puis services ponctuels
     *
     * @param $factureA
     * @param $factureB
     * @return int
     */
    protected function factureSort($factureA, $factureB){
        // On tri dans un premier temps sur l'année (du plus grand au plus petit)
        if ($factureA->year < $factureB->year){return -1;}
        if ($factureA->year > $factureB->year){return +1;}
        // A année égale, on tri sur le type de facture : 'cg' et 'cs' en premier puis 'cridonline' puis tous les autres
        $typeA = strtolower($factureA->type_facture);
        $typeB = strtolower($factureB->type_facture);
        if (in_array($typeA, array('cg', 'cs')) && !in_array($typeB, array('cg', 'cs'))) {return -1;}
        if (in_array($typeB, array('cg', 'cs')) && !in_array($typeA, array('cg', 'cs'))) {return +1;}
        if (in_array($typeA, array('cg', 'cs')) &&  in_array($typeB, array('cg', 'cs'))) {return 0;}
        if ($typeA === 'cridonline' && $typeB !== 'cridonline'){return -1;}
        if ($typeB === 'cridonline' && $typeA !== 'cridonline'){return +1;}
        if ($typeA === 'cridonline' && $typeB === 'cridonline'){return 0;}
        return 0;
    }

    /**
     * Notaire Mes Relevés Content Block (AJAX Friendly)
     * Associated template : app/views/notaires/contentmesreleves.php
     *
     * @return void
     */
    public function contentmesreleves()
    {
        // access secured
        $this->mesreleves();
        $this->renderView('contentmesreleves', true);
        die();
    }
    /**
     * Notaire Mes releves page
     * Associated template : app/views/notaires/mesreleves.php
     *
     * @return void
     */
    public function mesreleves()
    {
        $this->prepareSecureAccess(CONST_FINANCE_ROLE);
        $notaire = CriNotaireData();
        $this->set('notaire',$notaire);
        $releves = $this->model->getFactures($notaire, CONST_DOC_TYPE_RELEVECONSO);
        $this->set('releves', $releves);

        // tab rank
        $this->set('onglet', CONST_ONGLET_MES_RELEVES);
    }

    /**
     * render a view
     * @param array $view name of view to render
     * @param boolean $echo echo the view (true) / stock it in a var (false)
     *
     * @return string view
     */
    protected function renderView ($view, $echo){
        $vars = $this->view_vars;
        $vars['is_ajax'] = true;
        $vars['controller'] = $vars['this'];
        return CriRenderView($view, $vars, 'notaires', $echo);
    }
}
