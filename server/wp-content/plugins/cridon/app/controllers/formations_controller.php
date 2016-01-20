<?php

/**
 * Class FormationsController
 */
class FormationsController extends MvcPublicController
{

    /**
     * Action Archive
     */
    public function index()
    {
        $this->process_params_for_search();

        // params
        $params = $this->params;

        if (isset($_GET['option']) && $_GET['option'] != 'all') {
            if ($_GET['option'] == 'old') { // Formations passées : triées de la plus récente à la plus ancienne
                $params['order']      = 'custom_post_date DESC';
                $params['conditions'] = array('custom_post_date < ' => date('Y-m-d'));
            } elseif ($_GET['option'] == 'new') { // Formations a venir : triées de la plus proche à la plus éloignée
                $params['order']      = 'custom_post_date ASC';
                $params['conditions'] = array('custom_post_date >= ' => date('Y-m-d'));
            }
        }

        // get collection
        $collection = $this->model->paginate($params);

        // set object to template
        $this->set('objects', $collection['objects']);
        $this->set_pagination($collection);
    }
}