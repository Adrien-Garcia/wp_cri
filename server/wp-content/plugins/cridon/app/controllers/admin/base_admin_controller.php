<?php

/**
 * Description of base_admin_controller.php
 *
 * @package wp_cridon
 * @author  eTech
 * @contributor  Joelio
 */

abstract class BaseAdminController extends MvcAdminController
{

    /**
     * @var string
     */
    const NOTICE_SUCCESSFULLY_SAVED     = 'Sauvegarde terminée avec succès !';

    /**
     * @var string
     */
    const NOTICE_SUCCESSFULLY_CREATED   = 'Création terminée avec succès !';

    /**
     * @var string
     */
    const NOTICE_SUCCESSFULLY_DELETED   = 'Suppression terminée avec succès !';

    /**
     * @var string
     */
    const WARNING_NOTFOUND_MSG   = ' %s avec ID %s introuvable !';

    public function __construct()
    {
        parent::__construct();

        $this->model->per_page = CONST_ADMIN_NB_ITEM_PERPAGE;
    }


    public function delete() {
        $this->verify_id_param();
        $this->set_object();
        if (!empty($this->object)) {
            $this->model->delete($this->params['id']);
            $this->flash('notice', self::NOTICE_SUCCESSFULLY_DELETED);
        } else {
            $warning = sprintf(self::WARNING_NOTFOUND_MSG, $this->model->name, $this->params['id']);
            $this->flash('warning', $warning);
        }
        $url = MvcRouter::admin_url(array('controller' => $this->name, 'action' => 'index'));
        $this->redirect($url);
    }

    public function create_or_save()
    {
        if (!empty($this->params['data'][$this->model->name])) {
            $object = $this->params['data'][$this->model->name];
            if (empty($object['id'])) {
                $this->model->create($this->params['data']);
                $id = $this->model->insert_id;
                $url = MvcRouter::admin_url(array('controller' => $this->name, 'action' => 'edit', 'id' => $id));
                $this->flash('notice', self::NOTICE_SUCCESSFULLY_CREATED);
                $this->redirect($url);
            } else {
                if ($this->model->save($this->params['data'])) {
                    $this->flash('notice', self::NOTICE_SUCCESSFULLY_SAVED);
                    $this->refresh();
                } else {
                    $this->flash('error', $this->model->validation_error_html);
                }
            }
        }
    }

    public function create() {
        if (!empty($this->params['data'][$this->model->name])) {
            $id = $this->model->create($this->params['data']);
            $url = MvcRouter::admin_url(array('controller' => $this->name, 'action' => 'edit', 'id' => $id));
            $this->flash('notice', self::NOTICE_SUCCESSFULLY_CREATED);
            $this->redirect($url);
        }
    }

    public function save() {
        if (!empty($this->params['data'][$this->model->name])) {
            if ($this->model->save($this->params['data'])) {
                $this->flash('notice', self::NOTICE_SUCCESSFULLY_SAVED);
                $this->refresh();
            } else {
                $this->flash('error', $this->model->validation_error_html);
            }
        }
    }
}