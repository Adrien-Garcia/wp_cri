<?php


class QueryBuilder{
    
    protected $wpdb;
    
    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
    }
    
    public function find( $options ){
        $query = 'SELECT ';
        $fields = ' * ';
        if( isset( $options['attributes'] ) ){
            $fields = implode( ',',$options['attributes'] );
        }
        $query .= $fields. ' FROM ';
        if( $options[ 'model' ] != null ){
            $oModel = mvc_model( $options[ 'model' ] );
            $query .= $oModel->table. ' ';
        }else{
            if( !isset($options[ 'table' ]) ) return null;
            $query .= $options[ 'table' ].' ';
        }
        if( isset( $options['conditions'] ) ){
            $query .= ' WHERE ' . $options[ 'conditions' ];
        }
        return $this->wpdb->get_results( $query );
    }
    public function delete( $options ){
        $this->wpdb->query( 'DELETE FROM '.$this->wpdb->prefix.$options['table'].' WHERE '.$options['conditions'] );
    }
    
    public function insert( $options ){
        $this->wpdb->query( 'INSERT INTO '.$this->wpdb->prefix.$options['table'].'('.$options['attributes'].') VALUE('.$options['values'].')' );
    }
    
    public function deletePost( $post_ID ){
        // Delete postmeta before deleting post
        $this->detelePostMeta($post_ID);
        //Delete trash ...
        $conditions_2 = 'post_parent = '.$post_ID;
        $options_2 = array(
            'table' => 'posts',
            'conditions' => $conditions_2
        );       
        $this->delete($options_2);
        // Delete post
        $conditions = 'ID = '.$post_ID;
        $options = array(
            'table' => 'posts',
            'conditions' => $conditions
        );       
        $this->delete($options);
    }
    
    private function detelePostMeta( $post_ID ){
        $conditions = 'post_id = '.$post_ID;
        $options = array(
            'table' => 'postmeta',
            'conditions' => $conditions
        );       
        $this->delete($options);
    }
}

?>