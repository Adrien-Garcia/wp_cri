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
        $isInsert = false; //Control variable
        if( isset( $_POST[ '_wp_http_referer' ] ) ){
            $http = explode( 'cridon_type=', $_POST[ '_wp_http_referer' ] );
            if( count( $http ) == 2 ){
                if( isset( Config::$data[ $http[ 1 ] ] ) ){
                    if( findBy( Config::$data[ $http[ 1 ] ][ 'name' ], $post_ID ) == null ){//no duplicate
                        insertInTable( Config::$data[ $http[ 1 ] ][ 'name' ],$post_ID );    
                        $isInsert = true;
                    }
                }
            }
        }
        //Category managment
        if( isset( $_POST['cri_category'] ) && !$isInsert ){
            $oVeille  = findBy( 'veille' , $post_ID );
            if( $oVeille ){//Update model Veille
                updateVeille( $oVeille->id ,$_POST['cri_category'] );
            }
        }
        //End category managment
    }
    return $post_ID;
}
add_action('save_post','save_post_in_table');

function insertInTable( $table,$post_ID ){
    global $wpdb;
    $wpdb->query( 'INSERT INTO '.$wpdb->prefix.$table.'(post_id) VALUE('.$post_ID.')' );
    //Category managment
    if( isset( $_POST['cri_category'] ) && !empty( $_POST['cri_category'] ) ){
        updateVeille( $wpdb->insert_id, $_POST['cri_category'] );        
    }
    //End category managment
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


// Category managment
add_action('add_meta_boxes','init_meta_boxes_category_post');

function init_meta_boxes_category_post(){
    if( isset( $_GET['cridon_type'] ) && ( $_GET['cridon_type'] === 'veilles' ) ){//Check if is a model Veille
        add_meta_box('id_meta_boxes_link_post', Config::$titleMetabox , 'init_select_meta_boxes', 'post', 'side', 'high');        
    }
}
/**
 * Init metabox if it'a model Veille
 * 
 * @param \WP_Post $post
 */
function init_select_meta_boxes( $post ){ 
    echo '<select name="cri_category">';
    $oVeille  = findBy( 'veille' , $post->ID );//Find Veille
    $oMatiere = mvc_model( 'matiere' );//load model Matiere to use functions
    $aMatiere = $oMatiere->find( array( 'order' => 'label ASC' ) );
    foreach( $aMatiere as $value ){
        echo '<option'.check( $oVeille,$value ).' value="'.$value->id.'">'.$value->label.'</option>';  
    }
    echo '</select>';
}
/**
 * Check if Veille has an associate model Matiere
 * 
 * @param object $needle Object Veille
 * @param object $haystack Object Matiere
 * @return string|null
 */
function check( $needle ,$haystack ){
    if( !$needle ){
        if( $haystack->id == Config::$defaultMatiere['id'] ){
            return ' selected="selected" ';
        }
    }
    return ( ( $needle ) && ( $needle->id_matiere === $haystack->id ) ) ? ' selected="selected" ' : '';
}

/**
 * Update table Veille with Matiere Id
 * 
 * @param integer $id
 * @param integer $category
 */
function updateVeille( $id,$category ){
    $oVeille = mvc_model( 'veille' );
    $data = array(
        'Veille' => array(
            'id' => $id,
            'id_matiere' => $category
        )
    );
    $oVeille->save( $data );//Using WP_MVC to update model
}
// End Category managment

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
        
// Workflow

add_action( 'admin_init', 'init_custom_capabilities');
function init_custom_capabilities(){
    global $wp_roles;    
    foreach ( $wp_roles->role_objects as $role ){
        foreach( Config::$capabitilies as $capability ){
            if ( !$role->has_cap( $capability ) ) {//check capability is already true
                $role->add_cap( $capability,false );
            }
        }
    }    
}
if ( is_admin() ) {//only in admin
    checkUserAuthorization();
}
function checkUserAuthorization(){
    $user = wp_get_current_user();
    $capabilities = $user->get_role_caps();//Get user capability
    $aIndex = $aEdit = $aAdd = $aDelete = array();
    $roles = $user->roles;//get roles
    //If user is an administrator, he has full control
    if( empty( $roles ) || $roles[0] == 'administrator' ){        
        return;
    }
    foreach( $capabilities as $key => $value ){ 
        if( !$value ){//unchecked
            continue;
        }
        $tmp = explode( '-cridon',$key );//get custom capability
        if( !empty( $tmp ) ){
            //Listing
            if( preg_match('|liste-([a-zA-Z_-]+)|', $tmp[0], $matches ) ){
                $aIndex[] = $matches[1];
            } 
        }
    }
    $listRolesByCtrl = array();
    if( empty( $aIndex ) ){
        return;
    }
    foreach( $aIndex as $index ){
        $controller = $index.'s';
        if( $index == 'flash' ){//Name controller exception
            $controller = 'flashes';
        }
        //Set role of controller        
        $listRolesByCtrl[$controller] = $roles[0];
    }
    //Admin menu page generate with WP_MVC
    MvcConfiguration::append(array(
        'admin_controller_capabilities'=>$listRolesByCtrl
    ));
}

// End workflow