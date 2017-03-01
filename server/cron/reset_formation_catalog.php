<?php
/**
 * Description of reset_formation_catalog.php
 *
 * @package wp_cridon
 */

// load WP Core
require_once '../wp-load.php';

// notaire model
/**
 * @var $model Formation
 */
$model = mvc_model('Formation');
// call import action
if($model->resetCatalogNextYear()){
    echo 'OK';
} else {
    echo 'KO';
}