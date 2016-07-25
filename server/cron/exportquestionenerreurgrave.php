<?php
/**
 * Description of exportquestion.php
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
$model = mvc_model('question');
// call export action
$code = $model->exportQuestionEnErreurGrave();

echo $code;