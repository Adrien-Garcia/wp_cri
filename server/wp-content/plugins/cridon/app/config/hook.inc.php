<?php
/**
 * Description of hook.inc.php
 * @package wp_cridon
 * @author ETECH
 * @contributor Joelio
 */

require_once WP_PLUGIN_DIR . '/cridon/app/libs/cridon.fileuploader.lib.php';

/**
 * Hook admin menu
 *
 * @param string $parent_file
 *
 * @return string
 */
function hook_admin_menu($parent_file)
{
    global $submenu_file;
    if (isset( $_GET['cridon_type'] ) && $_GET['cridon_type'] ) {
        $submenu_file = 'post-new.php?cridon_type=' . $_GET['cridon_type'];
        $parent_file  = CONST_WPMVC_PREFIX . $_GET['cridon_type'];
    }else{
        //Correction bug WP_MVC
        //Au niveau du menu de l'admin, lors de l'edition d'un modèle il ne s'ouvre pas.
        if( isset( $_GET['page'] ) ){
            //Seulement pour le menu lié à  WP_MVC
            if ( preg_match( '/' . CONST_WPMVC_PREFIX . '(.*)/', $_GET['page'], $match ) ) {
                  if( isset( $match[0] ) ){
                      $tab = explode( '-',$match[0] );
                      if( ( count( $tab ) > 1 ) && ( $tab[1] === 'edit' ) ){//Seulement pour l'édition
                          $parent_file = $tab[0];                          
                      }
                  }
            }            
        }
    }
    return $parent_file;
}
add_filter('parent_file', 'hook_admin_menu');

/**
 * Hook for WP-MCV menu
 */
function custom_admin_menu()
{
    global $submenu;

    foreach ($submenu as $k => $d) {
        if (preg_match('/' . CONST_WPMVC_PREFIX . '(.*)/', $k, $match)) {
            if (in_array($match[1], Config::$mvcWithPostForm)) {
                $submenu[$k][1][2] = 'post-new.php?cridon_type=' . $match[1];
            }
            if (in_array($match[1], Config::$mvcWithUserForm)) {
                $submenu[$k][1][2] = 'user-new.php';
            }
        }
    }
}
add_action( 'admin_menu', 'custom_admin_menu', 100 );

/**
 * Add custom js in template
 */
function append_js_files()
{
    require_once ABSPATH . WPINC . '/pluggable.php';

    // only in front
    if (!is_admin()) {

        $criFileUploader = new CriFileUploader();

        wp_enqueue_script('cridon', plugins_url('cridon/app/public/js/cridon.js'), array('jquery'));
        wp_localize_script(
            'cridon',
            'jsvar',
            array(
                'ajaxurl'                  => admin_url('admin-ajax.php'),
                // connection
                'login_nonce'              => wp_create_nonce("process_login_nonce"),
                'login_error_msg'                => CONST_LOGIN_ERROR_MSG,
                'login_empty_error_msg'          => CONST_LOGIN_EMPTY_ERROR_MSG,
                'login_form_id'                  => CONST_TPL_FORM_ID,
                'login_login_field_id'           => CONST_TPL_LOGINFIELD_ID,
                'login_password_field_id'        => CONST_TPL_PASSWORDFIELD_ID,
                'login_error_bloc_id'            => CONST_TPL_ERRORBLOCK_ID,
                // lost password
                'password_lostpwd_nonce'            => wp_create_nonce("process_lostpwd_nonce"),
                'password_pwdform_id'               => CONST_TPL_PWDFORM_ID,
                'password_pwdmsg_block'             => CONST_TPL_PWDMSGBLOCK_ID,
                'password_email_field_id'           => CONST_TPL_PWDEMAILFIELD_ID,
                'password_crpcen_field_id'          => CONST_TPL_CRPCENFIELD_ID,
                'password_crpcen_error_msg'         => CONST_INVALIDEMAIL_ERROR_MSG,
                'password_crpcen_success_msg'       => CONST_RECOVPASS_SUCCESS_MSG,
                'password_empty_crpcen_msg'         => CONST_CRPCEN_EMPTY_ERROR_MSG,
                // post question
                'question_form_id'         => CONST_QUESTION_FORM_ID,
                'question_nonce'           => wp_create_nonce("process_question_nonce"),
                'question_support'         => CONST_QUESTION_SUPPORT_FIELD,
                'question_matiere'         => CONST_QUESTION_MATIERE_FIELD,
                'question_competence'      => CONST_QUESTION_COMPETENCE_FIELD,
                'question_objet'           => CONST_QUESTION_OBJECT_FIELD,
                'question_message'         => CONST_QUESTION_MESSAGE_FIELD,
                'question_fichier'         => CONST_QUESTION_ATTACHEMENT_FIELD,
                'question_msgblock'        => CONST_QUESTION_SUCCESS_MSG_FIELD,
                'question_content'         => CONST_QUESTION_ACTION_SUCCESSFUL,
                'question_nb_file'         => $criFileUploader::CONST_MAX_FILES,
                'question_nb_file_error'   => sprintf(CONST_QUESTION_MAX_FILES_ERROR, $criFileUploader::CONST_MAX_FILES),
                'question_max_file_size'   => CONST_QUESTION_MAX_FILE_SIZE,
                'question_file_size_error' => sprintf(CONST_QUESTION_FILE_SIZE_ERROR,
                                                      (CONST_QUESTION_MAX_FILE_SIZE / 1000000) . 'M'),
                // newsletter
                'newsletter_form_id'       => CONST_NEWSLETTER_FORM_ID,
                'newsletter_nonce'         => wp_create_nonce("process_newsletter_nonce"),
                'newsletter_msgblock_id'   => CONST_NEWSLETTER_MSGBLOCK_ID,
                'newsletter_user_email'    => CONST_NEWSLETTER_EMAIL_FIELD,
                'newsletter_empty_error'   => CONST_NEWSLETTER_EMPTY_ERROR_MSG,
                'newsletter_success_msg'   => CONST_NEWSLETTER_SUCCESS_MSG,
                'newsletter_email_error'   => CONST_NEWSLETTER_EMAIL_ERROR_MSG,
                
                // cridonline
                'cridonline_nonce'         => wp_create_nonce("process_cridonline_nonce"),
                'cridonline_CGV_error'     => CONST_CRIDONLINE_CGV_ERROR_MSG,

                // collaborateur
                'crud_nonce'                   => wp_create_nonce("process_crud_nonce"),
                'collaborateur_id_function'    => CONST_NOTAIRE_COLLABORATEUR,
                'collaborateur_delete_success' => CONST_COLLABORATEUR_DELETE_SUCCESS_MSG,
                'collaborateur_delete_error'   => CONST_COLLABORATEUR_DELETE_ERROR_MSG,
                'collaborateur_add_error'      => CONST_COLLABORATEUR_ADD_ERROR_MSG,
                'collaborateur_function_error' => CONST_COLLABORATEUR_FUNCTION_ERROR_MSG,

                'collaborateur_create_user'    => CONST_CREATE_USER,
                'collaborateur_modify_user'    => CONST_MODIFY_USER,
                'collaborateur_delete_user'    => CONST_DELETE_USER,

                'profil_modify_user'           => CONST_PROFIL_MODIFY_USER,
                'profil_modify_email'          => CONST_ALERT_EMAIL_CHANGED,
                // maj etude
                'office_crud_nonce'            => wp_create_nonce("process_office_crud_nonce"),
                'profil_office_modify_error'   => CONST_PROFIL_OFFICE_MODIFY_ERROR_MSG

            )
        );
    }
}
add_action('wp_enqueue_scripts', 'append_js_files', 99);

function cridonline_access()
{
    if (CriIsNotaire()) {
        $oNotaire = CriNotaireData();
        $lvl = Config::$authCridonOnline[(int) $oNotaire->etude->subscription_level];
        wp_enqueue_script('cridonline', esc_url_raw('http://abo.prod.wkf.fr/auth/autologin.js?'.
        'auth='.$lvl.
        '&cid='.$oNotaire->id.
        '&clname='.$oNotaire->last_name.
        '&cfname='.$oNotaire->first_name.
        '&cemail='.$oNotaire->email_adress.
        '&pid=CRIDON')
            , array());

    }
}
add_action('wp_enqueue_scripts', 'cridonline_access', 50);

/**
 * hook for connection
 */
function logins_connect()
{
    require_once WP_PLUGIN_DIR . '/cridon/app/controllers/logins_controller.php';
    $controller = new LoginsController();
    $controller->connect();
}
add_action( 'wp_ajax_logins_connect',   'logins_connect' );
add_action( 'wp_ajax_nopriv_logins_connect',   'logins_connect' );

/**
 * hook for lost password
 */
function lost_password()
{
    require_once WP_PLUGIN_DIR . '/cridon/app/controllers/logins_controller.php';
    $controller = new LoginsController();
    $controller->lostPassword();
}
add_action( 'wp_ajax_lost_password',   'lost_password' );
add_action( 'wp_ajax_nopriv_lost_password',   'lost_password' );

/**
 * hook for add question
 *
 * Only for connected user
 */
function add_question()
{
    require_once WP_PLUGIN_DIR . '/cridon/app/controllers/questions_controller.php';
    $controller = new QuestionsController();
    $controller->add();
}
add_action( 'wp_ajax_add_question',   'add_question' );

/**
 * Hook for logout
 */
function custom_logout_redirect()
{
    global $current_user;

    if ($current_user->roles[0] !== CONST_ADMIN_ROLE) {
        wp_redirect( home_url() );
        exit();
    }
}
add_action('wp_logout','custom_logout_redirect');

/**
 * Hook for wp_mail_from plugged to wordpress@sitename by default
 *
 * @param $email
 * @return string
 */
function cridon_mail_from( $email )
{
    return CONST_EMAIL_SENDER_CONTACT;
}
add_filter( 'wp_mail_from', 'cridon_mail_from' );

/**
 * Hook for cridon_mail_from_name plugged to Wordpress by default
 *
 * @param $name
 * @return string
 */
function cridon_mail_from_name( $name )
{
    return CONST_EMAIL_SENDER_NAME;
}
add_filter( 'wp_mail_from_name', 'cridon_mail_from_name' );


add_action('wp_head', 'custom_remove_admin_bar');
function custom_remove_admin_bar() {
    //Remove admin bar in front for notaire
    if( CriIsNotaire() ){
        show_admin_bar(false);
    }
}

/* Cacher les elements de menu sidebar left */
add_action( 'admin_menu', 'custom_remove_menu_pages' );
function custom_remove_menu_pages() {
    if( !is_super_admin() ){
        remove_menu_page('users.php');	//Section Utilisateurs        
    }
}

//Hook for Mvc_menu_position
/**
 * @see MvcAdminLoader Class at line 65 (wp-mvc\core\loaders\mvc_admin_loader.php)
 */
add_filter( 'mvc_menu_position', 'custom_mvc_menu_position', 10, 1 );
function custom_mvc_menu_position( $menu_position ){
    /**
     * WP_MVC puts menu_position = $12; that overwrites the other menu items
     */
    $new_menu_position = 30;//Start mvc_menu_position at 30
    return $new_menu_position ;
}
//End Hook for Mvc_menu_position

/**
 * Suppression option "show admin bar" sur fiche notaire en admin
 *
 * @param $subject
 * @return mixed
 */
function cri_remove_personal_options( $subject ) {
    if (isset($_GET['user_id']) && $_GET['user_id']) {
        $notaire = mvc_model('notaire')->find_one_by_id_wp_user($_GET['user_id']);

        if ($notaire->id) {
            $subject = preg_replace('#<tr class="show-admin-bar user-admin-bar-front-wrap">.+?/tr>#s', '', $subject, 1);
        }
    }
    return $subject;
}

function cri_profile_subject_start() {
    ob_start( 'cri_remove_personal_options' );
}

function cri_profile_subject_end() {
    ob_end_flush();
}

add_action( 'admin_head-user-edit.php', 'cri_profile_subject_start' );
add_action( 'admin_footer-user-edit.php', 'cri_profile_subject_end' );


function on_publish_future_post( $post ) {
    /**
     * Ce hook démarre lorsqu'un article passe de planifier à publier.
     * Manuellement ou chnager automatiquement par WP lorsqu'il y a une visite sur le site (BO ou FO)
     */
    if( ( $post->post_type != 'post' ) ){
        return;
    }
    foreach( Config::$data as $modelConf ){
        if( ($model = findBy( $modelConf['name'], $post->ID ) ) != null ){//already create
             sendNotificationForPostPublished($post, $model);
             break;
        }
    }
}
/**
 * @see https://codex.wordpress.org/Post_Status_Transitions
 */
add_action(  'future_to_publish',  'on_publish_future_post', 10, 1 );

/**
 * Hook add new post link (only apply on Cridon modele )
 *
 * @param string $url
 * @param string $path
 * @param int $blog_id
 * @return string
 */
function add_new_post_url( $url, $path, $blog_id ) {

    $hookPath = false;
    if ( $path == "post-new.php" && isset($_GET['cridon_type']) ) {
        $path = "post-new.php?cridon_type=" . $_GET['cridon_type'];
        $hookPath = true;
    }

    return ($hookPath) ? $path : $url;
}
add_filter( 'admin_url', 'add_new_post_url', 10, 3 );


/**
 * Hook for  admin navigation menu
 */
add_action( 'admin_init', array( 'CriAdminNavMenu', 'init' ) );

/**
 * @see MvcAdminLoader Class at line 52 (wp-mvc\core\loaders\mvc_admin_loader.php)
 */
add_filter( 'mvc_admin_title', 'custom_mvc_title_page', 10, 1 );
function custom_mvc_title_page( $title ){
    if( preg_match('/(\bCridons\b)/',$title) ){
        //without 's' in 'Cridon'
        $title = MvcInflector::singularize($title);
        //translate 'User'
        $title = preg_replace('/(\bUser\b)/', 'Utilisateur', $title);
    }
    return $title ;
}

// Match wp_posts and WP_MVC show action
if( !is_admin() ){
    /**
     * @see https://codex.wordpress.org/Plugin_API/Action_Reference/wp
     */
    add_action( 'wp', 'join_wp_post_and_wpmvc' );
    function join_wp_post_and_wpmvc($wp)
    {
        global $wpdb;
        //check wp_mvc pages
        if (empty($wp->matched_query) || preg_match("/(mvc_controller)|(mvc_action)/",$wp->matched_query)){
            return;
        }
        //only for WP_Posts
        if( !is_feed() && !empty($wp->query_vars) && !isset($wp->query_vars['mvc_controller']) && ('post' === get_post_type()) ){
            $post_ID = get_the_ID();
            $post = get_post();
            foreach( Config::$data as $v ){
                $table = $v[ 'name' ];
                //Simple query using WP_query to get mvc_model (id)
                $mvc = $wpdb->get_row(
                        'SELECT id FROM '.$wpdb->prefix.$table
                        .' WHERE post_id = '.$post_ID
                );
                //when model founded
                if($mvc){
                    $options=array(
                        'controller' => $v['controller'],
                        'action'     => 'show',
                        'id'         => $post->post_name
                    );
                    $url  = MvcRouter::public_url($options);
                    //redirect to correct url
                    wp_redirect( $url, 301 );
                    exit;
                }
            }
        }
    }
}
//End Match wp_posts and WP_MVC show action

/**
 * Filter a notary capabilities depending on config
 * This would be secure because only executed on user notary connected
 *
 * @param array $caps : A list of required capabilities to access the page or doing some actions
 * @param string $cap : The capability being checked
 * @param int $user_id : The current user ID
 * @param mixed $args : A numerically indexed array of additional arguments dependent on the meta cap being used
 * @return null|array
 */
function custom_map_meta_cap( $caps, $cap, $user_id, $args ) {
    // bypass unauthorized capabilities
    // by returning an empty value (or empty array)
    // if the capabalities was found on the list of config values
    if ( CriIsNotaire() ) { // Check if user is notary
        if (is_array($caps)
            && count(array_intersect($caps, Config::$authorizedCapsForNotary)) > 0
        ) {
            return;
        }
    }

    return $caps;
}
add_filter( 'map_meta_cap', 'custom_map_meta_cap', 10, 4 );

add_action( 'wp', 'custom_redirect_301' );
function custom_redirect_301($wp)
{
    if (!is_admin() && !empty($wp->matched_query)) {
        // convert a query string to an array of vars
        parse_str($wp->matched_query, $vars);

        if (is_array($vars)
            && isset($vars['mvc_controller'])
            && isset($vars['mvc_action'])
            && isset($vars['mvc_id']) // id notary is set
            && $vars['mvc_controller'] == 'notaires' // controller "notaires"
            && $vars['mvc_action'] && !in_array($vars['mvc_action'], Config::$exceptedActionForRedirect301)
        ) {
            // redirect 301 for an url like [site_url]/notaires/{id} to [site_url]/notaires
            // and [site_url]/notaires/{id}/{action} to [site_url]/notaires/{action}
            wp_redirect(
                mvc_public_url(array(
                        'controller' => 'notaires',
                        'action'     => $vars['mvc_action']
                    )
                ),
                301
            );
        }
    }
}


/**
 * Hook lien "Lire" pour conservation filtre de veille
 * A noter que ca respecte deja le principe de WP lors de la formation des liens des Posts
 * Pour info template associé : wp-content/themes/maestro/content-post-list.php
 * @see https://codex.wordpress.org/Plugin_API/Filter_Reference/the_permalink
 *
 * @param string $url the post url
 * @return string
 */
function append_query_string($url) {
    return add_query_arg($_GET, $url);
}
add_filter('the_permalink', 'append_query_string');

/**
 * Hook permettant d'ajouter les classes `analytics` au tag anchor <a>
 * @param array $atts {
 *     The HTML attributes applied to the menu item's `<a>` element, empty strings are ignored.
 *
 *     @type string $title  Title attribute.
 *     @type string $target Target attribute.
 *     @type string $rel    The rel attribute.
 *     @type string $href   The href attribute.
 * }
 * @param object $item  The current menu item.
 * @param array  $args  An array of {@see wp_nav_menu()} arguments.
 * @param int    $depth Depth of menu item. Used for padding.
 *
 * @return array $att
 */
function addClassesAnalytics($atts, $item, $args, $depth){
    foreach($item->classes as $class) {
        if (preg_match('/analytics/', $class)) {
            $atts['class'] = $class;
        }
    }
    return $atts;
}
add_filter( 'nav_menu_link_attributes', 'addClassesAnalytics',10,4 );
