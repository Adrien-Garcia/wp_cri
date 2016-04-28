<?php
/**
 * Description of cleanusermetadata.php
 *
 * @package wp_cridon
 * @author eTech
 * @contributor Joelio
 */

// load WP Core
require_once '../wp-load.php';

global $wpdb;

/**
 * Suppression de tous les doublons causés par cron majnotaire
 * (appel de CriDisableAdminBarForExistingNotaire)
 *
 * @see  http://rootslabs.net/blog/423-mysql-comment-nettoyer-table-de-ces-doublons
 */
$sql = "DELETE cu FROM {$wpdb->usermeta} cu
        LEFT OUTER JOIN (
            SELECT MIN(`cm`.`umeta_id`) umeta_id, `cm`.`user_id`,
              `cm`.`meta_key`,
              `cm`.`meta_value`
            FROM
              `{$wpdb->usermeta}` cm
            GROUP BY `cm`.`user_id`,
              `cm`.`meta_key`,
              `cm`.`meta_value`) t1
            ON `cu`.`umeta_id` = t1.umeta_id
        WHERE t1.umeta_id IS NULL ";

$wpdb->query($sql);

echo 'Clean data done';