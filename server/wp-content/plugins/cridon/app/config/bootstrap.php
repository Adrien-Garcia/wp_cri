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
    $modelConf = getRelatedContentConfInReferer($post_ID);
    if (!empty($modelConf)) {
        if( ($model = findBy( $modelConf['name'], $post_ID )) == null ){//no duplicate
            $model = insertInTable( $modelConf['name'], $post_ID );
        }
        updateRelatedContent( $model ,array(
            'id_matiere' => $_POST['cri_category']
        ));
    }
    return $post_ID;
}

/**
 * For current request, check if it's a MVC kind of post
 * @param $post_ID int ID used to know if the current content is an MVC one
 * @return mixed conf array or false if not found
 */
function getRelatedContentConfInReferer($post_ID) {
    if( $_POST[ 'post_type' ] == 'post' && !wp_is_post_revision( $post_ID ) ) {
        if (isset($_POST['_wp_http_referer'])) {
            $http = explode('cridon_type=', $_POST['_wp_http_referer']);
            if (count($http) == 2) {
                if (isset(Config::$data[$http[1]])) {
                    return Config::$data[ $http[ 1 ] ];
                }
            }
        }
    }
    return false;
}

add_action('save_post','save_post_in_table');

function insertInTable( $table,$post_ID ){
    global $wpdb;
    $wpdb->query( 'INSERT INTO '.$wpdb->prefix.$table.'(post_id) VALUE('.$post_ID.')' );
    //UI Component
    afterInsertModel( $table,$wpdb->insert_id );
    //End UI
    //Category managment
    if( isset( $_POST['cri_category'] ) && !empty( $_POST['cri_category'] ) ){
        updateVeille( $wpdb->insert_id, $_POST['cri_category'] );        
    }
    //End category managment
    return findBy( $table, $post_ID );
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
//End deleting

/**
 * Return MvcModel corresponding to the specific kind of post
 * @param $table
 * @param $post_ID
 * @return null
 */
function findBy( $table, $post_ID ){
    $config = assocToKeyVal(Config::$data, 'name', 'model');
    /**
     * @var $model MvcModel
     */
    $model = mvc_model($config[$table]);
    $mvcObject = $model->find_one_by_post_id($post_ID);
    return $mvcObject;
}

// After insert post
function on_post_import( $post_ID ) {
    $modelConf = getRelatedContentConfInReferer($post_ID);
    if (!empty($modelConf)) {
        $object = findBy( $modelConf['name'], $post_ID );
        if( $object ){
            $options = array(
                'controller' => $modelConf[ 'controller' ],
                'action'     => $modelConf[ 'action' ]
            );
            $adminUrl  = MvcRouter::admin_url($options);
            $adminUrl .= '&flash=success';
            wp_redirect( $adminUrl, 302 );
            exit; 
        }
    }
}
add_action( 'wp_insert_post', 'on_post_import' );
// End After insert post


// Category managment
add_action('add_meta_boxes','init_meta_boxes_category_post');

function init_meta_boxes_category_post(){
    if( isset( $_GET['cridon_type'] ) && in_array($_GET['cridon_type'], Config::$contentWithMatiere)) {//Check if is a model Veille
        // init meta box depends on the current type of content
        add_meta_box('id_meta_boxes_link_post', Config::$titleMetabox , 'init_select_meta_boxes', 'post', 'side', 'high', $_GET['cridon_type']);
    }
}
/**
 * Init metabox if it'a model Veille
 * 
 * @param \WP_Post $post
 */
function init_select_meta_boxes( $post, $args ){
    //args contains only one param : key to model name using config
    $models = $args['args'];
    $config = arrayGet(Config::$data, $models, reset(Config::$data));
    $oModel  = findBy( $config['name'] , $post->ID );//Find Current model
    $oMatiere = mvc_model( 'matiere' );//load model Matiere to use functions
    $aMatiere = $oMatiere->find( array( 'order' => 'label ASC' ) );

    // prepare vars
    $vars = array(
        'aMatiere' => $aMatiere,
        'oModel' => $oModel
    );

    // render view
    CriRenderView('matiere_meta_box', $vars);
}

/**
 * Check if current Model has an associate model Matiere
 * 
 * @param object $needle Object MvcModel
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
 * Update table content related with post
 * 
 * @param MvcModelObject $object Related content
 * @param array $postFields related fields
 */
function updateRelatedContent( $object, $postFields ){
    $class = $object->__model_name;
    /**
     * @var MvcModel $class
     */
    $model = mvc_model($class);
    //Assume pk will always be "id". There's no way to get it dynamically...
    $model->update($object->id, $postFields );//Using WP_MVC to update model
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

add_action( 'admin_init', 'init_custom_capabilities',99);
function init_custom_capabilities(){
    $roles = get_option('cri_user_roles');
    if( !empty( $roles ) ){
        foreach ( $roles as $k=>$v ){
            $role = get_role($k);
            foreach( Config::$capabitilies as $capability ){
                if ( !$role->has_cap( $capability ) ) {//check capability is already true
                    $role->add_cap( $capability,false );
                }
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
    if( empty( $roles ) || in_array( CONST_ADMIN_ROLE, $roles ) ){
        return;
    }
    foreach( $capabilities as $key => $value ){
        if( !$value ){//unchecked
            continue;
        }
        $tmp = explode( '-cridon',$key );//get custom capability
        if( !empty( $tmp ) && isset( $tmp[1] ) ){
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

//Admin User Cridon


add_action('user_register', 'register_extra_fields');
function register_extra_fields ($user_id)
{
    global $wp_roles;
    if ( !isset( $wp_roles ) ) {
        $wp_roles = new WP_Roles();
    }
    $user_info = get_userdata($user_id);
    $roles = $user_info->roles;
    if( !empty( $roles ) ){
        $model = mvc_model( 'UserCridon' );
        $data = array(
            'UserCridon' => array(
                'id_erp' => ( isset( $_POST['id_erp'] ) ) ? $_POST['id_erp'] : '',
                'profil' => empty( $roles )? '' : translate_user_role( $wp_roles->roles[$roles[0]]['name'] ),
                'id_wp_user' => $user_id
            )
        );
        //Insert into DB
        $model->create( $data );
        $options = array(
            'controller' => 'user_cridons',
            'action'     => 'index'
        );
        $adminUrl  = MvcRouter::admin_url($options);
        $adminUrl .= '&flash=success';
        //Redirect to UserCridon list
        wp_redirect( $adminUrl, 302 );
        exit;
    }
}

function generateHtmlForUserForm( $user ){
    $id_erp = '';
    $last_connection = '00-00-0000 00:00';
    if( !empty( $user ) && ( $user instanceof WP_User ) ){
        $model = mvc_model( 'UserCridon' );
        //Check if UserCridon exist
        $userCridon = $model->find_one_by_id_wp_user($user->ID);
        if( !empty( $userCridon ) ){
            $id_erp = $userCridon->id_erp;
            if( $userCridon->last_connection !== '0000-00-00 00:00:00' ){
                $dt = new DateTime( $userCridon->last_connection );
                $last_connection = $dt->format('d-m-Y H:i');
            }
        }
    }

    // prepare vars
    $vars = array(
        'id_erp'            => $id_erp,
        'last_connection'   => $last_connection,
        'user'              => $user
    );
    // render view
    CriRenderView('user_form', $vars);

}
add_action( "user_new_form_tag", "add_new_field_to_useradd" );
function add_new_field_to_useradd()
{
    generateHtmlForUserForm( null );
}
add_action ( 'edit_user_profile', 'custom_extra_profile_fields' );
function custom_extra_profile_fields( $user )
{
    generateHtmlForUserForm( $user );
}

add_action( 'profile_update', 'custom_save_extra_profile_fields', 10, 2 );

function custom_save_extra_profile_fields( $user_id, $old_user_data ) {
    global $wp_roles;
    if ( !isset( $wp_roles ) ) {
        $wp_roles = new WP_Roles();
    }
    if ( !current_user_can( 'edit_user', $user_id ) ){
        return false;
    }
    $model = mvc_model( 'UserCridon' );
    $userCridon = $model->find_one_by_id_wp_user( $user_id );
    $user_info = get_userdata($user_id);
    $roles = $user_info->roles;
    if( !empty( $userCridon ) && !empty( $roles ) ){
        $data = array(
            'UserCridon' => array(
                'id' => $userCridon->id,
                'id_erp' => ( isset( $_POST['id_erp'] ) ) ? $_POST['id_erp'] : '',
                'profil' => translate_user_role( $wp_roles->roles[ $roles[0] ]['name'] )
            )
        );
        $model->save( $data );
        $options = array(
            'controller' => 'user_cridons',
            'action'     => 'index'
        );
        $adminUrl  = MvcRouter::admin_url($options);
        $adminUrl .= '&flash=success&action_referer=edit';
        //Redirect to UserCridon list
        wp_redirect( $adminUrl, 302 );
        exit;
    }
}
function custom_after_login( $user_login, $user ) {
    if ( ( $user instanceof WP_User ) && isset( $user->roles ) && is_array( $user->roles ) ) {
        // check not notaire
        if ( !in_array( CONST_NOTAIRE_ROLE, $user->roles ) ) {
            $model = mvc_model( 'UserCridon' );
            //Check if UserCridon exist
            $userCridon = $model->find_one_by_id_wp_user($user->ID);
            //Update last_connection
            if( !empty( $userCridon ) ){
                $dt = new DateTime('now');
                $data = array(
                    'UserCridon' => array(
                        'id' => $userCridon->id,
                        'last_connection' => $dt->format('Y-m-d H:i:s'),
                    )
                );
                //Update DB
                $model->save( $data );
            }
        }
    }
}
add_filter( 'wp_login', 'custom_after_login', 10, 2 );

function custom_delete_user( $user_id ) {
    $model = mvc_model( 'UserCridon' );
    //Check if UserCridon exist
    $userCridon = $model->find_one_by_id_wp_user( $user_id );
    if( !empty( $userCridon ) ){
        $qb = new QueryBuilder();
        //Delete user cridon
        $qb->delete( array( 'table' => 'user_cridon', 'conditions' => 'id='.$userCridon->id ) );
        $options = array(
            'controller' => 'user_cridons',
            'action'     => 'index'
        );
        $adminUrl  = MvcRouter::admin_url($options);
        $adminUrl .= '&flash=success&action_referer=delete';
        //Redirect to UserCridon list
        wp_redirect( $adminUrl, 302 );
        exit;
    }
}
add_action( 'deleted_user', 'custom_delete_user' );
//End Admin User Cridon

// Custom usefull functions
/**
 * Get a value associated with a key in array if exists, default value otherwise
 * Avoid warning and easily allow fallback
 * @param mixed $key searched key
 * @param array $array in which the key should be
 * @param mixed $default value to retrieve if key is not in array
 * @return mixed $value corresponding to $key if exists, $defaults otherwise
 */
function arrayGet($array = array(), $key = 0, $default = null) {
    return isset($array[$key]) ? $array[$key] : $default;
}

/**
 * Converts a multi-dimensional associative array into an array of key => values with the provided field names
 * based on FuelPHP class Arr
 * @param   array   $assoc      the array to convert
 * @param   string  $key_field  the field name of the key field
 * @param   string  $val_field  the field name of the value field
 * @return  array
 * @throws  \InvalidArgumentException
 */
function assocToKeyVal($assoc, $key_field, $val_field)
{
    if ( ! is_array($assoc))
    {
        throw new \InvalidArgumentException('The first parameter must be an array.');
    }
    $output = array();
    foreach ($assoc as $row)
    {
        if (isset($row[$key_field]) and isset($row[$val_field]))
        {
            $output[$row[$key_field]] = $row[$val_field];
        }
    }
    return $output;
}

/**
 * Render custom view  
 *
 * @param string $path
 * @param array $view_vars
 */
function CriRenderView($path, $view_vars) {
    extract($view_vars);
    require_once WP_PLUGIN_DIR . '/cridon/app/views/custom/' . $path . '.php';
}

//End custom functions

/**
 * Send email for error reporting
 *
 * @param string $message the default message in which we want to add the error
 * @param string $error the error to introduce in the message
 */
function reportError($message, $object) {
    // message content
    $message =  sprintf($message, $object);
    $env = getenv('ENV');
    //define receivers
    if ((empty($env) || ($env !== 'PROD')) && !empty(Config::$emailNotificationError['cc'])) {
        // just send to client in production mode
        $ccs = (array) Config::$emailNotificationError['cc']; //cast to guarantee array
        $to = array_pop($ccs);
    } else {
        $to = arrayGet(Config::$emailNotificationError, 'to', CONST_EMAIL_ERROR_CONTACT);
    }
    $headers = array();
    if (!empty(Config::$emailNotificationError['cc'])) {
        foreach ((array) Config::$emailNotificationError['cc'] as $cc) {
            $headers[] = 'Cc: '.$cc;
        }
    }

    // send email
    wp_mail($to, CONST_EMAIL_ERROR_SUBJECT, $message, $headers);
}

//UI component
add_action('add_meta_boxes','init_meta_boxes_ui_component');

function init_meta_boxes_ui_component(){
    if( isset( $_GET['cridon_type'] ) ){
        add_meta_box('id_ui_meta_boxes', Config::$titleMetaboxDocument , 'init_ui_meta_boxes', 'post', 'normal');       
    }
}

function init_ui_meta_boxes( $post ){
    global $cri_container;
    $container = $cri_container->get('ui_container');
    $container->setTitle('');
    $current = null;
    foreach ( Config::$data as $v ){
        if( $v['value'] == $_GET['cridon_type'] ){
            $current = $v;break;
        }
    }
    if( !empty( $current ) && !empty( $post ) ){
        $obj = findBy( $current[ 'name' ], $post->ID );
        $container->setModel(mvc_model($current[ 'model' ]));
        $cls = new stdClass();
        $cls->id = $obj->id;
        $container->setObject($cls);
    }
    $container->create();
}

function after_save_post_for_ui( $post_ID ){ 
    if( $_POST[ 'post_type' ] == 'post' && !wp_is_post_revision( $post_ID ) ){
        if( isset( $_POST[ '_wp_http_referer' ] ) ){
            $http = explode( 'cridon_type=', $_POST[ '_wp_http_referer' ] );
            if( count( $http ) == 2 ){
                if( isset( Config::$data[ $http[ 1 ] ] ) ){
                    $obj = findBy( Config::$data[ $http[ 1 ] ][ 'name' ], $post_ID );
                    if( $obj == null ){//no duplicate
                        
                    }else{
                        $cls = new stdClass();
                        $cls->id = $obj->id;
                        saveDocumentsFromUI(Config::$data[ $http[ 1 ] ][ 'model' ], $cls);
                    }
                }
            }
        }
    }
    return $post_ID;
}
add_action('save_post','after_save_post_for_ui');
function saveDocumentsFromUI( $model,$obj ){
    global $cri_container;
    $ui_container = $cri_container->get('ui_container');
    $ui_container->setModel(mvc_model( $model ) );
    $ui_container->setObject($obj);
    $ui_container->save();
}

function afterInsertModel( $table,$lastID ){
    foreach( Config::$data as $v ){
        if( $v['name'] == $table ){
            $cls = new stdClass();
            $cls->id = $lastID;
            saveDocumentsFromUI($v['model'], $cls);
            break;
        }
    }
}
//End UI Component

/**
 * Permet d'écrire des logs complets avec backtrace si besoin.
 * @param $variable mixed : variable à inscrire dans les logs
 * @param $log_file string : Nom du fichier dans le dossier de log Oxid
 * @param $backtrace mixed : nombre de lignes de backtrace à ajouter.
 *      'true' correspond à 10 lignes + option provide object (full debug)
 */
function writeLog($variable, $log_file = 'log.txt', $backtrace = 0) {

    if (is_a($variable, 'Exception')) {
        /**
         * @var $exception Exception
         */
        $message = $variable->getMessage();
    } elseif ( gettype( $variable ) != 'string' ) {
        $message = var_export( $variable, true);
    } else {
        $message = $variable;
    }

    if ($backtrace) {
        $message .= "\n".print_r(debug_backtrace($backtrace === true ? DEBUG_BACKTRACE_PROVIDE_OBJECT : DEBUG_BACKTRACE_IGNORE_ARGS, $backtrace === true ? 10 : $backtrace), TRUE);
    }

    $sLogMsg = "------------[" . date("Y-m-d H:i:s") . "]-------------------\n{$message}\n";

    $handle = @fopen($log_file, "a+");
    if($handle) {
        @fwrite($handle, $sLogMsg);
        @fclose($handle);
    }
}