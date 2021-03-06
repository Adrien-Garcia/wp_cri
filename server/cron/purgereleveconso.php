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
        WHERE `type` = '".CONST_DOC_TYPE_RELEVECONSO."' AND `label` NOT LIKE '{$currYear}%'
        ORDER BY `id_externe`, `label` DESC
      ";
// recuperation resultats
$conso = $wpdb->get_results($sql);

// traitement des resultats
foreach ($conso as $key => $item) {
    if (!empty($item->aaaammdd)) {
        // recuperation de l'annee AAAA
        $year = substr($item->aaaammdd, 0, 4);
        if ($key == 0) { // L'année la plus récente (cf ORDER BY) est conservée
            // (ensuite, c'est le changement d'étude ou d'année qui permettra de conserver le dernier doc)
            $crpcenLast = $item->crpcen;
            $yearLast   = $year;
            $dateLast   = $item->aaaammdd;
        } else { // prochaines iterations
            if ($crpcenLast == $item->crpcen) { // meme crpcen
                if ($yearLast == $year && $item->aaaammdd < $dateLast) { // document meme annee
                    $file = $uploadDir['basedir'] . $item->file_path;

                    // suppression enregistrement de la BDD
                    $wpdb->query(" DELETE FROM {$wpdb->prefix}document WHERE file_path = '{$item->file_path}' ");

                    // supression physique du fichier correspondant
                    @unlink($file);
                } else { // on change d'annee, donc on maj les variables, mais on ne unlink pas le fichier
                    $crpcenLast = $item->crpcen;
                    $yearLast   = $year;
                    $dateLast   = $item->aaaammdd;
                }
            } else { // on passe au prochain crpcen, sans supprimer le premier document de la liste
                // (et donc le dernier de l'année, puisque ORDER BY DESC)
                $crpcenLast = $item->crpcen;
                $yearLast   = $year;
                $dateLast   = $item->aaaammdd;
            }
        }
    }
}
echo CONST_STATUS_CODE_OK;
