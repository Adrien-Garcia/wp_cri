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
        );

        $this->set('data', $data);

    }

    protected function _generate_calendar_array($month = null, $year = null) {
        $month = (!empty($month) && intval($month) <= 12 && intval($month) > 0 ) ? $month : date('m');
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
                'url' => MvcRouter::public_url($urlOptions),
                'id' => $session->id
            );
            $this->addSessionAction($lineSession);
            $calendar[$key]['sessions'][] = $lineSession;
        }

        return $calendar;
    }

    /**
     * Will provide session line in calendar with information concerning subscription or contact
     * @param $lineSession array : a Session entry in calendar
     */
    protected function addSessionAction(& $lineSession)
    {
        /** @TODO */
    }
}
