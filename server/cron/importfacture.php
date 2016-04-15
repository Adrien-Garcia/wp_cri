<?php
/**
 * Description of exportfacture.php
 *
 * @package wp_cridon
 * @author eTech
 * @contributor Joelio
 */

// load WP Core
require_once '../wp-load.php';

// etude model
/**
 * @var $model Etude
 */
$model = mvc_model('etude');
// call export action
$code = $model->importFacture();

echo $code;