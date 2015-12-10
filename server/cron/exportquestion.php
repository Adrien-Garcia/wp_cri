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
$model = mvc_model('question');
// call export action
$code = $model->exportQuestion();

echo $code;