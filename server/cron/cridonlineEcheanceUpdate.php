<?php

// load WP Core
require_once '../wp-load.php';

// notaire model
/**
 * @var $model Notaire
 */
$model = mvc_model('Notaire');
// call import action
$code = $model->cridonlineEcheance();
echo $code;
