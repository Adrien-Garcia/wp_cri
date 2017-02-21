<?php
/**
 * Import files without sending mails (used for history import)
 */

// load WP Core
require_once '../wp-load.php';

// f --> "Facture" or r--> "Relevés de consommation"
if (!empty($argv[1]) || !in_array($argv[1], array('r', 'f'))) {
    die('Missing kind of file argument : f for "Factures", r for "Relevés de Consommation');
}

/**
 * @var $model Entite
 */
$model = mvc_model('entite');

if ('f' === $argv[1]) {
    $code = $model->importFacture(false);
} else {
    $code = $model->importReleveconso(false);
}

echo $code;
