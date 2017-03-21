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
 * @var $model Formation
 */
ini_set("memory_limit","256M");
$model = mvc_model('formation');
// call import action
$code = $model->importIntoFormation($force);

echo $code;
