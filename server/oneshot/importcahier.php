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
// update default id_matiere for Flash and Veille model after modification on cri_matiere
// old value of default matiere = 12, now it's 14 (Expertise transversales)
$query = "UPDATE `cri_veille` SET `id_matiere` = 14 WHERE `id_matiere` = 12;
UPDATE `cri_flash` SET `id_matiere` = 14 WHERE `id_matiere` = 12;";

mvc_model('QueryBuilder')->getInstanceMysqli()->multi_query($query);