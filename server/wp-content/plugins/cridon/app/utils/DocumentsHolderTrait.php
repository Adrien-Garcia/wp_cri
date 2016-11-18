<?php
/*
 * This file is part of the JETPULP wp_cridon project.
 *
 * Copyright (C) JETPULP
 */

trait DocumentsHolderTrait
{
    /**
     * Récupération des documents d'un model
     *
     * @param MvcModelObject $model
     * @return MvcModelObject[]
     */
    public static function getDocuments($model){
        $options = array(
            'conditions' => array(
                'type' => $model->name,
                'id_externe' => $model->primary_key
            )
        );
        return mvc_model('Document')->find($options);
    }

    /**
     * Delete related documents for specified model id
     * @param $id
     */
    public function delete($id)
    {
        $qb    = new QueryBuilder();
        $model = $qb->find(array(
            'attributes' => array('id,post_id'),
            'model'      => $this->name,
            'conditions' => 'id = ' . $id
        ));
        if (!empty($model)) {
            if ($model[0]->post_id != null) {
                //Delete post
                $qb->deletePost($model[0]->post_id);
            }
        }
        //Delete document
        $qb->deleteDocument($this, $id);
        parent::delete($id);
    }
}
