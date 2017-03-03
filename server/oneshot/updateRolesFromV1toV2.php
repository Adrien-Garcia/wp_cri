<?php
/*
 * This file is part of the JETPULP wp_cridon project.
 *
 * Copyright (C) JETPULP
 */

// set utf-8 encoding
header('Content-type: text/html; charset=utf-8');
if (!defined( 'WP_ADMIN' )) {
    define('WP_ADMIN', true);
}
// load WP Core
require_once '../wp-load.php';
// notaire role
if ( !defined( 'CONST_NOTAIRE_ROLE' ) ) {
    define( 'CONST_NOTAIRE_ROLE', 'notaire' );
}

if ( !defined( 'CONST_CONNAISANCE_ROLE' ) ) {
    define( 'CONST_CONNAISANCE_ROLE', 'accesconnaissance' );
}

if ( !defined( 'CONST_NOTAIRE_DIV_ROLE' ) ) {
    define( 'CONST_NOTAIRE_DIV_ROLE', 'notaire_div' );
}

if ( !defined( 'CONST_NOTAIRE_ORG_ROLE' ) ) {
    define( 'CONST_NOTAIRE_ORG_ROLE', 'notaire_org' );
}

if ( !defined( 'CONST_FINANCE_ROLE' ) ) {
    define( 'CONST_FINANCE_ROLE', 'accesfinances' );
}

add_role(CONST_VEILLES_ROLE, 'Accès aux Veilles (Niv1)');
add_role(CONST_FLASH_ROLE, 'Accès aux actus Flash');
add_role(CONST_CAHIERS_ROLE, 'Accès aux Cahiers du Cridon');
add_role(CONST_SINEQUA_ROLE, 'Accès au moteur de recherche Sinequa');
add_role(CONST_CRIDONLINE_ROLE, 'Accès à la plateforme Crid\'Online');
add_role(CONST_DROITS_COLLABORATEUR_ROLE, 'Gestion des droits des collaborateurs');
add_role(CONST_REGLES_ROLE, 'Accès aux règles de facturation');
add_role(CONST_FACTURES_ROLE, 'Accès au détail des factures');
add_role(CONST_CONSO_ROLE, 'Accès aux relevés de consommation');
add_role(CONST_DASHBOARD_ROLE, 'Accès au dashboard');
add_role(CONST_PRIVATEPAGES_ROLE, 'Accès aux pages sécurisées');

global $wpdb;

$sql = 'SELECT * FROM ' . $wpdb->prefix . 'notaire';

$notaires = $wpdb->get_results($sql);

foreach ($notaires as $notaire) {
    $user = new WP_User($notaire->id_wp_user);
    // user must be an instance of WP_User vs WP_Error
    if ($user instanceof WP_User) {
        if (in_array(CONST_NOTAIRE_ROLE, $user->roles)) {
            $user->add_role(CONST_DASHBOARD_ROLE);
            $user->add_role(CONST_PRIVATEPAGES_ROLE);
            // DEPRECATED
            $user->remove_role(CONST_NOTAIRE_ROLE);
        }
        if (in_array(CONST_CONNAISANCE_ROLE, $user->roles)) {
            $user->add_role(CONST_VEILLES_ROLE);
            $user->add_role(CONST_FLASH_ROLE);
            $user->add_role(CONST_CAHIERS_ROLE);
            $user->add_role(CONST_SINEQUA_ROLE);
            $user->add_role(CONST_CRIDONLINE_ROLE);
            // DEPRECATED
            $user->remove_role(CONST_CONNAISANCE_ROLE);
        }
        if (in_array(CONST_FINANCE_ROLE, $user->roles)) {
            $user->add_role(CONST_FACTURES_ROLE);
            $user->add_role(CONST_REGLES_ROLE);
            $user->add_role(CONST_CONSO_ROLE);
            // DEPRECATED
            $user->remove_role(CONST_FINANCE_ROLE);
        }
        if (in_array(CONST_COLLABORATEUR_TAB_ROLE, $user->roles)) {
            $user->add_role(CONST_DROITS_COLLABORATEUR_ROLE);
        }
        if (in_array(CONST_NOTAIRE_DIV_ROLE, $user->roles)) {
            $user->remove_role(CONST_NOTAIRE_DIV_ROLE);
        }
        if (in_array(CONST_NOTAIRE_ORG_ROLE, $user->roles)) {
            $user->remove_role(CONST_NOTAIRE_ORG_ROLE);
        }
    }
}

remove_role(CONST_NOTAIRE_ROLE);
remove_role(CONST_CONNAISANCE_ROLE);
remove_role(CONST_NOTAIRE_DIV_ROLE);
remove_role(CONST_NOTAIRE_ORG_ROLE);
remove_role(CONST_FINANCE_ROLE);