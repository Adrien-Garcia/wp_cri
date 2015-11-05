<?php
/**
 * Description of const.inc.php
 * @package wp_cridon
 * @author Etech
 * @contributor Joelio
 */

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
if ( !defined( 'CONST_ODBC_HOST' ) ) {
	define( 'CONST_ODBC_HOST', '192.168.1.9' );
}
if ( !defined( 'CONST_ODBC_PORT' ) ) {
	define( 'CONST_ODBC_PORT', 3306 );
}
if ( !defined( 'CONST_ODBC_USER' ) ) {
	define( 'CONST_ODBC_USER', 'cridon' );
}
if ( !defined( 'CONST_ODBC_PASSWORD' ) ) {
	define( 'CONST_ODBC_PASSWORD', '2d7nGNFc' );
}
if ( !defined( 'CONST_ODBC_DATABASE' ) ) {
	define( 'CONST_ODBC_DATABASE', 'cridon' );
}
if ( !defined( 'CONST_ODBC_TABLE_NOTAIRE' ) ) {
	define( 'CONST_ODBC_TABLE_NOTAIRE', 'ZEXPNOTV' );
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

// import option to be used (csv, odbc)
if ( !defined( 'CONST_IMPORT_OPTION' ) ) {
    define( 'CONST_IMPORT_OPTION', 'odbc' );
}

// login
if ( !defined( 'CONST_LOGIN_ERROR_MSG' ) ) {
    define( 'CONST_LOGIN_ERROR_MSG', 'Les informations de connexion sont incorrectes. En cas d\'erreurs répétées, nous vous invitons à contacter le Cridon de Lyon afin de recevoir vos identifiants' );
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
    define( 'DEFAULT_POST_PER_PAGE', 4 );
}

// lost password
if ( !defined( 'CONST_INVALIDEMAIL_ERROR_MSG' ) ) {
    define( 'CONST_INVALIDEMAIL_ERROR_MSG', 'Vous ne pouvez pas récupérer votre mot de passe. Merci de contacter CRIDON' );
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
    define( 'CONST_EMAIL_CONTENT', 'Votre mot de passe pour accèder à l\'espace privé du site de Cridon est : %s' );
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
if ( !defined( 'CONST_EMAIL_ERROR_CONTACT_CC' ) ) {
    define( 'CONST_EMAIL_ERROR_CONTACT_CC', 'contactcc@mail.dev' );
}
// do not remove "%s" : it uses to inject import type (notaire|solde) into the mail content
if ( !defined( 'CONST_EMAIL_ERROR_SUBJECT' ) ) {
    define( 'CONST_EMAIL_ERROR_SUBJECT', 'Cridon - Import de fichier' );
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

