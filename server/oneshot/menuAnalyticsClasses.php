<?php
if (!defined( 'WP_ADMIN' )) {
    define('WP_ADMIN', true);
}
// load WP Core
require_once '../wp-load.php';

global $wpdb;

/**
 * Ajout de classe pour analytics sur les liens du menu
 * @var string $metaKey
 * @var string $q
 */
$metaKey = '_menu_item_classes';
$q = "SELECT `pm`.`meta_value` AS classes, `pm`.`meta_id`, `pm`.`post_id`,`p`.`post_name`
          FROM `cri_postmeta` pm
          JOIN `cri_posts` p ON `p`.`ID` = `pm`.`post_id`
          WHERE `pm`.`meta_key` = '$metaKey'
          AND `p`.`post_name` IN (
          'urgente',
          'normal',
          'semaine',
          'demande-de-documentation',
          'prendre-un-rendez-vous'
          )";

$menuClasses = $wpdb->get_results($q) ;

foreach ($menuClasses as $item) {
    $classes = unserialize($item->classes);
    $class = '';
    if ($item->post_name == 'urgente'){
        $class = 'analytics_Urgent_question';
    }
    if ($item->post_name == 'semaine'){
        $class = 'analytics_Semaine_question';
    }
    if ($item->post_name == 'normal'){
        $class = 'analytics_Normal_question';
    }
    if ($item->post_name == 'demande-de-documentation'){
        $class = 'analytics_Demande_doc';
    }
    if ($item->post_name == 'prendre-un-rendez-vous'){
        $class = 'analytics_Demande_rdv';
    }
    if ($class && !in_array($class,$classes)) {
        if (count($classes) == 1 && $classes[0] === "") {
            $classes[0] = $class;
        } else {
            $classes[] = $class;
        }
        update_post_meta($item->post_id, $metaKey, $classes);
    }
}

echo 'Traitement OK ';
