<?php
/**
 * Created by PhpStorm.
 * User: amsellem
 * Date: 10/02/2016
 * Time: 11:49
 */


// set utf-8 encoding
header('Content-type: text/html; charset=utf-8');

// load WP Core
require_once '../wp-load.php';

/**
 * @var Document $model
 */
$model = mvc_model('Document');
// call archivePJ
$documents = $model->getDocumentsWithPJAndDocAnswer();
if (!empty($documents)){
    $model->archivePJs($documents);
}

echo 'Archive done';