<?php
/**
 * Description of purgereleveconso.php
 *
 * @package wp_cridon
 * @author eTech
 * @contributor Joelio
 */

// load WP Core
require_once '../wp-load.php';

/**
 * Recuperation des documents à supprimer
 */
global $wpdb;

// recuperation upload path
$uploadDir = wp_upload_dir();

// preparation requette
$currYear = date('Y');
$sql = "
        SELECT
                `d`.`id_externe` crpcen, `d`.`file_path`, `d`.`label` aaaammdd
        FROM
                cri_document d
        WHERE `type` = 'releveconso' AND `label` NOT LIKE '{$currYear}%'
        ORDER BY `id_externe`, `label` DESC
      ";
// recuperation resultats
$conso = $wpdb->get_results($sql);

// traitement des resultats
foreach ($conso as $key => $item) {
    if (!empty($item->aaaammdd)) {
        // recuperation de l'annee AAAA
        $year = substr($item->aaaammdd, 0, 4);
        if ($key == 0) { // premiere iteration
            $crpcenTemp = $item->crpcen;
            $yearTemp   = $year;
            $dateTemp   = $item->aaaammdd;
        } else { // prochaines iterations
            if ($crpcenTemp == $item->crpcen) { // meme crpcen
                if ($yearTemp == $year && $item->aaaammdd < $dateTemp) { // document meme annee
                    $file = $uploadDir['basedir'] . $item->file_path;

                    // suppression enregistrement de la BDD
                    $wpdb->query(" DELETE FROM {$wpdb->prefix}document WHERE file_path = '{$item->file_path}' ");

                    // supression physique du fichier correspondant
                    @unlink($file);
                } else { // on change d'annee
                    $crpcenTemp = $item->crpcen;
                    $yearTemp   = $year;
                    $dateTemp   = $item->aaaammdd;
                }
            } else { // on passe au prochain crpcen
                $crpcenTemp = $item->crpcen;
                $yearTemp   = $year;
                $dateTemp   = $item->aaaammdd;
            }
        }
    }
}
echo CONST_STATUS_CODE_OK;