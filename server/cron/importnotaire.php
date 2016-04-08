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

// Force update ?
$force = isset($argv[1]) && $argv[1];

// notaire model
/**
 * @var $model Notaire
 */
$model = mvc_model('notaire');
// call import action
$code = $model->importIntoWpUsers($force);

// call reset action
$code = $model->cronResetPwd();

echo $code;
