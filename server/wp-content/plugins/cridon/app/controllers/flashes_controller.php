<?php

class FlashesController extends MvcPublicController {
    public function show() {
        $entity = $this->model->find_by_id($this->params['id']);
        
        if (empty($entity)) {
            MvcError::fatal('Object not found!');
        }
        $this->set('entity', $entity);
        $this->set('post', $entity->post);
    }
}

?>