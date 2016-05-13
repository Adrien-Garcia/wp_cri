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
// add  access collaborateur tab
add_role( CONST_COLLABORATEUR_TAB_ROLE, 'Accès onglet collaborateur' );
// add  access finance role
add_role( CONST_FINANCE_ROLE, 'Accès finances' );
// add  access add question role
add_role( CONST_QUESTIONECRITES_ROLE, 'Poser des questions écrites' );
// add  access add question role
add_role( CONST_QUESTIONTELEPHONIQUES_ROLE, 'Poser des questions téléphoniques' );
// add  access "connaissance" role
add_role( CONST_CONNAISANCE_ROLE, 'Accès aux bases de connaissance' );
// add notary specific role
add_role( CONST_NOTAIRE_DIV_ROLE, 'Notaires DIV' );
add_role( CONST_NOTAIRE_ORG_ROLE, 'Notaires ORG' );

echo 'Init role done';
