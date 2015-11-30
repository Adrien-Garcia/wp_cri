<?php
/**
 * Description of importcahier.php
 *
 * @package wp_cridon
 * @author eTech
 * @contributor Joelio
 */

// set utf-8 encoding
header('Content-type: text/plain; charset=utf-8');

// load WP Core
require_once '../wp-load.php';

// cahier_cridon model
$model = mvc_model('CahierCridon');
// call import action
$model->importIntoSite();