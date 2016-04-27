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
$sql = "
        SELECT
                `d`.`id_externe`, `d`.`file_path`, `d`.`label` AS aaaammdd
        FROM
                cri_document d
        WHERE `type` = 'releveconso'
      ";
$resp = $wpdb->get_results($sql);

$listDocs       = array();
$listDocPath    = array();
foreach ($resp as $item) {
    if (!empty($item->aaaammdd)) {
        // recuperation de l'annee AAAA
        $year = substr($item->aaaammdd, 0, 4);
        // exclure l'annee en cours
        if ($year < date('Y')) {
            // regroupement de tous les docs de meme annee par etude
            $listDocs[$year][$item->id_externe][] = $item->aaaammdd;
            // chemin absolu correspondant au doc (utile pour la suppression)
            $listDocPath[$item->id_externe][$item->aaaammdd] = $item->file_path;
        }
    }
}

/**
 * Traitement de purge des fichiers
 */
$uploadDir = wp_upload_dir();
foreach($listDocs as $year => $docByCRPCEN) {
    foreach($docByCRPCEN as $crpcen => $docs) {
        // tri par ordre croissant des elements
        sort($docs);

        // depile le dernier element (faut garder la derniere doc de recap)
        array_pop($docs);
        foreach($docs as $aaaammjj) {
            if (!empty($listDocPath[$crpcen][$aaaammjj])) {
                $filePath = $listDocPath[$crpcen][$aaaammjj];
                $file = $uploadDir['basedir'] . $listDocPath[$crpcen][$aaaammjj];

                // suppression enregistrement de la BDD
                $wpdb->query(" DELETE FROM {$wpdb->prefix}document
                               WHERE file_path = '{$filePath}'
                ");

                // supression physique du fichier correspondant
                @unlink($file);
            }
        }
    }
}
echo CONST_STATUS_CODE_OK;