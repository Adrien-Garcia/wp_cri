<?php
/**
 * Description of exportnotaire.php
 *
 * @package wp_cridon
 * @author eTech
 * @contributor Joelio
 */

// load WP Core
require_once '../wp-load.php';

/**
 * @var $model Notaire
 */
$model = mvc_model('Notaire');
// call export action
$code = $model->cronExportNotary();

echo $code;