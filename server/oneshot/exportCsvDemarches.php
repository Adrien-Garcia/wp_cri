<?php
/**
 * Description of export.php
 *
 * @package wp_cridon
 * @author Jetpulp
 * @contributor chorgues
 */

// set utf-8 encoding
header('Content-type: text/plain; charset=utf-8');
if (!defined( 'WP_ADMIN' )) {
    define('WP_ADMIN', true);
}
// load WP Core
require_once '../wp-load.php';

/**
 * @var Demarche $model
 */
$model = mvc_model('Demarche');
// call export action
$model->exportCsvDemarchesToFile(CONST_EXPORT_CSV_DEMARCHE_FILE_PATH . 'demarches_complet.csv', true);
echo CONST_EXPORT_CSV_DEMARCHE_FILE_PATH . 'demarches_complet.csv';
echo "\n";
echo wp_upload_dir()['baseurl']. '/demarches' . '/demarches_complet.csv';
echo "\n";

