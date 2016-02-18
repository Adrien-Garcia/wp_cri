<?php
/**
 * Description of question_weeklyfullupdate.php
 *
 * @package wp_cridon
 * @author eTech
 * @contributor Joelio
 */

// load WP Core
require_once '../wp-load.php';

// question model
/**
 * @var $model Question
 */
$model = mvc_model('Question');
// call import action
$code = $model->weeklyUpdate();
echo $code;