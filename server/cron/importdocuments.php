<?php
/**
 * Created by JETPULP.
 * User: valbert
 * Date: 08/12/2015
 * Time: 10:09
 */


// load WP Core
require_once '../wp-load.php';

/**
 * @var $model Document
 */
$model = mvc_model('document');

// call import action
$model->import();