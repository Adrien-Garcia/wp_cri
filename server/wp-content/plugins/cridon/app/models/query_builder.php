<?php


class QueryBuilder{
    
    public $lastInsertId;
    protected $wpdb;
    
    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->lastInsertId = false;
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
        $this->deletePostMeta( $post_ID );
        //Delete trash ...
        $conditions_2 = 'post_parent = '.$post_ID;
        $options_2 = array(
            'table' => 'posts',
            'conditions' => $conditions_2
        );       
        $this->delete($options_2);
        // Delete comment
        $this->deleteComment( $post_ID );
        // Delete post
        $conditions = 'ID = '.$post_ID;
        $options = array(
            'table' => 'posts',
            'conditions' => $conditions
        );       
        $this->delete($options);
    }
    
    private function deleteCommentMeta( $comment_ID ){
        $conditions = 'comment_id = '.$comment_ID;
        $options = array(
            'table' => 'commentmeta',
            'conditions' => $conditions
        );       
        $this->delete($options);
    }
    private function deleteComment( $post_ID ){
        $conditions = 'comment_post_ID = '.$post_ID;
        $options = array(
            'table' => 'comments',
            'conditions' => $conditions
        );  
        $aComments = $this->findAll( 'comments', $options,'comment_ID' );
        if( !empty( $aComments ) ){
            foreach ( $aComments as $comment ){
                $this->deleteCommentMeta( $comment->comment_ID );
            }            
        }
        $this->delete($options);
    }
    private function deletePostMeta( $post_ID ){
        $conditions = 'post_id = '.$post_ID;
        $options = array(
            'table' => 'postmeta',
            'conditions' => $conditions
        );       
        $this->delete($options);
    }
    
    private function constructJoin( $option ){
        if( isset( $option[ 'type'] ) ){
            switch ( $option[ 'type'] ){
                case 'left':
                    $sql = ' LEFT JOIN ';
                    break;
                case 'right':
                    $sql = ' RIGHT JOIN ';
                    break;
                case 'full':
                    $sql = ' FULL JOIN ';            
                    break;
                default:
                    $sql = ' INNER JOIN ';            
            }            
        }else{
            $sql = ' INNER JOIN ';            
        }
        $table = $this->wpdb->prefix.$option['table'];
        $sql .= $table.' ON '.$option['column'].' ';
        return $sql;
    }
    public function findAll( $table , $options = array() ,$primaryKey = 'id' ){
        $table = $this->wpdb->prefix.$table;
        $sql = 'SELECT ';         
        if( isset( $options['fields'] ) ){
            if( is_array( $options['fields'] ) ){
                $sql .= implode( ', ',$options['fields'] );
            }
            else{
                $sql .= $options['fields'];
            }
        }
        else{
            $sql .='*';
        }
        if (isset($options['synonym'])) {
            $synonym = $options['synonym'];
        } else {
            $synonym = $table;
        }
        $sql .= ' FROM '.$table.' as '.$synonym.' ';
        //construnct join
        if( isset( $options['join'] ) ){
            if( is_array( $options['join'] ) ){
                foreach( $options['join'] as $option ){
                    $sql .= $this->constructJoin( $option );
                }
            }
        }
        //construct conditions
        if( isset( $options['conditions'] ) ){
            $sql .= 'WHERE ';
            if( !is_array( $options['conditions'] ) ){
                $sql .= $options['conditions'];
            }
            else{
                $cond = array();
                foreach( $options['conditions'] as $k=>$v ){
                    if( !is_numeric( $v ) ){
                        $v = '"'.mysqli_real_escape_string( $v ).'"'; //clean
                    }                    
                    $cond[] = "$k = $v";
                }
                $sql .= implode( ' AND ',$cond );
            }            
        }
        if( isset( $options['not'] ) ){
            if ( !isset($options['conditions'] ) ) {
                $sql .= 'WHERE ';
            } else {
                $sql .= ' AND ';
            }
            $cond = array();
            foreach( $options['not'] as $k=>$v ){
                if( !is_numeric($v) ){
                    $v = '"'.mysqli_real_escape_string($v).'"'; //clean
                }                    
                $cond[] = "$k <> $v";
            }
            $sql .= implode( ' AND ',$cond );                
        }
        if( isset( $options['in'] ) && !empty( $options['in'] ) ){
            if ( !isset($options['conditions'] ) && !isset( $options['not'] )) {
                $sql .= 'WHERE ';                
            }else{
                $sql .= ' AND ';
            }
            $cond = array();
            foreach( $options['in'] as $k => $v ){
                foreach( $v as $l=>$w ){
                    if( !is_numeric( $w ) ){
                        $w = '"'.mysqli_real_escape_string( $w ).'"'; //clean
                    }
                    $cond[] = "$w";
                }
                $sql .= $k.' IN ('.implode(' ,',$cond).' )';
                if( count( $options['in']) > 1 ){
                    $sql .= ' AND ';
                }
            }
        }
        if( isset( $options['group'] ) ){
            $sql .= ' GROUP BY '.$options['group'];
        }
        $sql .= ' ORDER BY '.$primaryKey;
        if( isset( $options['order'] ) ){
            $sql .= ' '.$options['order'];
        }else{
            $sql .= ' ASC';
        }
        if( isset( $options['limit'] ) ){
            $sql .= ' LIMIT '.$options['limit'];
        }
        return $this->wpdb->get_results( $sql );
    }
    public function findOne( $table , $options = array() ,$primaryKey = 'id' ){
        $options['limit'] = 1;
        $results = $this->findAll( $table, $options, $primaryKey );
        return ( empty( $results ) ) ? null : $results[0];
    }
    private function format( $value ){
        if( is_float( $value ) || is_double( $value )){
            return "%f";                    
        }
        elseif( is_int( $value ) ){
            return "%d";                    
        }
        return "%s";
    }
    public function save( $data,$table,$primaryKey ){
        $this->lastInsertId = false; // init
        $table = $this->wpdb->prefix.$table;
        $key = $primaryKey;
        $fields = array();
        $values = array();
        $d = array();
        foreach( $data as $k => $v ){ 
            if( isset( $data->$key )&& !empty( $data->$key ) ){
                if( $k !== $key ){
                    $fields[] = "$k= ". $this->format( $v );        
                }
            }else{
                $fields[] = "$k";
                $values[] = $this->format( $v ); 
            }
            $d[] = $v;                    
        }
        //update
        if( isset( $data->$key )&& !empty( $data->$key ) ){
            $this->id = $data->$key;
            if (isset($data->$key)) {
                unset($data->$key);
            }
            $sql = 'UPDATE '.$table.' SET '.implode(',',$fields).' WHERE '.$key.'= %d';            
            $action = 'update';
        }
        else{
            if ( isset( $data->$key ) ) {
                unset( $data->$key );
            }
            $sql = 'INSERT INTO '.$table.'('.implode(',',$fields).') VALUES( '.implode(',',$values).' )';
            $action = 'insert';
        }
        $this->wpdb->query( $this->wpdb->prepare( $sql, $d ) );
        if( $action == 'insert' ){
            if( $this->wpdb->insert_id ){
                $this->lastInsertId = $this->wpdb->insert_id;
            }
        }
        return true;
    }
}

?>