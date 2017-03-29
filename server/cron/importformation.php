<?php
/**
 * Description of importformation.php
 *
 * @package wp_cridon
 * @author Jetpulp
 * @contributor chorgues
 */

// load WP Core
require_once '../wp-load.php';

// formation model
/**
 * @var $model Formation
 */
ini_set("memory_limit","256M");
$model = mvc_model('formation');
// call import action
$code = $model->importIntoFormation();

echo $code;
