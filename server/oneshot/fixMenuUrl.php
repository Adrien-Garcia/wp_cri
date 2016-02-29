<?php
/**
 * Description of fixmenuurl.php
 *
 * @package wp_cridon
 * @author eTech
 * @contributor Joelio
 */

// set utf-8 encoding
//header('Content-type: text/plain; charset=utf-8');

// load WP Core
require_once '../wp-load.php';

global $wpdb;

/**
 * Mise à jour des urls des menus deja initialisés
 * @var string $metaKey
 * @var string $q
 */
$metaKey = '_menu_item_url';
$q = "SELECT `pm`.`meta_value` AS url, `pm`.`meta_id`, `pm`.`post_id`
          FROM `cri_postmeta` pm
          WHERE `pm`.`meta_key` = '$metaKey' ";

$menuUrls = $wpdb->get_results($q) ;

$postModels = array(
    'veilles',
    'cahier_cridons',
    'flashes',
);
foreach ($menuUrls as $item) {
    if (preg_match('#/(veilles|cahier_cridons|flashes|matieres)/(.*)#i', $item->url, $matches)) {
        if (isset($matches[2]) && $matches[2]) {
            $itemId = str_replace('/', '', $matches[2]);
            if (intval($itemId) > 0) {
                $id = intval($itemId);
                $model = mvc_model(MvcInflector::singularize($matches[1]));

                if (in_array($matches[1], $postModels)) { // utilisation de Post.post_name
                    $newUrl = mvc_public_url(array(
                        'controller' => $matches[1],
                        'id' => $model->find_by_id($id)->post->post_name,
                    ));
                    // maj url
                    update_post_meta($item->post_id, $metaKey, $newUrl);
                } elseif($matches[1] == 'matieres') { // Matiere.virtual_name
                    $newUrl = mvc_public_url(array(
                        'controller' => $matches[1],
                        'id' => $model->find_by_id($id)->virtual_name,
                    ));
                    // maj url
                    update_post_meta($item->post_id, $metaKey, $newUrl);
                }
            }
        }
    }
}

echo 'Traitement OK ';