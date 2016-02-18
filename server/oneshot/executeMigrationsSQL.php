<?php
/**
 * Created by PhpStorm.
 * User: valbert
 * Date: 08/02/16
 * Time: 11:27
 */
$cridonDir = dirname(__FILE__).'/../wp-content/plugins/cridon';

// load WP Core
require_once '../wp-load.php';
require_once $cridonDir . '/app/utils/class_loader.php';
require_once $cridonDir . '/cridon_loader.php';
$loader = new CridonLoader();
$loader->migrate();
