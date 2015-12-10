<?php
/**
 * Description of question_dailyupdate.php
 *
 * @package wp_cridon
 * @author eTech
 * @contributor Joelio
 */

// load WP Core
require_once '../wp-load.php';

// question model
$model = mvc_model('Question');
// call import action
$code = $model->dailyUpdate();
echo $code;