<?php
//Retrieve post data using custom table join
function custom_posts_join ($join) {
    global $custom_global_join;
    if ( $custom_global_join ){
        $join .= " $custom_global_join";
    }
    return $join;
}
function custom_posts_where ($where) {
    global $custom_global_where;
    if ( $custom_global_where ) {
        $where .= " $custom_global_where";
    }
    return $where;
}
add_filter('posts_join','custom_posts_join');
add_filter('posts_where','custom_posts_where');
function resetGlobalVars(){
    global $custom_global_join;
    global $custom_global_where;
    $custom_global_join = $custom_global_where = '';
}
// End retrieve post

// After save into post table, save in others tables 
function save_post_in_table( $post_ID ){ 
    if( $_POST[ 'post_type' ] == 'post' && !wp_is_post_revision( $post_ID ) ) {
        if( isset( $_POST[ '_wp_http_referer' ] ) ){
            $http = explode( 'cridon_type=', $_POST[ '_wp_http_referer' ] );
            if( count( $http ) == 2 ){
                if( isset( Config::$data[ $http[ 1 ] ] ) ){
                    if( findBy( Config::$data[ $http[ 1 ] ][ 'name' ], $post_ID ) == null ){//no duplicate
                        insertInTable( Config::$data[ $http[ 1 ] ][ 'name' ],$post_ID );                         
                    }
                }
            }
        }
    }
    return $post_ID;
}
add_action('save_post','save_post_in_table');

function insertInTable( $table,$post_ID ){
    global $wpdb;
    $wpdb->query( 'INSERT INTO '.$wpdb->prefix.$table.'(post_id) VALUE('.$post_ID.')' );
}
// end after save into post table, save in othres tables

// deleting in post table and in others table
add_action( 'delete_post', 'before_deleting' );
function before_deleting( $post_ID ){
    if( wp_is_post_revision( $post_ID ) ) return;
    deleteAllById( $post_ID );
}
function deleteAllById( $post_ID ){
    global $wpdb;
    foreach( Config::$data as $v ){
        $table = $v[ 'name' ];
        $object = findBy($table, $post_ID);
        if( $object ){
            $wpdb->query( 'DELETE FROM '.$wpdb->prefix.'document WHERE type = "'.$table.'" AND id_externe ='.$object->id );
        }
        $wpdb->query( 'DELETE FROM '.$wpdb->prefix.$table.' WHERE post_id = '.$post_ID );
    }
}
function findBy( $table, $post_ID ){
    global $wpdb;
    $aObjects = $wpdb->get_results( 'SELECT * FROM '.$wpdb->prefix.$table.' WHERE post_id = '.$post_ID );
    return ( empty( $aObjects ) ) ? null : $aObjects[0];
}
// End deleting

// After insert post
function on_post_import( $post_ID ) {
    foreach( Config::$data as $v ){
        $object = findBy( $v[ 'name' ], $post_ID );
        if( $object ){
            $options = array(
                'controller' => $v[ 'controller' ],
                'action'     => $v[ 'action' ]
            );
            $adminUrl  = MvcRouter::admin_url($options);
            $adminUrl .= '&flash=success';
            wp_redirect( $adminUrl, 301 );
            exit; 
        }
    }
}
add_action( 'wp_insert_post', 'on_post_import' );
// End After insert post

//Remove on menu Notaire action add
MvcConfiguration::append(array(
    'AdminPages' => array(
        'notaires' => array(
            'delete',
            'edit'
        )
    )
));
//End remove