<?php
/**
 * Allow to set default roles for a specific user, according to his "function"/status
 * @param $argv[1] : CRPCEN, ID for user's group
 * @param $argv[2] : Password of the related user (the only diff between users is the password)
 */

// load WP Core
require_once '../wp-load.php';

// Credentials
$crpcen = !empty($argv[1]) ? $argv[1] : false;
$pass = !empty($argv[2]) ? $argv[2] : false;

if (!$crpcen || !$pass) {
    die ('No CRPCEN nor Password provided');
}

$options = array (
    'conditions' => array(
        'crpcen = "'.$crpcen.'" AND web_password = "'.$pass.'"'
    ),
);

/** @var QueryBuilder $qb */
$qb = mvc_model('QueryBuilder');

$notaire = $qb->findOne('notaire', $options);

/** @var Notaire $modelNotaire */
$modelNotaire = mvc_model('notaire');

$modelNotaire->majNotaireRole($notaire);

die ('OK');


