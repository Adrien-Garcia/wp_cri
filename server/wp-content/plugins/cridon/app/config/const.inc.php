<?php
/**
 * Description of const.inc.php
 * @package wp_cridon
 * @author Etech
 * @contributor Joelio
 */

// notaire
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
    define( 'CONST_STATUS_ENABLED', 1 );
}
if ( !defined( 'CONST_STATUS_DISABLED' ) ) {
    define( 'CONST_STATUS_DISABLED', 0 );
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
