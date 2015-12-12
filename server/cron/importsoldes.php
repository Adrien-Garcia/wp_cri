<?php
/**
 * Description of importnotaire.php
 *
 * @package wp_cridon
 * @author eTech
 * @contributor Joelio
 */

// load WP Core
require_once '../wp-load.php';

// notaire model
/**
 * @var $model Notaire
 */
$model = mvc_model('notaire');
// call import action
$code = $model->importSolde();

echo $code;