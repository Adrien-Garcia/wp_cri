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
// add notary specific role
add_role( CONST_NOTAIRE_DIV_ROLE, 'Notaires DIV' );
add_role( CONST_NOTAIRE_ORG_ROLE, 'Notaires ORG' );

// add default capabilities for "Notary" role (access_solde, access_level_1)
$notaryRole = get_role( CONST_NOTAIRE_ROLE );
$notaryRole->add_cap(CONST_ACCESS_SOLDE);
$notaryRole->add_cap(CONST_ACCESS_LEVEL_1);

echo 'Init role done';