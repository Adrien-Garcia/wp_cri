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

// to be deleted
$this->current_user->add_role( CONST_NOTAIRE_ROLE );
$this->current_user->add_role( CONST_FINANCE_ROLE );
echo '<pre>'; die(print_r($this->current_user));

echo 'Init role done';