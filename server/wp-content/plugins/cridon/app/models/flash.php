<?php

class Flash extends MvcModel {
    var $table     = "{prefix}flash";
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
    public function create($data) {  
        if( isset( $data[$this->name]['post_id'] ) ){
            $qb = new QueryBuilder();
            $options = array(
                'table'         => 'postmeta',
                'attributes'    => 'post_id,meta_key,meta_value',
                'values'        => $data[$this->name]['post_id'].',"_cridon_post_value",'.Config::$data[ $this->name ][ 'value' ]
            );       
            $qb->insert($options);
        }
        return parent::create($data);
    }
    public function save($data) {
        if( isset( $data[$this->name]['post_id'] ) ){
            $qb = new QueryBuilder();
            $options = array(
                'table'         => 'postmeta',
                'attributes'    => 'post_id,meta_key,meta_value',
                'values'        => $data[$this->name]['post_id'].',"_cridon_post_value",'.Config::$data[ $this->name ][ 'value' ]
            );       
            $qb->insert($options);
        }
        return parent::save($data);
    }
}

?>