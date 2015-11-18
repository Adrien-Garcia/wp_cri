<?php


class QueryBuilder{
    
    public $lastInsertId;// last insert in table
    protected $wpdb; // Manipulate database in wordpress
    
    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->lastInsertId = false;
    }
    
    /*
     * Delete data
     */
    
    /**
     * Delete data in a table
     * 
     * @param array $options Contains table name and clause where 
     */
    public function delete( $options ){
        $this->wpdb->query( 'DELETE FROM '.$this->wpdb->prefix.$options['table'].' WHERE '.$options['conditions'] );
    }    
    
    /**
     * Delete document in associate with model
     * 
     * @param string $model
     * @param integer $object_id 
     */
    public function deleteDocument( $model,$object_id ){
        $table = $model->table;
        $type = str_replace( $this->wpdb->prefix, '', $table);
        $conditions = 'type="'.$type.'" AND id_externe='.$object_id ;
        $options = array(
            'table'       => 'document',
            'conditions'  => $conditions
        );
        $this->delete( $options );
    }
    
    /**
     * Delete post for specific ID
     * @param type $post_ID
     */
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
    //Delete commentmeta when comment is deleted.
    private function deleteCommentMeta( $comment_ID ){
        $conditions = 'comment_id = '.$comment_ID;
        $options = array(
            'table' => 'commentmeta',
            'conditions' => $conditions
        );       
        $this->delete($options);
    }
    //Delete Comment when post is deleted
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
    // Delete postmeta where post is deleted
    private function deletePostMeta( $post_ID ){
        $conditions = 'post_id = '.$post_ID;
        $options = array(
            'table' => 'postmeta',
            'conditions' => $conditions
        );       
        $this->delete($options);
    }
    
    /*
     * End delete data
     */
    
    /*
     * Insert or update
     */
    
    /**
     * Insert data in table 
     * 
     * @param array $options Specify table name, attributes and values 
     */
    public function insert( $options ){
        $this->wpdb->query( 'INSERT INTO '.$this->wpdb->prefix.$options['table'].'('.$options['attributes'].') VALUE('.$options['values'].')' );
        if( $this->wpdb->insert_id ){
            $this->lastInsertId = $this->wpdb->insert_id;
        }else{
            $this->lastInsertId = false;
        }
    }
    
    //This function return format for value when you used prepared query
    private function format( $value ){
        if( is_float( $value ) || is_double( $value )){
            return "%f";                    
        }
        elseif( is_int( $value ) ){
            return "%d";                    
        }
        return "%s";
    }
    /**
     * Insert or update data
     * @param array $data Contain data to push
     * @param string $table Table where put data
     * @param string $primaryKey Specify primary key of table
     * @return bool 
     */
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
                unset( $data->$key );// not really necessary but on the safe side
            }
            $sql = 'INSERT INTO '.$table.'('.implode(',',$fields).') VALUES( '.implode(',',$values).' )';
            $action = 'insert';
        }
        $this->wpdb->query( $this->wpdb->prepare( $sql, $d ) );// use prepared query
        if( $action == 'insert' ){
            if( $this->wpdb->insert_id ){
                $this->lastInsertId = $this->wpdb->insert_id;
            }
        }
        return true;
    }
    
    /*
     * End insert or update
     */
    
    
    /*
     * Retrieve data from table
     */
    
    /**
     * Find more elements in table with an sample clause WHERE in condition of query
     * 
     * @param array $options Specify a model or a table, and the conditions in the query
     * @return array
     */
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

    /**
     * Find one element in table with an sample clause WHERE in condition of query
     *
     * @param array $options Specify a model or a table, and the conditions in the query
     * @return array
     */
    public function findOneByOptions( $options ){
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
        return $this->wpdb->get_row( $query );
    }
    
    //Sample function to construct join clause
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
        if( isset( $option['nested'] ) ){
            $table = $option['table'];
        }else{
            $table = $this->wpdb->prefix.$option['table'];            
        }
        $sql .= $table.' ON '.$option['column'].' ';
        return $sql;
    }
    /**
     * Find more elements in table
     * 
     * @param string $table Table name
     * @param array $options Contain keys where specified sql clauses (join,where,...)
     * @param string $primaryKey The primary key for table
     * @return array
     */
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
                if( isset( $options['nested'] ) ){// Nested query
                    $sql .= $k.' IN ('.$v.' )';
                }else{
                    foreach( $v as $l=>$w ){
                        if( !is_numeric( $w ) ){
                            $w = '"'.mysqli_real_escape_string( $w ).'"'; //clean
                        }
                        $cond[] = "$w";
                    }
                    $sql .= $k.' IN ('.implode(' ,',$cond).' )';                    
                }
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
    
     /**
     * Build sample query
     * 
     * @param string $table Table name
     * @param array $options Contain keys where specified sql clauses (join,where,...)
     * @param string $primaryKey The primary key for table
     * @return array
     */
    public function buildQuery( $table , $options = array() ,$primaryKey = 'id' ){
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
        return $sql;
    }
    /**
     * Find one element in table
     * 
     * @param string $table Table name
     * @param array $options Contain keys where specified sql clauses (join,where,...)
     * @param string $primaryKey The primary key for table
     * @return object
     */
    public function findOne( $table , $options = array() ,$primaryKey = 'id' ){
        $options['limit'] = 1;
        $results = $this->findAll( $table, $options, $primaryKey );
        return ( empty( $results ) ) ? null : $results[0];
    }
    /*
     * End retrive data
     */

    /**
     * Insert multi rows
     * using syntax
     * INSERT INTO table
     * (prenom, nom, ville, age)
     *   VALUES
     * ('Robecca', 'Armand', 'Saint-Didier-des-Bois', 24),
     * ('Aimee', 'Hebert', 'Marigny-le-Chatel', 36),
     * ('Marielle', 'Ribeiro', 'Mailleres', 27),
     * ('Hilaire', 'Savary', 'Conie-Molitard', 58)
     *
     * @param array $options
     */
    public function insertMultiRows($options = array())
    {
        $query = 'INSERT INTO ' . $this->wpdb->prefix . $options['table']
                 . '(' . $options['attributes'] . ')
            VALUES
            ' . $options['values'];

        $this->wpdb->query($query);
    }
}