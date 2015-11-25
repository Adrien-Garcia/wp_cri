<?php

class CahierCridon extends MvcModel {
    var $table          = "{prefix}cahier_cridon";
    var $includes       = array('Post');
    var $belongs_to     = array(
        'Post' => array('foreign_key' => 'post_id'),
        'Matiere' => array('foreign_key' => 'id_matiere'),
        'principal' => array('foreign_key' => 'id_parent'),
    );
    var $has_many       = array(
        'secondaires' => array(
            'foreign_key' => 'id_parent'
        )
    );
    var $display_field = 'post_id';
    public function delete($id) {
        $qb = new QueryBuilder();
        $model = $qb->find( array( 'attributes' => array('id,post_id'), 'model' => $this->name , 'conditions' => 'id = '.$id ) );
        if( !empty( $model ) ){
            if( $model[0]->post_id != null ){
                //Delete post
                $qb->deletePost( $model[0]->post_id );
            }
        }
        //Delete document
        $qb->deleteDocument( $this , $id );
        parent::delete($id);
    }
}

?>