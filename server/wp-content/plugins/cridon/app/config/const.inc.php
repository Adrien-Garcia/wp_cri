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
	define( 'CONST_ODBC_DRIVER', '{SQL Server Native Client 10.0}' );
}
if ( !defined( 'CONST_ODBC_HOST' ) ) {
	define( 'CONST_ODBC_HOST', 'localhost' );
}
if ( !defined( 'CONST_ODBC_PORT' ) ) {
	define( 'CONST_ODBC_PORT', 1433 );
}
if ( !defined( 'CONST_ODBC_USER' ) ) {
	define( 'CONST_ODBC_USER', 'user' );
}
if ( !defined( 'CONST_ODBC_PASSWORD' ) ) {
	define( 'CONST_ODBC_PASSWORD', '' );
}
if ( !defined( 'CONST_ODBC_DATABASE' ) ) {
	define( 'CONST_ODBC_DATABASE', 'cridon' );
}
if ( !defined( 'CONST_ODBC_TABLE_NOTAIRE' ) ) {
	define( 'CONST_ODBC_TABLE_NOTAIRE', 'notaire' );
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

