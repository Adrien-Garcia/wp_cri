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
function save_post_in_table( $post_ID, $post ){
    $modelConf = getRelatedContentConfInReferer($post_ID);
    $isInsert = false;
    if (!empty($modelConf)) {
        if( ($model = findBy( $modelConf['name'], $post_ID )) == null ){//no duplicate
            $model = insertInTable( $modelConf['name'], $post_ID );
            $isInsert = true;
        }
        $aAdditionalFields = array();
        if (!empty($_POST['cri_category'])) {
            $aAdditionalFields['id_matiere'] = $_POST['cri_category'];
        }
        if (!empty($_POST['id_parent'])) {
            $aAdditionalFields['id_parent'] = $_POST['id_parent'];
        }
        if (isset($_POST['custom_post_date'])) {
            $aAdditionalFields['custom_post_date'] = $_POST['custom_post_date'];
        }
        if (isset($_POST['address'])) {
            $aAdditionalFields['address'] = $_POST['address'];
        }
        if (isset($_POST['postal_code'])) {
            $aAdditionalFields['postal_code'] = $_POST['postal_code'];
        }
        if (isset($_POST['town'])) {
            $aAdditionalFields['town'] = $_POST['town'];
        }
        if (!empty($_POST['cri_post_level'])) {
            $aAdditionalFields['level'] = $_POST['cri_post_level'];
        }
        updateRelatedContent( $model , $aAdditionalFields);
        //Only on insert and post status is publish
        if( $isInsert && ( $post->post_status == 'publish' ) && ( $post->post_type == 'post' ) ){
            sendNotificationForPostPublished($post, $model);
        }
    }
    return $post_ID;
}

/**
 * For current request, check if it's a MVC kind of post
 * @param $post_ID int ID used to know if the current content is an MVC one
 * @return mixed conf array or false if not found
 */
function getRelatedContentConfInReferer($post_ID) {
    if( isset($_POST[ 'post_type' ]) && $_POST[ 'post_type' ] == 'post' && !wp_is_post_revision( $post_ID ) ) {
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

add_action('save_post','save_post_in_table',10,2);

function insertInTable( $table,$post_ID ){
    global $wpdb;
    $wpdb->query( 'INSERT INTO '.$wpdb->prefix.$table.'(post_id) VALUE('.$post_ID.')' );
    //UI Component
    afterInsertModel( $table,$wpdb->insert_id );
    //End UI
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


// Parent management
add_action('add_meta_boxes','init_meta_boxes_parent');

function init_meta_boxes_parent(){
    if( isset( $_GET['cridon_type'] ) && in_array($_GET['cridon_type'], Config::$contentWithParent)) {
        // init meta box depends on the current type of content
        add_meta_box('id_meta_boxes_parent_select', Config::$titleParentMetabox , 'init_parent_meta_boxes', 'post', 'side', 'high', $_GET['cridon_type']);
    }
}
/**
 * Init metabox if it'a model Veille
 *
 * @param \WP_Post $post
 */
function init_parent_meta_boxes( $post, $args ){
    //args contains only one param : key to model name using config
    $models = $args['args'];
    $config = arrayGet(Config::$data, $models, reset(Config::$data));
    $oModel  = findBy( $config['name'] , $post->ID );//Find Current model
    $oParent = mvc_model( $config['model'] );//load model Matiere to use functions
    $aQueryOptions = array(
        'selects' => array('Post.post_title', $config['model'].'.*'),
        'order' => 'Post.post_title ASC',
        'conditions' => array(
            $config['model'].'.id_parent' => null
        ),
        'joins' => array('Post')
    );
    if (!empty($oModel->id)) {
        $aQueryOptions['conditions'][$config['model'].'.id != '] = $oModel->id;
    }
    $aParent = $oParent->find( $aQueryOptions );

    // prepare vars
    $vars = array(
        'aParent' => $aParent,
        'oModel' => $oModel
    );

    // render view
    CriRenderView('parent_meta_box', $vars);
}

// End parent management

// Category managment
add_action('add_meta_boxes','init_meta_boxes_category_post');

function init_meta_boxes_category_post(){
    if( isset( $_GET['cridon_type'] ) && in_array($_GET['cridon_type'], Config::$contentWithMatiere)) {
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
 * Check if current Model must be checked in a select meta box
 * 
 * @param object $needle Object MvcModel
 * @param object $haystack Object MvcModel
 * @param string $property property to test on needle
 * @return string|null
 */
function check( $needle ,$haystack, $property = 'id_matiere' ){
    if( !$needle && ($property == 'id_matiere')){
        if( $haystack->id == Config::$defaultMatiere['id'] ){
            return ' selected="selected" ';
        }
    }
    return ( ( $needle ) && ( $needle->{$property} === $haystack->id ) ) ? ' selected="selected" ' : '';
}

/**
 * Update table content related with post
 * 
 * @param MvcModelObject $object Related content
 * @param array $postFields related fields
 */
function updateRelatedContent( &$object, $postFields ){
    $class = $object->__model_name;
    /**
     * @var MvcModel $class
     */
    $model = mvc_model($class);
    //Assume pk will always be "id". There's no way to get it dynamically...
    $model->update($object->id, $postFields );//Using WP_MVC to update model
}
// End Category managment


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
    require_once ABSPATH . WPINC . '/pluggable.php';
    $user = wp_get_current_user();
    $capabilities = $user->get_role_caps();//Get user capability
    $aIndex = $aEdit = $aAdd = $aDelete = array();
    $roles = $user->roles;//get roles
    //If user is an administrator, he has full control
    if( empty( $roles ) || in_array( CONST_ADMIN_ROLE, $roles ) ){
        setAdminbarTranslation(Config::$listOfControllersWpMvcOnSidebar);//translate to french
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
    $controllers = array();//list of controller
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
        $controllers[] = $controller;//add to list
    }
    //Admin menu page generate with WP_MVC
    MvcConfiguration::append(array(
        'admin_controller_capabilities'=>$listRolesByCtrl
    ));
    setAdminbarTranslation($controllers);//translate to french
}

// End workflow
//Sidebar admin translation
function setAdminbarTranslation( $controllers ){
    foreach( $controllers as $ctrl ){
        $actions = Config::$sidebarAdminMenuActions;
        if( in_array($ctrl,Config::$listOfControllersWithNoActionAdd )){
            if( isset( $actions['add'] ) ){
                unset($actions['add']);
            }
        }
        MvcConfiguration::append(array(
                'AdminPages' => array(
                    $ctrl => $actions
                )
            )
        );
    }
}
//End Sidebar admin translation
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
 * @param array $array in which the key should be
 * @param mixed $key searched key
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
 * Search for the specified file
 * returns FALSE if not found, file name in the correct form otherwise
 * (avoid problems with case when using file_exists)
 * @param string    $fileName
 * @param bool|true $caseSensitive
 *
 * @return mixed file name OR false
 */
function fileExists($fileName, $caseSensitive = true) {

    if(file_exists($fileName)) {
        return $fileName;
    }
    if($caseSensitive) return false;

    // Handle case insensitive requests
    $directoryName = dirname($fileName);
    $fileArray = glob($directoryName . '/*', GLOB_NOSORT);
    $fileNameLowerCase = strtolower($fileName);
    foreach($fileArray as $file) {
        if(strtolower($file) == $fileNameLowerCase) {
            return $file;
        }
    }
    return false;
}

/**
 * Render custom view
 *
 * @param string $path
 * @param array $view_vars
 * @param string $folder
 * @param boolean $echo
 *
 * @return string
 */
function CriRenderView($path, $view_vars, $folder = "custom", $echo = true) {
    if (!$echo) {
        ob_start();
    }
    extract($view_vars);
    require WP_PLUGIN_DIR . '/cridon/app/views/' . $folder . '/' . $path . '.php';
    if (!$echo) {
        return ob_get_clean();
    }
}

//End custom functions

/**
 * Send email for error reporting
 *
 * @param string $message the default message in which we want to add the error
 * @param string $object the error to introduce in the message
 * @param string $subject the subject of the sent mail
 */
function reportError($message, $object, $subject = CONST_EMAIL_ERROR_SUBJECT) {
    $to = arrayGet(Config::$emailNotificationError, 'to', CONST_EMAIL_ERROR_CONTACT);
    // send email
    return sendMail($to,$subject,$message,$object,Config::$emailNotificationError['cc']);
}
/**
 * Send email for reporting
 *
 * @param string $message the default message in which we want to add the error
 * @param mixed $object the error to introduce in the message
 * @param array $ccs 
 */
function sendNotification($message, $object, $ccs = array() ) {
    $to = arrayGet(Config::$emailNotificationEmptyDocument, 'to', Config::$emailNotificationEmptyDocument['to']);
    // send email
    return sendMail($to,Config::$emailNotificationEmptyDocument['subject'],$message,$object,$ccs);
}

/**
 * Send email
 * 
 * @param string $to
 * @param string $subject
 * @param mixed $message
 * @param string $object
 * @param array $ccs
 * @return boolean
 */
function sendMail( $to,$subject,$message, $object, $ccs = array()){
    // message content
    $message =  sprintf($message, $object);
    $env = getenv('ENV');
    //define receivers
    if ((empty($env) || ($env !== 'PROD')) && !empty($ccs)) {
        // just send to client in production mode
        $ccs = (array) $ccs; //cast to guarantee array
        $to = array_pop($ccs);
    }
    $headers = array();
    if (!empty($ccs)) {
        foreach ((array) $ccs as $cc) {
            $headers[] = 'Cc: '.$cc;
        }
    }
    return wp_mail($to, $subject, $message, $headers);
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
        $container->setModel($current[ 'model' ]);
        if( $obj ){
            $cls = new stdClass();
            $cls->id = $obj->id;
            $container->setObject($cls);
        }
    }
    $container->create();
}

function after_save_post_for_ui( $post_ID ){ 
    if( isset($_POST[ 'post_type' ]) && $_POST[ 'post_type' ] == 'post' && !wp_is_post_revision( $post_ID ) ){
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
    $ui_container->setModel($model);
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

    $log_file = CONST_LOG_ERROR_DIR.DIRECTORY_SEPARATOR.$log_file;

    $handle = fopen($log_file, "a+");
    if($handle) {
        fwrite($handle, $sLogMsg);
        fclose($handle);
    }
}

//Notification for published post
function sendNotificationForPostPublished( $post,$model ){
    //for using get_the_content() with all hook used in front
    global $pages,$page ;
    $pages = array($post->post_content);
    $page = 1;
    $title = $post->post_title;
    $date  = get_the_date('d M Y',$post->ID);

    $excerpt = wp_trim_words(strip_tags($post->post_excerpt), 30);
    $content = get_the_content();
    //$model don't contain Matiere
    //It's necessary to get it again
    $completeModel = mvc_model($model->__model_name)->find_one_by_id($model->id);
    $documentModel = mvc_model('Document');
    $documents = false;
    $class = $model->__model_name;
    if (property_exists($completeModel, 'documents') || method_exists($class, "getDocuments")) {
        if (property_exists($completeModel, 'documents')){
            $documents = $completeModel->documents;
        } else {
            $documents = $class::getDocuments($model->id);
        }
    }
    $matiere = (!empty($completeModel) && !empty($completeModel->matiere)) ? $completeModel->matiere : false;
    $permalink = generateUrlByModel($model);
    $tags = get_the_tags( $post->ID );
    $subject  = sprintf(Config::$mailBodyNotification['subject'], $title );

    //writeLog($post, "mailog.txt");

    $vars = array(
        "documentModel" => $documentModel,
        "model" => strtolower( $model->__model_name ),
        "documents" => $documents,
        "title" => $title,
        "date" => $date ,
        "excerpt" => $excerpt,
        "content" => $content,
        "matiere" => $matiere,
        "permalink" => $permalink,
        "tags" => $tags,
        "post" => $post,
    );

    //writeLog($vars, "mailog.txt");



    $message = CriRenderView('mail', $vars,'custom', false);
    $headers = array('Content-Type: text/html; charset=UTF-8');


    /**
     * type = 1 => all notaries
     * type = 0 => subscribers notaries ( veille )
     */
    $type = checkTypeNofication($completeModel);
    if( $type == 1 ){
        //all notaries
        $options = array (
            'conditions' => array(
                'u.user_status' => CONST_STATUS_ENABLED
            ),
             'synonym' => 'n',
             'join' => array(
                array(
                    'table'  => 'users u',
                    'column' => ' n.id_wp_user = u.id'
                ),
             )
         );
        $notaires = mvc_model('QueryBuilder')->findAll('notaire', $options, 'n.id');
    }elseif( $type == 0 ){
        $notaires = getNotariesByMatiere($completeModel);
    }else{
        return false;//Don't send notification
    }
    $env = getenv('ENV');
    
    if (empty($env)|| ($env !== 'PROD')) {
        if ($env === 'PREPROD'){
            $dest = Config::$notificationAddressPreprod;
        } else {
            $dest = Config::$notificationAddressDev;
        }
        $mail = wp_mail( $dest , $subject, $message, $headers );
        writeLog("not Prod: " . $mail . "\n", "mailog.txt");
    } elseif( !empty( $notaires ) ){
        foreach( $notaires as $notaire ){
            if( isset($notaire->email_adress ) ){
                wp_mail( $notaire->email_adress , $subject, $message, $headers );
            }
        }
    }
}

function generateUrlByModel( $model ){
    if( empty( $model ) ){
        return '';
    }
    $options = array(
        'controller' => MvcInflector::tableize(strtolower($model->__model_name)),
        'action'     => ( !isset( $model->id ) ) ? 'index' : 'show'        
    );
    if( isset( $model->id ) ){
        $options['id'] = $model->post->post_name;
    }
    return MvcRouter::public_url($options);
}

function checkTypeNofication( $model ){
    if( in_array(strtolower($model->__model_name),Config::$notificationForAllNotaries ) ){
        return 1;//All notaries
    }
    if( in_array(strtolower($model->__model_name),Config::$notificationForSubscribersNotaries ) ){
        return 0;//Subscribers notaries
    }
    return -1;//Don't send notification
}

function getNotariesByMatiere( $model ){
    $options = array(
        'fields'  => 'n.*',
        'synonym' => 'mn',
        'join' => array(
            array(
                'table' => 'notaire n',
                'column' => 'n.id = mn.id_notaire'
            ),
            array(
                'table'  => 'users u',
                'column' => ' n.id_wp_user = u.id'
            ),
        ),
        'conditions' => array(
            'mn.id_matiere' => $model->id_matiere,
            'u.user_status' => CONST_STATUS_ENABLED
        )
    );
    $notaires = mvc_model('QueryBuilder')->findAll( 'matiere_notaire',$options,'n.id' );
    $emails = array();
    foreach($notaires as $notaire){
        $emailAddress = trim($notaire->email_adress);
        if (!empty($emailAddress) && mvc_model('Veille')->userCanAccessSingle($model, $notaire)) {
            $emails[] = $notaire->email_adress;
        }
    }
    return array_unique($emails);
}
//End Notification for published post


/**
 * CSS pour les formulaires en admin des modèles WP_MVC
 */
function loadAdminCustomCss(){
    wp_register_style( 'mvcform-style-css', plugins_url('cridon/app/public/css/form-style.css'), false ); 
    wp_enqueue_style( 'mvcform-style-css' );
}

// Date de formation
add_action('add_meta_boxes','formation_post_date_meta_box');

function formation_post_date_meta_box(){
    if( isset( $_GET['cridon_type'] ) && in_array($_GET['cridon_type'], Config::$contentWithCustomDate)) {
        // init meta box depends on the current type of content
        add_meta_box('id_meta_boxes_link_post_date', Config::$dateTitleMetabox , 'content_formation_post_date', 'post', 'side', 'high', $_GET['cridon_type']);
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-datepicker');

        wp_enqueue_script('jquery-ui-i18n-fr', plugins_url('cridon/app/public/js/jquery.ui.datepicker-fr.js'), array('jquery-ui-datepicker'));
        wp_register_script( 'formation-js', plugins_url('cridon/app/public/js/bo/formation.js'), array('jquery') );
        wp_enqueue_script('formation-js');
        wp_enqueue_style('jquery-ui-css', plugins_url('cridon/app/public/css/jquery-ui.css'));
    }
}

// Adresse de formation : adresse, code postal & ville
add_action('add_meta_boxes','formation_post_address_meta_box');

function formation_post_address_meta_box(){
    if( isset( $_GET['cridon_type'] ) && in_array($_GET['cridon_type'], Config::$contentWithAddress)) {
        // init meta box depends on the current type of content
        add_meta_box('id_meta_boxes_link_post_address', Config::$addressTitleMetabox['address'], 'content_formation_post_address', 'post', 'side', 'high', $_GET['cridon_type']);
        add_meta_box('id_meta_boxes_link_post_postal_code', Config::$addressTitleMetabox['postal_code'], 'content_formation_post_postal_code', 'post', 'side', 'high', $_GET['cridon_type']);
        add_meta_box('id_meta_boxes_link_post_town', Config::$addressTitleMetabox['town'], 'content_formation_post_town', 'post', 'side', 'high', $_GET['cridon_type']);
    }

}

/**
 * Init metabox
 *
 * @param \WP_Post $post
 */
function content_formation_post_date( $post, $args ){
    //args contains only one param : key to model name using config
    $models = $args['args'];
    $config = arrayGet(Config::$data, $models, reset(Config::$data));
    $oModel  = findBy( $config['name'] , $post->ID );//Find Current model

    // prepare vars
    $vars = array(
        'oModel' => $oModel
    );

    // render view
    CriRenderView('date_meta_box', $vars);
}

/**
 * Init metabox adresse de la formation
 *
 * @param \WP_Post $post
 */
function content_formation_post_address( $post, $args ){
    //args contains only one param : key to model name using config
    $models = $args['args'];
    $config = arrayGet(Config::$data, $models, reset(Config::$data));
    $oModel  = findBy( $config['name'] , $post->ID );//Find Current model

    // prepare vars
    $vars = array(
        'oModel' => $oModel
    );

    // render view
    CriRenderView('address_meta_box', $vars);
}

/**
 * Init metabox code postal de la formation
 *
 * @param \WP_Post $post
 */
function content_formation_post_postal_code( $post, $args ){
    //args contains only one param : key to model name using config
    $models = $args['args'];
    $config = arrayGet(Config::$data, $models, reset(Config::$data));
    $oModel  = findBy( $config['name'] , $post->ID );//Find Current model

    // prepare vars
    $vars = array(
        'oModel' => $oModel
    );

    // render view
    CriRenderView('postal_code_meta_box', $vars);
}

/**
 * Init metabox ville de la formation
 *
 * @param \WP_Post $post
 */
function content_formation_post_town( $post, $args ){
    //args contains only one param : key to model name using config
    $models = $args['args'];
    $config = arrayGet(Config::$data, $models, reset(Config::$data));
    $oModel  = findBy( $config['name'] , $post->ID );//Find Current model

    // prepare vars
    $vars = array(
        'oModel' => $oModel
    );

    // render view
    CriRenderView('town_meta_box', $vars);
}

// Level meta_box
add_action('add_meta_boxes','init_meta_boxes_post_level');

function init_meta_boxes_post_level()
{
    // check if is a post cridon model
    if( isset( $_GET['cridon_type'] ) && in_array($_GET['cridon_type'], Config::$contentWithLevel)) {
        // init meta box depends on the current type of content
        add_meta_box('level_meta_boxes', sprintf(Config::$titleLevelMetabox, MvcInflector::camelize(MvcInflector::singularize($_GET['cridon_type']))) , 'init_select_level_meta_boxes', 'post', 'side', 'high', $_GET['cridon_type']);
    }
}
/**
 * Init metabox for Post Level
 *
 * @param \WP_Post $post
 */
function init_select_level_meta_boxes( $post, $args ){
    //args contains only one param : key to model name using config
    $models = $args['args'];
    $config = arrayGet(Config::$data, $models, reset(Config::$data));
    $oModel  = findBy( $config['name'] , $post->ID );//Find Current model
    $aLevel = array();
    foreach (Config::$listOfLevel as $label => $id) {
        $oLevel        = new \stdClass;
        $oLevel->label = $label;
        $oLevel->id    = $id;

        $aLevel[] = clone $oLevel;
    }
    /**
     * cast type de $oModel::level afin de respecter
     * la comparaison strict imposée par la methode "check"
     * var_dump renvoit en fait un type string pour "$oModel::level" !!!!
     */
    if (is_object($oModel) && property_exists($oModel, 'level')) {
        $oModel->level = (int)$oModel->level;
    }

    // prepare vars
    $vars = array(
        'aLevel' => $aLevel,
        'oModel' => $oModel,
    );

    // render view
    CriRenderView('level_meta_box', $vars);
}

/**
 * @return [] : link value and type of link
 */
function CridonlineAutologinLink()
{
    // default URL when accessing to Cridonline.
    $url = mvc_public_url(array('controller' => 'notaires', 'action' => 'show')) . '?error=FONCTION_NON_AUTORISE';
    $access = 0;
    if (CriIsNotaire() && CriCanAccessSensitiveInfo(CONST_CONNAISANCE_ROLE)) {
        $oNotaire = CriNotaireData();
        $lvl = Config::$authCridonOnline[(int) $oNotaire->etude->subscription_level];
        // Proxy : http://abo.prod.wkf.fr/auth --> SERVER_NAME/wolters
        $url = esc_url_raw('/wolters/autologin.js?'.
            'auth='.$lvl.
            '&cid='.$oNotaire->id.
            '&clname='.$oNotaire->last_name.
            '&cfname='.$oNotaire->first_name.
            '&cemail='.$oNotaire->email_adress.
            '&pid=CRIDON');
        $access = 1;

    }
    return array($access, $url);
}
