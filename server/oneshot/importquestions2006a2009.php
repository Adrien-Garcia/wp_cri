<?php
/**
 * Created by JETPULP.
 * User: valbert
 * Date: 12/12/2015
 * Time: 11:39
 */
// set utf-8 encoding
header('Content-type: text/plain; charset=utf-8');
if (!defined( 'WP_ADMIN' )) {
    define('WP_ADMIN', true);
}
// load WP Core
require_once '../wp-load.php';

// indexes of fields
$indexes = array(
    /* index SRENUM  */
    'SRENUM'            => 0,
    /* index SRENUM  */
    'CRPCEN'            => 1,
    /* index notaire  */
    'NOTAIRE_PRENOM'    => 2,
    'NOTAIRE_NOM'       => 3,
    /* index Objet  */
    'OBJET'             => 4,
    /* index competence  */
    'COMPETENCE'        => 5,
    /* index Juriste  */
    'JURISTE'           => 6,
    /* index Support  */
    'SUPPORT'           => 7,
    /* index Code affectation  */
    'CODE_AFFECTATION'  => 8,
    /* index Date creation  */
    'DATE_CREATION'     => 9,
    /* index Date affectation  */
    'DATE_AFFECTATION'  => 10,
    /* index Date de reponse  */
    'DATE_REPONSE'      => 11,
    /* PDF */
    'PDF'               => 12,
    /* index Suite  */
    'SUITE'             => 13,
);

// cahier_cridon model
/**
 * @var $model Question
 */
$model = mvc_model('Question');
// call import action
$resp = $model->importQuestion2006to2009($indexes, true);

echo ($resp ? 'Import successful' : 'Import failed');
