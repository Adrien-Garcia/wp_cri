<?php
/**
 * Description of importnotaire.php
 *
 * @package wp_cridon
 * @author eTech
 * @contributor Joelio
 */

// load WP Core
require_once '../wp-load.php';

// notaire model
$model = mvc_model('notaire');
// call import action
$code = $model->importIntoWpUsers();

echo $code;