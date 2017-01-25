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
        $daytostartofweek = intval($firstdayofmonth->format('N')) -1;
        $firstday = $firstdayofmonth->modify('-'. $daytostartofweek .' days');

        $lastdayofmonth = new DateTime('last day of '. $tmpmonth . ' ' . $year);
        $daytoendofweek = 7 - intval($lastdayofmonth->format('N'));
        $lastday = $lastdayofmonth->modify('+'. $daytoendofweek .' days');

        $calendar = array();

        $date = $firstday;
        $today = strtotime('today midnight');
        while ($lastday->getTimestamp() > $date->getTimestamp()) {
            $calendar[$date->format('Y-m-d')] = array(
                'date' => $date,
                'today' => $date->getTimestamp() == $today,
            );
            $date->modify('+1 day');
        }
        return $calendar;
    }

    protected function _fill_calendar_data($calendar) {
        foreach ($calendar as $n => $day) {
            $rand = rand(-5, 5);

            $day['event'] = $rand > 3 ? 'Universités lorem ipsum dolor sit amet' : null;

            $day['sessions'] = array();

            $nb = $rand >=0 ? $rand : 0;

            for ($i = 0; $i < $nb; $i++) {
                $data = array(
                    'name' => 'Couple et patrimoine : optimiser le choix du régime matrimonial '.$i,
                    'short_name' => 'Optimisation régime matrimonial '.$i,
                    'matiere' => mvc_model('Matiere')->find_by_id($i+1),
                    'time' => 'Après-midi',
                    'url' => '/formations/'.$i,
                );

                $j = rand(1,5);
                switch ($j) {
                    case 1: // dispensé en chambre + connecté + notaire!=chambre
                        $data['contact_cridon'] = '/contact/cridon-formation';
                        break;
                    case 2: // dispensé en chambre + connecté + notaire==chambre
                        $data['chambre_name'] = 'Chambre régionale des notaires d\'auvergne-rhône-alpes';
                        $data['chambre_phone'] = '0102030405';
                        $data['chambre_email'] = 'contact@institutxavier.fr';
                        break;
                    case 3: // dispensé en chambre + pas connecté
                        $data['place'] = 'Institut Xavier - Grenoble';
                        break;
                    case 4: // dispensé au cridon + connecté
                        $data['inscription_url'] = '/inscription-formation/'.$i;
                        break;
                    case 5: // dispensé au cridon + pas connecté @TODO pas dans les maquettes, à spécifier ?
                        $data['contact_cridon'] = '/contact/cridon-formation';
                        break;
                }

                $day['sessions'][] = $data;
            }

            $calendar[$n] = $day;
        }
        return $calendar;
    }
}
