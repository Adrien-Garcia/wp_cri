<?php
/**
 * Description of const.inc.php
 * @package wp_cridon
 * @author Etech
 * @contributor Joelio
 */

/**
 * @var $env string will determine if dev, preprod or prod mode is active. Value must be set via SERVER or putenv in wp-config
 */
$env = getenv('ENV');
define('DEV', 'DEV');
define('LOCAL', 'LOCAL');
define('PROD', 'PROD');
define('PREPROD', 'PREPROD');

// notaire role
if ( !defined( 'CONST_NOTAIRE_ROLE' ) ) {
	define( 'CONST_NOTAIRE_ROLE', 'notaire' );
}
// administrator role
if ( !defined( 'CONST_ADMIN_ROLE' ) ) {
	define( 'CONST_ADMIN_ROLE', 'administrator' );
}
if ( !defined( 'CONST_LOGIN_SEPARATOR' ) ) {
    define( 'CONST_LOGIN_SEPARATOR', '~' );
}

// wpmvc prefix
if ( !defined( 'CONST_WPMVC_PREFIX' ) ) {
	define( 'CONST_WPMVC_PREFIX', 'mvc_' );
}

// ODBC
if ( !defined( 'CONST_ODBC_DRIVER' ) ) {
	define( 'CONST_ODBC_DRIVER', '{MySQL ODBC 5.3 Ansi Driver}' );
}

if ( !defined( 'CONST_DB_DEFAULT' ) ) {
    define('CONST_DB_DEFAULT', 'MySQL');
}

if ( !defined( 'CONST_DB_ORACLE' ) ) {
    define('CONST_DB_ORACLE', 'oracle');
}
if ( !defined( 'CONST_DB_TYPE' ) ) {
    switch ($env) {
        case PROD:
        case PREPROD:
        case DEV:
        case LOCAL:
            $type = CONST_DB_ORACLE;
            break;
        default:
            $type = CONST_DB_DEFAULT;
            break;
    }
    define( 'CONST_DB_TYPE', $type );
}

if ( !defined( 'CONST_DB_HOST' ) ) {
    switch ($env) {
        case PROD:
            $host = '10.115.100.192';
            break;
        case PREPROD:
            $host = '10.115.100.192';
            break;
        case DEV:
            $host = '10.115.100.192';
            break;
        case LOCAL:
            $host = '192.168.69.7';
            break;
        default:
            $host = '192.168.1.9';
            break;
    }
	define( 'CONST_DB_HOST', $host );
}
if ( !defined( 'CONST_DB_PORT' ) ) {
    switch ($env) {
        case PROD:
        case PREPROD:
        case LOCAL:
        case DEV:
            $port = 1521;
            break;
        default:
            $port = 3306;
            break;
    }
	define( 'CONST_DB_PORT', $port );
}
if ( !defined( 'CONST_DB_USER' ) ) {
    switch ($env) {
        case PROD:
            $user = 'JETPULP';
            break;
        case PREPROD:
            $user = 'JETPULP';
            break;
        case DEV:
            $user = 'JETPULP';
            break;
        case LOCAL:
            $user = 'cridon';
            break;
        default:
            $user = 'cridon';
            break;
    }
	define( 'CONST_DB_USER', $user );
}
if ( !defined( 'CONST_DB_PASSWORD' ) ) {
    switch ($env) {
        case PROD:
            $pwd = 'JTPLPX3';
            break;
        case PREPROD:
            $pwd = 'JTPLPX3';
            break;
        case DEV:
            $pwd = 'JTPLPX3';
            break;
        case LOCAL:
            $pwd = 'cridon';
            break;
        default:
            $pwd = '2d7nGNFc';
            break;
    }
	define( 'CONST_DB_PASSWORD', $pwd );
}
if ( !defined( 'CONST_DB_DATABASE' ) ) {
    switch ($env) {
        case PROD:
            $dbn = 'X150';
            break;
        case PREPROD:
            $dbn = 'X150';
            break;
        case DEV:
            $dbn = 'X150';
            break;
        case LOCAL:
            $dbn = 'XE';
            break;
        default:
            $dbn = 'cridon';
            break;
    }
	define( 'CONST_DB_DATABASE', $dbn );
}
if ( !defined( 'CONST_DB_TABLE_NOTAIRE' ) ) {
	define( 'CONST_DB_TABLE_NOTAIRE', 'ZEXPNOTV' );
}

// import CSV notaire file path
if ( !defined( 'CONST_IMPORT_CSV_NOTAIRE_FILE_PATH' ) ) {
//    define( 'CONST_IMPORT_CSV_NOTAIRE_FILE_PATH', 'G:/MyProjects/JetPulp/Docs/CSV/' );
    define( 'CONST_IMPORT_CSV_NOTAIRE_FILE_PATH', 'PATH_TO_CSVFILE' );
}

// status
if ( !defined( 'CONST_STATUS_ENABLED' ) ) {
    define( 'CONST_STATUS_ENABLED', 2 );
}
if ( !defined( 'CONST_STATUS_DISABLED' ) ) {
    define( 'CONST_STATUS_DISABLED', 1 );
}

// default role by notaire group
if ( !defined( 'CONST_OFFICES_ROLE' ) ) {
    define( 'CONST_OFFICES_ROLE', 'off' );
}
if ( !defined( 'CONST_ORGANISMES_ROLE' ) ) {
    define( 'CONST_ORGANISMES_ROLE', 'org' );
}
if ( !defined( 'CONST_CLIENTDIVERS_ROLE' ) ) {
    define( 'CONST_CLIENTDIVERS_ROLE', 'div' );
}

// import option to be used (csv, odbc, oci)
if ( !defined( 'CONST_IMPORT_OPTION' ) ) {
    switch ($env) {
        case PROD:
        case PREPROD:
        case DEV:
        case LOCAL:
            $dbn = 'oci';
            break;
        default:
            $dbn = 'odbc';
            break;
    }
    define( 'CONST_IMPORT_OPTION', $dbn );
}

// login
if ( !defined( 'CONST_LOGIN_ERROR_MSG' ) ) {
    define( 'CONST_LOGIN_ERROR_MSG', 'Les informations de connexion sont incorrectes. En cas d\'erreurs répétées, nous vous invitons à contacter le CRIDON LYON afin de recevoir vos identifiants' );
}
if ( !defined( 'CONST_LOGIN_EMPTY_ERROR_MSG' ) ) {
    define( 'CONST_LOGIN_EMPTY_ERROR_MSG', 'Merci de bien remplir votre identifiant et mot de passe !' );
}
if ( !defined( 'CONST_TPL_FORM_ID' ) ) {
    define( 'CONST_TPL_FORM_ID', 'loginFormId' );
}
if ( !defined( 'CONST_TPL_ERRORBLOCK_ID' ) ) {
    define( 'CONST_TPL_ERRORBLOCK_ID', 'errorMsgId' );
}
if ( !defined( 'CONST_TPL_LOGINFIELD_ID' ) ) {
    define( 'CONST_TPL_LOGINFIELD_ID', 'loginFieldId' );
}
if ( !defined( 'CONST_TPL_PASSWORDFIELD_ID' ) ) {
    define( 'CONST_TPL_PASSWORDFIELD_ID', 'passwordFieldId' );
}
//Default Post per page
if ( !defined( 'DEFAULT_POST_PER_PAGE' ) ) {
    define( 'DEFAULT_POST_PER_PAGE', 10 );
}

// lost password
if ( !defined( 'CONST_INVALIDEMAIL_ERROR_MSG' ) ) {
    define( 'CONST_INVALIDEMAIL_ERROR_MSG', 'Vous ne pouvez pas récupérer votre mot de passe. Merci de contacter CRIDON LYON' );
}
if ( !defined( 'CONST_RECOVPASS_SUCCESS_MSG' ) ) {
    define( 'CONST_RECOVPASS_SUCCESS_MSG', 'Votre mot de passe veint d\'être envoyé sur votre adresse email.' );
}
if ( !defined( 'CONST_TPL_PWDFORM_ID' ) ) {
    define( 'CONST_TPL_PWDFORM_ID', 'lostPwdFormId' );
}
if ( !defined( 'CONST_TPL_PWDMSGBLOCK_ID' ) ) {
    define( 'CONST_TPL_PWDMSGBLOCK_ID', 'msgBlockId' );
}
if ( !defined( 'CONST_CRPCEN_EMPTY_ERROR_MSG' ) ) {
    define( 'CONST_CRPCEN_EMPTY_ERROR_MSG', 'Merci de bien remplir vos adresse email et CRPCEN !' );
}
if ( !defined( 'CONST_TPL_PWDEMAILFIELD_ID' ) ) {
    define( 'CONST_TPL_PWDEMAILFIELD_ID', 'emailFieldId' );
}
if ( !defined( 'CONST_TPL_CRPCENFIELD_ID' ) ) {
    define( 'CONST_TPL_CRPCENFIELD_ID', 'crpcenFieldId' );
}
if ( !defined( 'CONST_EMAIL_SUBJECT' ) ) {
    define( 'CONST_EMAIL_SUBJECT', 'Cridon - Mot de passe oublié' );
}
// do not remove "%s" : it uses to inject password value into the mail content
if ( !defined( 'CONST_EMAIL_CONTENT' ) ) {
    define( 'CONST_EMAIL_CONTENT', 'Votre mot de passe pour accèder à l\'espace privé du site de CRIDON LYON est : %s' );
}

// email sender adress and name
// Hook wp_mail for not using sitename and "Wordpress" by default
if ( !defined( 'CONST_EMAIL_SENDER_NAME' ) ) {
    define( 'CONST_EMAIL_SENDER_NAME', 'Cridon' );
}
if ( !defined( 'CONST_EMAIL_SENDER_CONTACT' ) ) {
    define( 'CONST_EMAIL_SENDER_CONTACT', 'cridon@jetpulp.dev' );
}

// import CSV solde file path
if ( !defined( 'CONST_IMPORT_CSV_SOLDE_FILE_PATH' ) ) {
    $uploadDir = wp_upload_dir();
    define( 'CONST_IMPORT_CSV_SOLDE_FILE_PATH', $uploadDir['basedir'] . '/import/importConso/' );
}

// Contact email for import error reporting
if ( !defined( 'CONST_EMAIL_ERROR_CONTACT' ) ) {
    define( 'CONST_EMAIL_ERROR_CONTACT', 'victor.albert@jetpulp.fr' );
}

// do not remove "%s" : it uses to inject import type (notaire|solde) into the mail content
if ( !defined( 'CONST_EMAIL_ERROR_SUBJECT' ) ) {
    define( 'CONST_EMAIL_ERROR_SUBJECT', 'Cridon - Import' );
}
if ( !defined( 'CONST_EMAIL_ERROR_CONTENT' ) ) {
    define( 'CONST_EMAIL_ERROR_CONTENT', 'Fichier d\'import absent pour : %s' );
}
if ( !defined( 'CONST_EMAIL_ERROR_CORRUPTED_FILE' ) ) {
    define( 'CONST_EMAIL_ERROR_CORRUPTED_FILE', 'Fichier d\'import mal formaté pour : %s' );
}

// Error reporting for Exception
if ( !defined( 'CONST_EMAIL_ERROR_CATCH_EXCEPTION' ) ) {
    define( 'CONST_EMAIL_ERROR_CATCH_EXCEPTION', 'Une exeption a été levée avec le message d\'erreur suivante : "%s"' );
}
// Appel && Courrier support id
if ( !defined( 'CONST_SUPPORT_APPEL_ID' ) ) {
    define( 'CONST_SUPPORT_APPEL_ID',  2);
}
if ( !defined( 'CONST_SUPPORT_COURRIER_ID' ) ) {
    define( 'CONST_SUPPORT_COURRIER_ID',  1);
}
if ( !defined( 'CONST_SUPPORT_URG48H_ID' ) ) {
    define( 'CONST_SUPPORT_URG48H_ID',  6);
}
if ( !defined( 'CONST_SUPPORT_URGWEEK_ID' ) ) {
    define( 'CONST_SUPPORT_URGWEEK_ID',  7);
}

// Notaire fonctions id (used for filtering capability)
if ( !defined( 'CONST_NOTAIRE_ASSOCIE' ) ) {
    define( 'CONST_NOTAIRE_ASSOCIE', 2 );
}
if ( !defined( 'CONST_NOTAIRE_ASSOCIEE' ) ) {
    define( 'CONST_NOTAIRE_ASSOCIEE', 3 );
}
if ( !defined( 'CONST_NOTAIRE_SALARIE' ) ) {
    define( 'CONST_NOTAIRE_SALARIE', 4 );
}
if ( !defined( 'CONST_NOTAIRE_SALARIEE' ) ) {
    define( 'CONST_NOTAIRE_SALARIEE', 5 );
}
if ( !defined( 'CONST_NOTAIRE_GERANT' ) ) {
    define( 'CONST_NOTAIRE_GERANT', 6 );
}
if ( !defined( 'CONST_NOTAIRE_GERANTE' ) ) {
    define( 'CONST_NOTAIRE_GERANTE', 7 );
}
if ( !defined( 'CONST_NOTAIRE_SUPLEANT' ) ) {
    define( 'CONST_NOTAIRE_SUPLEANT', 8 );
}
if ( !defined( 'CONST_NOTAIRE_SUPLEANTE' ) ) {
    define( 'CONST_NOTAIRE_SUPLEANTE', 9 );
}

// Add Question Form
if ( !defined( 'CONST_QUESTION_SUPPORT_FIELD' ) ) {
    define( 'CONST_QUESTION_SUPPORT_FIELD', 'question_support' );
}
if ( !defined( 'CONST_QUESTION_FORM_ID' ) ) {
    define( 'CONST_QUESTION_FORM_ID', 'questionFormId' );
}
if ( !defined( 'CONST_QUESTION_MATIERE_FIELD' ) ) {
    define( 'CONST_QUESTION_MATIERE_FIELD', 'question_matiere' );
}
if ( !defined( 'CONST_QUESTION_COMPETENCE_FIELD' ) ) {
    define( 'CONST_QUESTION_COMPETENCE_FIELD', 'question_competence' );
}
if ( !defined( 'CONST_QUESTION_OBJECT_FIELD' ) ) {
    define( 'CONST_QUESTION_OBJECT_FIELD', 'question_objet' );
}
if ( !defined( 'CONST_QUESTION_MESSAGE_FIELD' ) ) {
    define( 'CONST_QUESTION_MESSAGE_FIELD', 'question_message' );
}
if ( !defined( 'CONST_QUESTION_ATTACHEMENT_FIELD' ) ) {
    define( 'CONST_QUESTION_ATTACHEMENT_FIELD', 'question_fichier' );
}
// Files options
if ( !defined( 'CONST_QUESTION_MAX_FILE_SIZE' ) ) {
    define( 'CONST_QUESTION_MAX_FILE_SIZE', 8000000 ); // bytes
}
// Success Message
if ( !defined( 'CONST_QUESTION_SUCCESS_MSG_FIELD' ) ) {
    define( 'CONST_QUESTION_SUCCESS_MSG_FIELD', 'msgBlockQuestionId' );
}
if ( !defined( 'CONST_QUESTION_ACTION_SUCCESSFUL' ) ) {
    define( 'CONST_QUESTION_ACTION_SUCCESSFUL', 'Question envoyée avec succès' );
}
// Error message
if ( !defined( 'CONST_QUESTION_ACTION_ERROR' ) ) {
    define( 'CONST_QUESTION_ACTION_ERROR', 'Une erreur s\'est produite lors de l\'envoie de votre question. Merci de contacter le responsable.' );
}
if ( !defined( 'CONST_QUESTION_MAX_FILES_ERROR' ) ) {
    define( 'CONST_QUESTION_MAX_FILES_ERROR', 'Le nombre maximal de fichiers autorisés est de %s' );
}
if ( !defined( 'CONST_QUESTION_FILE_SIZE_ERROR' ) ) {
    define( 'CONST_QUESTION_FILE_SIZE_ERROR', 'La taille maximale de chaque fichier ne doit pas depasser de %s' );
}//Default question answered per page
if ( !defined( 'DEFAULT_QUESTION_PER_PAGE' ) ) {
    define( 'DEFAULT_QUESTION_PER_PAGE', 10 );
}


// import Question
if ( !defined( 'CONST_ODBC_TABLE_QUEST' ) ) {
    define( 'CONST_ODBC_TABLE_QUEST', 'ZQUESTV' );
}
if ( !defined( 'CONST_QUEST_CREATED_BY_SITE' ) ) {
    define( 'CONST_QUEST_CREATED_BY_SITE', 0 );
}
if ( !defined( 'CONST_QUEST_CREATED_IN_X3' ) ) {
    define( 'CONST_QUEST_CREATED_IN_X3', 1 );
}
if ( !defined( 'CONST_QUEST_UPDATED_IN_X3' ) ) {
    define( 'CONST_QUEST_UPDATED_IN_X3', 2 );
}
if ( !defined( 'CONST_QUEST_TRANSMIS_ERP' ) ) {
    define( 'CONST_QUEST_TRANSMIS_ERP', 1 );
}

// import GED
if ( !defined( 'CONST_IMPORT_DOCUMENT_ORIGINAL_PATH' ) ) {
    $uploadDir = wp_upload_dir();
    define( 'CONST_IMPORT_DOCUMENT_ORIGINAL_PATH', $uploadDir['basedir'] . '/import/importsGED/' );
}
if ( !defined( 'CONST_IMPORT_DOCUMENT_TEMP_PATH' ) ) {
    $uploadDir = wp_upload_dir();
    define( 'CONST_IMPORT_DOCUMENT_TEMP_PATH', $uploadDir['basedir'] . '/import/importsGEDTemp/' );
}
if ( !defined( 'CONST_IMPORT_FILE_TYPE' ) ) {
    define( 'CONST_IMPORT_FILE_TYPE', 'txt' );
}
if ( !defined( 'CONST_IMPORT_GED_CONTENT_SEPARATOR' ) ) {
    define( 'CONST_IMPORT_GED_CONTENT_SEPARATOR', ';' );
}
if ( !defined( 'CONST_IMPORT_GED_LOG_SUCCESS_MSG' ) ) {
    define( 'CONST_IMPORT_GED_LOG_SUCCESS_MSG', 'Import GED du %s : action terminée avec succès pour les documents suivants "%s"' );
}
if ( !defined( 'CONST_IMPORT_GED_LOG_CORRUPTED_DOC_MSG' ) ) {
    define( 'CONST_IMPORT_GED_LOG_CORRUPTED_DOC_MSG', 'Import GED du %s : le CSV associé au document "%s" ne contenait pas les informations attendues' );
}
if ( !defined( 'CONST_IMPORT_GED_LOG_CORRUPTED_CSV_MSG' ) ) {
    define( 'CONST_IMPORT_GED_LOG_CORRUPTED_CSV_MSG', 'Import GED du %s : fichier d\'import mal formaté pour "%s"' );
}
if ( !defined( 'CONST_IMPORT_GED_LOG_CORRUPTED_PDF_MSG' ) ) {
    define( 'CONST_IMPORT_GED_LOG_CORRUPTED_PDF_MSG', 'Import GED du %s : le fichier PDF associé au CSV de la question "%s" est illisible' );
}
if ( !defined( 'CONST_IMPORT_GED_LOG_EMPTY_DIR_MSG' ) ) {
    define( 'CONST_IMPORT_GED_LOG_EMPTY_DIR_MSG', 'Import GED du %s : repertoire d\'import vide' );
}
if ( !defined( 'CONST_IMPORT_GED_LOG_DOC_WITHOUT_QUESTION_MSG' ) ) {
    define( 'CONST_IMPORT_GED_LOG_DOC_WITHOUT_QUESTION_MSG', 'Import GED du %s : aucune question n\'est associée au document suivant "%s"' );
}

if (!defined('CONST_CONNECTION_FAILED')) {
    define('CONST_CONNECTION_FAILED', 'La connexion à la base Oracle a échoué');
}

// Log file
if ( !defined( 'CONST_LOG_ERROR_DIR' ) ) {
    $logDir = WP_PLUGIN_DIR . '/cridon/logs';
    if (!is_dir($logDir)) {
        wp_mkdir_p($logDir);
    }
    define( 'CONST_LOG_ERROR_DIR',  $logDir);
}