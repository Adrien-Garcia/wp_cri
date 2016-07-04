<?php
/**
 * Created by PhpStorm.
 * User: valbert
 * Date: 17/02/16
 * Time: 11:48
 */
// load WP Core
if (!defined( 'WP_ADMIN' )) {
    define('WP_ADMIN', true);
}
require_once '../wp-load.php';
// disable admin bar for existing notaire
CriDisableAdminBarForExistingNotaire();
