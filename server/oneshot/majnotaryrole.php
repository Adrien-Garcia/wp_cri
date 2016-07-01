<?php
/**
 * Description of majnotaryrole.php
 *
 * @package wp_cridon
 * @author eTech
 * @contributor Joelio
 */

// set utf-8 encoding
header('Content-type: text/html; charset=utf-8');
if (!defined( 'WP_ADMIN' )) {
    define('WP_ADMIN', true);
}
// load WP Core
require_once '../wp-load.php';

// useful class
require_once 'majnotaryrole.class.php';

// init action
MajNotaryRole::init();
