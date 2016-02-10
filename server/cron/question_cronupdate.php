<?php
/**
 * Description of question_cronupdate.php
 *
 * @package wp_cridon
 * @author eTech
 * @contributor Joelio
 */

// load WP Core
require_once '../wp-load.php';

// Force update ?
$force = isset($argv[1]) && $argv[1];

// question model
/**
 * @var $model Question
 */
$model = mvc_model('Question');
// call import action
$code = $model->cronUpdate($force);
echo $code;
