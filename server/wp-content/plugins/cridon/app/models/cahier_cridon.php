<?php

class CahierCridon extends MvcModel {
    var $table          = "{prefix}cahier_cridon";
    var $includes       = array('Post');
    var $belongs_to     = array(
        'Post' => array('foreign_key' => 'post_id')
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
        parent::delete($id);
    }
}

?>