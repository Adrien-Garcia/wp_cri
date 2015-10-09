<?php

class Formation extends MvcModel {
    var $table          = "{prefix}formation";
    var $includes       = array('Post');
    var $belongs_to     = array(
        'Post' => array('foreign_key' => 'post_id')
    );
    var $display_field = 'post_id';
    public function delete($id) {
        $qb = new QueryBuilder();
        $model = $qb->find( array( 'attributes' => array('id,post_id'), 'model' => $this->name , 'conditions' => 'id = '.$id ) );
        if( count( $model ) > 0 ){
            if( $model[0]->post_id != null ){
                $conditions = 'post_id = '.$model[0]->post_id. ' AND meta_key = "_cridon_post_value" AND meta_value = '.Config::$data[ $this->name ][ 'value' ];
                $options = array(
                    'table' => 'postmeta',
                    'conditions' => $conditions
                );       
                $qb->delete($options);
            }
        }
        parent::delete($id);
    }
}

?>