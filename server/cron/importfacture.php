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

// entite model
/**
 * @var $model Entite
 */
$model = mvc_model('entite');
// call export action
$code = $model->importFacture(true);

echo $code;
