<?php
/**
 * Description of reset_formation_catalog.php
 *
 * @package wp_cridon
 */

// load WP Core
require_once '../wp-load.php';

// Mis à jour des prix cridonline d'une année sur l'autre : les prix de l'année N+1 sont copiés dans ceux de l'année N. Les N+1 restent inchangés
if(mvc_model('Entite')->yearlyUpdateCridonlinePrices()){
    echo 'OK';
} else {
    echo 'KO';
}