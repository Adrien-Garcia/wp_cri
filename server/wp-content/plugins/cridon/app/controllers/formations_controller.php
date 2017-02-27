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
     * Action Archive
     */
    public function index()
    {
        $this->process_params_for_search();

        // params
        $params = $this->params;
        // Formations a venir : triées de la plus proche à la plus éloignée
        $params['order']      = 'custom_post_date ASC';
        $params['conditions'] = array('custom_post_date >= ' => date('Y-m-d'));
        $collection = $this->model->paginate($params);
        $formationsFutures = $collection['objects'];

        // set object to template
        $this->set('formationsFutures', $formationsFutures);
        $this->set_pagination($collection);
    }

    public function show(){
        parent::show();
        $formation = $this->object;
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
            $organismesAssociatedToEtude = array();
            if (!empty($notaire = CriNotaireData())){
                $modelEtude = new Etude();
                $organismesAssociatedToEtude = $modelEtude->getOrganismesAssociatedToEtude($notaire->crpcen);
            }
            foreach($sessions as $key => $session){
                $data = $this->addContactAction($session,$organismesAssociatedToEtude, false);
                $sessions[$key]->action = $data ['action'];
                $sessions[$key]->action_label = $data ['action_label'];
                $sessions[$key]->contact_organisme = $data ['contact_organisme'];
            }
            // Pass data to the single-formation view
            $this->set('sessions', $sessions);
        }
    }

    public function past()
    {
        $this->process_params_for_search();

        // params
        $params = $this->params;
        // Formations passées : triées de la plus récente à la plus ancienne
        $params['order']      = 'custom_post_date DESC';
        $params['conditions'] = array('custom_post_date < ' => date('Y-m-d'));
        // get collection
        $collection = $this->model->paginate($params);
        $formationsPassees = $collection['objects'];

        $this->set('formationsPassees', $formationsPassees);
        $this->set_pagination($collection);
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
                'Organisme'
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
        $organismesAssociatedToEtude = array();
        if (!empty($notaire = CriNotaireData())){
            $modelEtude = new Etude();
            $organismesAssociatedToEtude = $modelEtude->getOrganismesAssociatedToEtude($notaire->crpcen);
        }

        foreach ($sessions as $session) {
            $key = $session->date;
            if (!isset($calendar[$key])) {
                throw new OutOfBoundsException(sprintf('Key %s not found for current calendar', $key));
            }
            $formation = $formations[$session->id_formation];
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
            $before_today = DateTime::createFromFormat('Y-m-d', $session->date)->getTimestamp() < time();
            $data = $this->addContactAction($session, $organismesAssociatedToEtude, $before_today);

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
     * @param $organismesAssociatedToEtude : every organism associated to current etude
     * @param $remove_actions boolean : If true remove the actions
     * @return array
     */
    protected function addContactAction($session, $organismesAssociatedToEtude, $remove_actions = true)
    {
        $data ['action'] = $data ['action_label'] =  $data['details'] = '';
        $data ['organisme'] = $session->organisme;
        $data ['contact_organisme'] = false;
        // Pour les différents cas ; se reporter à goo.gl/0fHVxB
        if (CriIsNotaire() && in_array(CriNotaireData()->id_fonction, Config::$allowedNotaryFunction) ) { // Line 2 (logged in is notaire)
            // L'étude dépend-t-elle d'un organisme ?
            $etudeIsAssociatedToOrganisme = false;
            foreach ($organismesAssociatedToEtude as $organisme) {
                if ($session->id_organisme == $organisme->id) {
                    $etudeIsAssociatedToOrganisme = true;
                    break;
                }
            }

            if ($session->organisme->is_cridon) { // Cell B2 (préinscription)
                $data ['action'] = '/session-pre-inscription-cridon';
                $data ['action_label'] = 'Se pré-inscrire';
            } else if ($etudeIsAssociatedToOrganisme) { // Cell C2 (informations contact)
                $data ['contact_organisme'] = true;
            } else { // Cell D2 (contact Cridon)
                $data ['action'] = '/session-contact-cridon';
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
}
