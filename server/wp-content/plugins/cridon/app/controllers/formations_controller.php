<?php

/**
 * Class FormationsController
 */
require_once 'base_actu_controller.php';
class FormationsController extends BaseActuController
{

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

        $month = $params['month'];
        $tmpmonth = DateTime::createFromFormat('!m', $month);
        $tmpmonth = $tmpmonth->format('F');
        $year = $params['year'];

        $firstdayofmonth = new DateTime('first day of '. $tmpmonth . ' ' . $year);
        $daytostartofweek = intval($firstdayofmonth->format('N')) -1;
        $firstday = $firstdayofmonth->modify('-'. $daytostartofweek .' days');

        $lastdayofmonth = new DateTime('last day of '. $tmpmonth . ' ' . $year);
        $daytoendofweek = 7 - intval($lastdayofmonth->format('N'));
        $lastday = $lastdayofmonth->modify('+'. $daytoendofweek .' days');

        $calendar = array();

        $date = $firstday;
        while ($lastday->getTimestamp() > $date->getTimestamp()) {
            $calendar[$date->format('Y-m-d')] = array(
                'day' => $date->format('d'),
                'month' => $date->format('m'),
                'year' => $date->format('Y'),
                'weekday' => $date->format('l'),
            );
            $date->modify('+1 day');
        }

        $this->set('month', $month);
        $this->set('year', $year);
        $this->set('calendar', $calendar);

    }

    public function calendar_now()
    {
        $this->params['year']         = date('Y');
        $this->params['month']        = date('m');

        $this->calendar();
    }
}
