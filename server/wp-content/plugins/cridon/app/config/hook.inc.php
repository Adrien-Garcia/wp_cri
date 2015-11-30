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
                'error_msg'                => CONST_LOGIN_ERROR_MSG,
                'empty_error_msg'          => CONST_LOGIN_EMPTY_ERROR_MSG,
                'form_id'                  => CONST_TPL_FORM_ID,
                'login_field_id'           => CONST_TPL_LOGINFIELD_ID,
                'password_field_id'        => CONST_TPL_PASSWORDFIELD_ID,
                'error_bloc_id'            => CONST_TPL_ERRORBLOCK_ID,
                // lost password
                'lostpwd_nonce'            => wp_create_nonce("process_lostpwd_nonce"),
                'pwdform_id'               => CONST_TPL_PWDFORM_ID,
                'pwdmsg_block'             => CONST_TPL_PWDMSGBLOCK_ID,
                'email_field_id'           => CONST_TPL_PWDEMAILFIELD_ID,
                'crpcen_field_id'          => CONST_TPL_CRPCENFIELD_ID,
                'crpcen_error_msg'         => CONST_INVALIDEMAIL_ERROR_MSG,
                'crpcen_success_msg'       => CONST_RECOVPASS_SUCCESS_MSG,
                'empty_crpcen_msg'         => CONST_CRPCEN_EMPTY_ERROR_MSG,
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
            )
        );
    }
}
add_action('wp_enqueue_scripts', append_js_files(), 99);

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
 * hook for newsletter subscription
 */
function newsletter()
{
    require_once WP_PLUGIN_DIR . '/cridon/app/controllers/notaires_controller.php';
    $controller = new NotairesController();
    $controller->newsletterSubsciprtion();
}
add_action( 'wp_ajax_newsletter',   'newsletter' );
add_action( 'wp_ajax_nopriv_newsletter',   'newsletter' );

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

//Publish post
function post_published_notification( $post_ID, $post ) {
    //only for wp cron schedule
    if (!isset($_POST['_wp_http_referer']) && ( $post->post_type == 'post ')) {
        foreach( Config::$data as $modelConf ){
            if( ($model = findBy( $modelConf['name'], $post_ID )) != null ){//already create
                 sendNotificationForPostPublished($post, $model);
                 break;
            } 
        }
    }
}
add_action( 'publish_post', 'post_published_notification', 10, 2 );