<?php

/**
 * Created by PhpStorm.
 * User: amsellem
 * Date: 08/02/2016
 * Time: 14:52
 */
class BaseActuController extends MvcPublicController
{
    /**
     * @var \App\Override\Model\CridonMvcModel
     */
    public $model = null;
    /**
     * @override
     * @return boolean
     */
    public function set_object()
    {
        if (!empty($this->model->invalid_data)) {
            if (!empty($this->params['id']) && empty($this->model->invalid_data[$this->model->primary_key])) {
                $this->model->invalid_data[$this->model->primary_key] = $this->params['id'];
            }
            $object = $this->model->new_object($this->model->invalid_data);
        } else if (!empty($this->params['id'])) {
            //optimized query
            $object = $this->model->associatePostWithDocumentByPostName($this->params['id']);
            if (empty($object) && is_numeric($this->params['id'])) {
                //if the url is numeric and if it's not a post name
                $object = $this->model->find_by_id($this->params['id']);
                if (!empty($object)) {
                    $options = array(
                        'controller' => $this->name,
                        'action' => 'show',
                        'id' => $object->post->post_name
                    );
                    $url = MvcRouter::public_url($options);
                    if (!empty($_GET)) {
                        $url .= '?'.http_build_query($_GET);
                    }

                    //redirect to url with virtual-name
                    wp_redirect($url, 301);
                    exit;
                }
            }
        }
        if (!empty($object)) {
            $this->set('object', $object);
            MvcObjectRegistry::add_object($this->model->name, $this->object);
            return true;
        }
        redirectTo404();
    }
}
