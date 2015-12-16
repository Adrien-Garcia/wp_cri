<?php
/**
 * Created by JETPULP.
 * User: valbert
 * Date: 12/12/2015
 * Time: 11:39
 */
// set utf-8 encoding
header('Content-type: text/plain; charset=utf-8');

// load WP Core
require_once '../wp-load.php';

// cahier_cridon model
/**
 * @var $model Question
 */
$model = mvc_model('Question');
// call import action
$model->importIntoCriQuestion();
