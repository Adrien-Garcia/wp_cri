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

        $month = !empty($params['month']) ? $params['month'] : date('m');
        $year = !empty($params['year']) ? $params['year'] : date('Y');

        $calendar = $this->_generate_calendar_array($month, $year);

        $calendar = $this->_fill_calendar_data($calendar);

        $this->set('month', $month);
        $this->set('year', $year);
        $this->set('calendar', $calendar);

    }

    protected function _generate_calendar_array($month = null, $year = null) {
        $month = (!empty($month) && intval($month) < 12 && intval($month) > 0 ) ? $month : date('m');
        $year = (!empty($year) && intval($year) > 1970) ? $year : date('Y');

        $tmpmonth = DateTime::createFromFormat('!m', $month);
        $tmpmonth = $tmpmonth->format('F');

        $firstdayofmonth = new DateTime('first day of '. $tmpmonth . ' ' . $year);
        $this->firstDayOfMonth = clone $firstdayofmonth;
        $daytostartofweek = intval($firstdayofmonth->format('N')) -1;
        $firstday = $firstdayofmonth->modify('-'. $daytostartofweek .' days');

        $lastdayofmonth = new DateTime('last day of '. $tmpmonth . ' ' . $year);
        $this->lastDayOfMonth = clone $lastdayofmonth;
        $daytoendofweek = 7 - intval($lastdayofmonth->format('N'));
        $lastday = $lastdayofmonth->modify('+'. $daytoendofweek .' days');

        $calendar = array();

        $date = $firstday;
        $today = strtotime('today midnight');
        while ($lastday->getTimestamp() > $date->getTimestamp()) {
            $calendar[$date->format('Y-m-d')] = array(
                'date' => clone $date,
                'today' => $date->getTimestamp() == $today,
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
     *     'event' => (optional) Name for the event of the day @TODO
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

        $modelSession = new Session();
        $sessions = $modelSession->find(array(
            'conditions' => array(
                'OR' => array(
                    'Session.date >= ' => $this->firstDayOfMonth->format('Y-m-d'),
                    'Session.date <= ' => $this->lastDayOfMonth->format('Y-m-d')
                )
            ),
            'joins' => array(
                'Formation',
                'Lieu'
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

        // On récupère les lieux dont dépends l'étude
        $modelEtude = new Etude();
        $lieuxAssociatedToEtude = $modelEtude->getLieuxAssociatedToEtude();

        foreach ($sessions as $session) {
            $key = $session->date;
            if (!isset($calendar[$key])) {
                throw new OutOfBoundsException(sprintf('Key %s not found for current calendar', $key));
            }
            $formation = $formations[$session->id_formation];
            $urlOptions = array(
                'controller' => 'documents',
                'action'     => 'download',
                'id'         => $session->id_formation
            );
            $lineSession = array(
                'name' => $formation->post->post_title,
                'short_name' => $formation->short_name,
                'matiere' => $formation->matiere,
                'time' => $session->timetable,
                'url' => MvcRouter::public_url($urlOptions)
            );
            $this->addContactAction($lineSession, $session, $lieuxAssociatedToEtude);
            $calendar[$key]['sessions'][] = $lineSession;
        }

        return $calendar;
    }

    /**
     * Will provide session line in calendar with information concerning subscription or contact
     * @param $lineSession array : a Session entry in calendar
     * @param $session array : the session with all data
     * @param $lieuxAssociatedToEtude : every lieu associated to current etude
     */
    protected function addContactAction(& $lineSession, $session, $lieuxAssociatedToEtude)
    {
        $lineSession ['action'] = $lineSession ['action_label'] =  $lineSession['details'] = '';
        $view_vars = array();
        $view_vars ['lieu'] = '';
        $view_vars ['contact_lieu'] = false;
        // Pour les différents cas ; se reporter à goo.gl/0fHVxB
        if (!is_user_logged_in()){
            $error_code = "PROTECTED_CONTENT";
            $lineSession ['action'] = "?openLogin=1&messageLogin=" . $error_code . "&requestUrl=" . urlencode('/calendrier-des-formations');

            $lineSession ['action_label'] = 'Se former';
        } elseif (!CriIsNotaire() || !in_array(CriNotaireData()->id_fonction, Config::$allowedNotaryFunction) ) {
            if (!$session->lieu->is_cridon){
                $view_vars ['lieu'] = $session->lieu;
            }
        } else {
            if ($session->lieu->is_cridon) {
                $lineSession ['action'] = '/session-pré-inscription-cridon';
                $lineSession ['action_label'] = 'Se pré-inscrire';
            } else {
                // L'étude dépend-t-elle du lieu ?
                $view_vars ['lieu'] = $session->lieu;
                $etudeIsAssociatedToLieu = false;
                foreach ($lieuxAssociatedToEtude as $lieu) {
                    if ($session->id_lieu == $lieu->id) {
                        $etudeIsAssociatedToLieu = true;
                        $view_vars ['contact_lieu'] = true;
                        break;
                    }
                }
                if (!$etudeIsAssociatedToLieu) {
                    $lineSession ['action'] = '/session-contact-cridon';
                    $lineSession ['action_label'] = 'Contacter le CRIDON LYON';
                }
            }
        }
        $view_vars ['session'] = $lineSession;
        $lineSession ['details'] = CriRenderView('session_details',$view_vars,'sessions',false);
    }
}
