<?php
/**
 * Description of initdefaultrole.php
 *
 * @package wp_cridon
 * @author eTech
 * @contributor Joelio
 */

// set utf-8 encoding
header('Content-type: text/html; charset=utf-8');

// load WP Core
require_once '../wp-load.php';

// add  notary role
add_role( CONST_NOTAIRE_ROLE, 'Notaires' );
// add  access finance role
add_role( CONST_FINANCE_ROLE, 'Accès finances' );
// add  access add question role
add_role( CONST_QUESTIONECRITES_ROLE, 'Poser des questions écrites' );
// add  access add question role
add_role( CONST_QUESTIONTELEPHONIQUES_ROLE, 'Poser des questions téléphoniques' );

echo 'Init role done';