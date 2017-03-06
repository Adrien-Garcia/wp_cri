<?php

/**
 * Class FormationsController
 */
require_once 'base_actu_controller.php';
class FormationsController extends BaseActuController
{
    /** @var DateTime : first day oh the requested month for calendar view */
    protected $firstDayOfMonth;

    /** @var DateTime : last day oh the requested month for calendar view */
    protected $lastDayOfMonth;

    /**
     * Action Formations futures ( >= date du jour)
     */
    public function index()
    {
        // Sessions futures : triées de la plus proche à la plus lointaine
        $sessions = $this->getSessions('ASC', true);
        $this->set('sessionsFutures', $sessions);
        $this->set('formations', $this->getFormations($sessions));
    }

    /**
     * Action Formations passées ( < date du jour)
     */
    public function past()
    {
        // Sessions passées : triées de la plus récente à la plus ancienne
        $sessions = $this->getSessions('DESC', false );
        $this->set('sessionsPassees', $sessions);
        $this->set('formations', $this->getFormations($sessions));
    }

    /**
     * Retrieve all sessions
     *
     * @param $order : sort order
     * @param $future : future or past sessions ?
     * @return $sessions object
     */
    public function getSessions($order, $future = true) {
        $this->process_params_for_search();
        $params = $this->params;
        $params['order']      = 'date '.$order;

        $sign = ($future ? ' >= ' : ' < ');
        $params['conditions'] = array('date'.$sign => date('Y-m-d'));

        $modelSession = new Session();
        $collection = $modelSession->paginate($params);
        $sessions = $collection['objects'];

        $this->set_pagination($collection);
        return $sessions;
    }

    /**
     * Retrieve formations bound to $sessions as an array :
     * (key) id_formation => (value) Formation
     *
     * @param object $sessions
     * @return array $allFormations
     */
    public function getFormations($sessions) {
        $ids = array();
        foreach($sessions as $session){
            $ids [] = $session->id_formation;
        }
        $formations = $this->model->find(array('conditions' => array('f.id' => $ids)));
        $allFormations = array();
        foreach($formations as $formation){
            $allFormations [$formation->id] = $formation;
        }
        return $allFormations;
    }

    public function show(){
        $params = $this->params;
        parent::show();
        $formation = $this->object;
        $highlight = false;
        if (!empty($formation->id)) {
            $options = array(
                'conditions' => array(
                    'id_formation' => $formation->id,
                    'date >= ' => date('Y-m-d')
                ),
                'order' => 'date asc'
            );
            $sessions = mvc_model('Session')->find($options);

            // On récupère les organismes dont dépends l'étude
            $organismesAssociatedToEntite = array();
            if (!empty($notaire = CriNotaireData())){
                $modelEntite = new Entite();
                $organismesAssociatedToEntite = $modelEntite->getOrganismesAssociatedToEntite($notaire->crpcen);
            }
            $highlight = reset($sessions);
            foreach($sessions as $key => $session){
                $data = $this->addContactAction($session,$organismesAssociatedToEntite, false);
                $sessions[$key]->action = $data ['action'];
                $sessions[$key]->action_label = $data ['action_label'];
                $sessions[$key]->contact_organisme = $data ['contact_organisme'];
                if (!empty($params['sessionid']) && $sessions[$key]->id === $params['sessionid']) {
                    $highlight = $sessions[$key];
                }
            }
            // Pass data to the single-formation view
            $this->set('highlight', $highlight);
            $this->set('sessions', $sessions);
        }
    }

    public function calendar()
    {
        $params = $this->params;

        $matieres = mvc_model('Matiere')->find(array(
            'conditions' => array(
                'displayed' => 1,
            )
        ));

        $matches = array();
        $month = date('m');
        $year = date('Y');

        if (preg_match('/^(\d{2})(-)(\d{4})$/', $params['id'], $matches) ) {
            $month = !empty($params['id']) ? $matches[1] : date('m');
            $year = !empty($params['id']) ? $matches[3] : date('Y');
        } else if (preg_match('/^(\d{4})(-)(\d{2})$/', $params['id'], $matches)) {
            $month = !empty($params['id']) ? $matches[3] : date('m');
            $year = !empty($params['id']) ? $matches[1] : date('Y');
        }

        $calendar = $this->_generate_calendar_array($month, $year);

        $calendar = $this->_fill_calendar_data($calendar);

        $prev_month = ($month-1) >= 1 ? $month-1 : 12;
        $prev_month = ($prev_month < 10 ? '0'.strval($prev_month) : strval($prev_month));
        $next_month = ($month+1) <= 12 ? $month+1 : 1;
        $next_month = ($next_month < 10 ? '0'.strval($next_month) : strval($next_month));

        $data = array(
            'month' => $month,
            'year' => $year,
            'calendar' => $calendar,
            'prev_month' => array(
                'month' => $prev_month,
                'year' => strval(($month-1) >= 1 ? $year : $year-1),
            ),
            'next_month' => array(
                'month' => $next_month,
                'year' => strval(($month+1) <= 12 ? $year : $year+1),
            ),
            'matieres' => $matieres,
        );

        $this->set('data', $data);

    }

    protected function _generate_calendar_array($month = null, $year = null) {
        $month = (!empty($month) && intval($month) <= 12 && intval($month) > 0 ) ? $month : date('m');
        $year = (!empty($year) && intval($year) > 1970) ? $year : date('Y');

        $tmpmonth = DateTime::createFromFormat('!m', $month);
        $tmpmonth = $tmpmonth->format('F');

        $firstdayofmonth = new DateTime('first day of '. $tmpmonth . ' ' . $year);
        $daytostartofweek = intval($firstdayofmonth->format('N')) -1;
        $firstday = $firstdayofmonth->modify('-'. $daytostartofweek .' days');
        $this->firstDayOfMonth = clone $firstday;

        $lastdayofmonth = new DateTime('last day of '. $tmpmonth . ' ' . $year);
        $daytoendofweek = 7 - intval($lastdayofmonth->format('N'));
        $lastday = $lastdayofmonth->modify('+'. $daytoendofweek .' days');
        $this->lastDayOfMonth = clone $lastday;

        $calendar = array();

        $date = $firstday;
        $today = strtotime('today midnight');
        while ($lastday->getTimestamp() >= $date->getTimestamp()) {
            $calendar[$date->format('Y-m-d')] = array(
                'date' => clone $date,
                'today' => $date->getTimestamp() == $today,
                'in_month' => $date->format('m') == $month,
            );
            $date->modify('+1 day');
        }
        return $calendar;
    }

    /**
     * $calendar = array(
     *   'yyyymmdd' => array(
     *     'date' => Datetime
     *     'today' => bool
     *     'event' => (optional) Name for the event of the day
     *     'sessions' => array(
     *       array(
     *         'name' => string
     *         'short_name' => string
     *         'matiere' => Matiere|MvcObject
     *         'time' => string
     *         'url' => string
     *         'action' => string : URL
     *         'action_label' => string
     *         'details' => string : HTML content
     *       )
     *     )
     *   )
     * )
     *
     * @param $calendar array : Content corresponds to the calendar view
     * @return array : The very same calendar filled with sessions values
     * @throws Exception
     */
    protected function _fill_calendar_data($calendar) {

        //Ajout des sessions de formations au calendrier
        $modelSession = new Session();
        $sessions = $modelSession->find(array(
            'conditions' => array(
                'AND' => array(
                    'Session.date >= ' => $this->firstDayOfMonth->format('Y-m-d'),
                    'Session.date <= ' => $this->lastDayOfMonth->format('Y-m-d')
                )
            ),
            'joins' => array(
                'Formation',
                'Entite'
            ),
            'order' => 'Session.date ASC',
        ));

        // As current ORM does not handle multiple JOIN
        $formations = $this->model->find(array(
            'joins' => array(
                'Post',
                'Matiere',
            ),
        ));
        $formations = assocToKeyVal($formations, 'id');

        // On récupère les organismes dont dépends l'étude
        $organismesAssociatedToEntite = array();
        if (!empty($notaire = CriNotaireData())){
            $modelEntite = new Entite();
            $organismesAssociatedToEntite = $modelEntite->getOrganismesAssociatedToEntite($notaire->crpcen);
        }

        foreach ($sessions as $session) {
            $key = $session->date;
            if (!isset($calendar[$key])) {
                throw new OutOfBoundsException(sprintf('Key %s not found for current calendar', $key));
            }
            $formation = $formations[$session->id_formation];
            $before_today = DateTime::createFromFormat('Y-m-d', $session->date)->getTimestamp() < time();
            $urlOptions = array(
                'controller' => 'formations',
                'action'     => 'show',
                'id'         => $session->id_formation
            );
            $lineSession = array(
                'name'       => $formation->post->post_title,
                'short_name' => $formation->short_name,
                'matiere'    => $formation->matiere,
                'time'       => $session->timetable,
                'url'        => MvcRouter::public_url($urlOptions),
                'id'         => $session->id
            );
            if (!$before_today) {
                $lineSession['url'] .= '?'.http_build_query(array('sessionid' => $session->id));
            }
            $data = $this->addContactAction($session, $organismesAssociatedToEntite, $before_today);

            $lineSession ['action']         = $data ['action'];
            $lineSession ['action_label']   = $data ['action_label'];
            $lineSession ['organisme']           = $data['organisme'];
            $lineSession ['contact_organisme']   = $data['contact_organisme'];
            $calendar[$key]['sessions'][] = $lineSession;
        }

        //Ajout des évènements au calendrier
        $modelEvenement = new Evenement();
        $evenements = $modelEvenement->find(array(
            'conditions' => array(
                'AND' => array(
                    'Evenement.date >= ' => $this->firstDayOfMonth->format('Y-m-d'),
                    'Evenement.date <= ' => $this->lastDayOfMonth->format('Y-m-d')
                )
            ),
            'order' => 'Evenement.date ASC',
        ));

        foreach($evenements as $evenement){
            $key = $evenement->date;
            if (!isset($calendar[$key])) {
                throw new OutOfBoundsException(sprintf('Key %s not found for current calendar', $key));
            }
            if (!empty($evenement->name)){
                $calendar[$key]['event'] = $evenement->name;
            }
        }

        return $calendar;
    }

    /**
     * Will provide session line in calendar with information concerning subscription or contact
     * @param $session Session : the session with all data
     * @param $organismesAssociatedToEntite : every organism associated to current entite
     * @param $remove_actions boolean : If true remove the actions
     * @return array
     */
    protected function addContactAction($session, $organismesAssociatedToEntite, $remove_actions = true)
    {
        $data ['action'] = $data ['action_label'] =  $data['details'] = '';
        $data ['organisme'] = $session->entite;
        $data ['contact_organisme'] = false;
        // Pour les différents cas ; se reporter à goo.gl/0fHVxB
        if (CriIsNotaire() && in_array(CriNotaireData()->id_fonction, Config::$allowedNotaryFunction) ) { // Line 2 (logged in is notaire)
            // L'étude dépend-t-elle d'un organisme ?
            $entiteIsAssociatedToOrganisme = false;
            foreach ($organismesAssociatedToEntite as $organisme) {
                if ($session->id_organisme == $organisme->id) {
                    $entiteIsAssociatedToOrganisme = true;
                    break;
                }
            }

            if ($session->entite->is_cridon) { // Cell B2 (préinscription)
                $urlOptions = array(
                    'controller' => 'formations',
                    'action'     => 'preinscription',
                    'id'         => $session->id
                );
                $data ['action'] = MvcRouter::public_url($urlOptions);
                $data ['action_label'] = 'Se pré-inscrire';
            } else if ($entiteIsAssociatedToOrganisme) { // Cell C2 (informations contact)
                $data ['contact_organisme'] = true;
            } else { // Cell D2 (contact Cridon)
                $urlOptions = array(
                    'controller' => 'formations',
                    'action'     => 'demande',
                    'id'         => $session->id_formation
                );
                $data ['action'] = MvcRouter::public_url($urlOptions);
                $data ['action_label'] = 'Contacter le CRIDON LYON';
            }

        } else if (CriIsNotaire()) { // Line 3 (logged in not notaire (collab...))
            // DO NOTHING
        } else if (!is_user_logged_in()){ // Line 4 (not logged in)
            $error_code = "PROTECTED_CONTENT";
            $data ['action'] = "?openLogin=1&messageLogin=" . $error_code . "&requestUrl=" . urlencode($_SERVER['REQUEST_URI']);

            $data ['action_label'] = 'Se former';
        }

        if ($remove_actions) {
            $data ['action'] = false;
            $data ['action_label'] = false;
        }
        return $data;
    }

    protected function _validateFormulaire ($params, $type)
    {
        $element = false;
        $error = false;
        $model_session = mvc_model('Session');
        $model_formation = mvc_model('Formation');
        /**
         * @var $model Formation
         */
        switch ($type) {
            case CONST_FORMATION_PREINSCRIPTION :
                $model = $model_session;
                break;
            case CONST_FORMATION_DEMANDE :
                $model = $model_formation;
                break;
            default :
            case CONST_FORMATION_GENERIQUE :
                $model = false;
                break;
        }

        if ($model) {
            if (empty($params['id'])) {
                $error = array('error' => 'Element non précisé', 'errorCode' => 'noid');
            } else {
                $id = $params['id'];
                $element = $model->find_by_id($id);
                if (empty($element)) {
                    $error = array('error' => 'Id d\'élément inexistant', 'errorCode' => 'noelement');
                }
            }
        } else {
            $element = [1];
        }

        if (empty($params['formationCommentaire'])) {
            $error =  array('error'=>'Veuillez remplir les champs obligatoires.', 'errorCode' => 'nocommentaire');
        }

        if ($type == CONST_FORMATION_PREINSCRIPTION || $type == CONST_FORMATION_DEMANDE) {
            if (empty($params['formationParticipants'])) {
                $error =  array('error'=>'Veuillez remplir les champs obligatoires.', 'errorCode' => 'noparticipant');
            }
        } else {
            if (empty($params['formationTheme'])) {
                $error =  array('error'=>'Veuillez remplir les champs obligatoires.', 'errorCode' => 'notheme');
            }
        }

        if ($error) {
            return $error;
        }
        return $element;
    }

    protected function _processFormulaire ($params, $type) {
        $element = $this->_validateFormulaire($params, $type);
        if (is_array($element) && !empty($element['error'])) {
            return $element;
        }
        $model_formation = mvc_model('Formation');

        $formationCommentaire = wp_kses(nl2br($params['formationCommentaire']), Config::$allowedMailTags);

        if ($type == CONST_FORMATION_PREINSCRIPTION || $type == CONST_FORMATION_DEMANDE) {
            $formationParticipants = sanitize_text_field($params['formationParticipants']);
        } else {
            $formationTheme = sanitize_text_field($params['formationTheme']);
        }

        switch ($type) {
            case CONST_FORMATION_PREINSCRIPTION :
                $model_formation->sendEmailPreinscription($element, $formationParticipants, $formationCommentaire);
                break;
            case CONST_FORMATION_DEMANDE :
                $model_formation->sendEmailDemande($element, $formationParticipants, $formationCommentaire);
                break;
            case CONST_FORMATION_GENERIQUE :
                $model_formation->sendEmailGenerique($formationTheme, $formationCommentaire);
                break;
        }
        return array('valid'=>' Votre demande a bien été envoyée. ');

    }



    /**
     * Content Block (AJAX Friendly)
     *
     * @return void
     */
    public function contentdemande()
    {
        if ( !CriIsNotaire() ) {
            CriRefuseAccess();
        }
        $params = $this->params;

        $return = $this->_processFormulaire($params, CONST_FORMATION_DEMANDE);

        echo json_encode($return);
        die();
    }

    /**
     * Content Block (AJAX Friendly)
     *
     * @return void
     */
    public function contentdemandegenerique()
    {
        if ( !CriIsNotaire() ) {
            CriRefuseAccess();
        }
        $params = $this->params;

        $return = $this->_processFormulaire($params, CONST_FORMATION_GENERIQUE);

        echo json_encode($return);
        die();
    }

    /**
     * Content Block (AJAX Friendly)
     *
     * @return void
     */
    public function contentpreinscription()
    {
        if ( !CriIsNotaire() ) {
            CriRefuseAccess();
        }
        $params = $this->params;

        $return = $this->_processFormulaire($params, CONST_FORMATION_PREINSCRIPTION);

        echo json_encode($return);
        die();
    }

    public function demandegenerique() {
        if ( !CriIsNotaire() ) {
            CriRefuseAccess();
        }
        $params = $this->params;

        $demandeGenerique = array(
            'ajax-action' => MvcRouter::public_url(array(
                'controller'=> 'formations',
                'action' => 'contentdemandegenerique',
            )).(!empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '')
        );
        $this->set('demandeGenerique', $demandeGenerique);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $return = $this->_processFormulaire($params, CONST_FORMATION_GENERIQUE);

            foreach ($return as $key => $item) {
                $this->set($key, $item);
            }
        }
    }

    public function demande()
    {
        if ( !CriIsNotaire() ) {
            CriRefuseAccess();
        }
        $params = $this->params;
        $formation = false;
        if (!empty($params['id'])) {
            $formationId = $params['id'];
            $formation = mvc_model('Formation')->find_by_id($formationId);

            if (empty($formation)) {
                $params['id'] = null;
                $url = MvcRouter::public_url(array(
                        'controller'=> 'formations',
                        'action' => 'demandegenerique',
                    )
                );
                wp_redirect($url);
            }
        }

        $demandeFormation = array(
            'formation' => array(
                'title' => $formation->post->post_title,
                'content' => $formation->post->post_content,
                'url' => MvcRouter::public_url(array(
                    'controller'=> 'formations',
                    'action' => 'show',
                    'id' => $formation->id
                )),
            ),
            'ajax-action' => MvcRouter::public_url(array(
                'controller'=> 'formations',
                'action' => 'contentdemande',
                'id' => $formation->id,
            )).(!empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '')
            ,
        );
        $this->set('demandeFormation', $demandeFormation);


        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $return = $this->_processFormulaire($params, CONST_FORMATION_DEMANDE);

            foreach ($return as $key => $item) {
                $this->set($key, $item);
            }
        }
    }

    public function preinscription()
    {
        if ( !CriIsNotaire() ) {
            CriRefuseAccess();
        }
        $params = $this->params;
        $session = false;
        if (!empty($params['id'])) {
            $sessionId = $params['id'];
            $options = array(
                'conditions' => array(
                    'id' => $sessionId,
                    'date > ' => date('Y-m-d'),
                )
            );
            $session = mvc_model('Session')->find($options);
            // get first element of response
            $session = reset($session);
        }

        if (empty($session) || !$session->entite->is_cridon) {
            $params['id'] = null;
            $url = MvcRouter::public_url(array(
                    'controller'=> 'formations',
                    'action' => 'demandegenerique',
                )
            );
            wp_redirect($url);
        }

        $preinscription = array(
            'formation' => array(
                'title' => $session->formation->post->post_title,
                'content' => $session->formation->post->post_content,
                'url' => MvcRouter::public_url(array(
                        'controller'=> 'formations',
                        'action' => 'show',
                        'id' => $session->formation->id,
                    )).'?'.http_build_query(array('sessionid' => $session->id)),
                'organisme' => $session->entite->name,
                'city' => $session->entite->city,
                'time' => $session->timetable
            ),
            'ajax-action' => MvcRouter::public_url(array(
                'controller'=> 'formations',
                'action' => 'contentpreinscription',
                'id' => $session->id,
            )).(!empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '')
        );

        $this->set('preinscription', $preinscription);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $return = $this->_processFormulaire($params, CONST_FORMATION_PREINSCRIPTION);

            foreach ($return as $key => $item) {
                $this->set($key, $item);
            }
        }
    }

    /**
     * Add canonical on old route : /catalogue to new route : /formations/catalogue
     */
    public function oldcatalog(){

        add_action('wp_head','rel_canonical_catalog');
        $this->catalog();
    }

    /**
     * Retrieve all formations for current or next year millesime and return an array (by matiere by id asc) of array (formations)
     * @param $current boolean : If true : current year else : next year
     * @return array of array
     */
    public function catalog($current = true)
    {
        $option = get_option('cridon_next_year_catalog_published');
        $this->set('catalogPublished', $option);

        $year = ($current ? date('Y') : Date ('Y', strtotime('+1 year')));
        $options = array(
            'selects' => array(
                'f.id','p.post_title', 'd.download_url','ma.label'
            ),
            'conditions' => array(
                'm.year' => $year,
                'p.post_status' => 'publish'
            ),
            'synonym' => 'f',
            'joins' => array(
                array(
                    'model'  => 'Post',
                    'alias'  => 'p',
                    'on'     => ' p.ID = f.post_id'
                ),
                array(
                    'model'  => 'Matiere',
                    'alias'  => 'ma',
                    'on'     => ' ma.id = f.id_matiere'
                ),
                array(
                    'model'  => 'Millesime',
                    'alias'  => 'm',
                    'on'     => ' m.id_formation = f.id'
                ),
                array(
                    'model'  => 'Document',
                    'alias'  => 'd',
                    'on'     => ' d.id_externe = f.id'
                ),
            )
        );
        $formations = $this->model->find($options);

        $sortedFormations = array();
        foreach($formations as $formation){
            $sortedFormations[$formation->matiere->id][] = $formation;
        }
        ksort($sortedFormations);
        $this->set('sortedFormations', $sortedFormations);
    }

    public function catalognextyear()
    {
        $option = get_option('cridon_next_year_catalog_published');
        $this->set('catalogPublished', $option);
        if ($option){
            $this->catalog(false);
        }
    }
}
