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

// documents
$Directory  = new RecursiveDirectoryIterator(CONST_IMPORT_RELEVECONSO_PATH);
$Iterator   = new RecursiveIteratorIterator($Directory);
$documents  = new RegexIterator($Iterator, '/^.+\.pdf$/i', RecursiveRegexIterator::GET_MATCH);

// tableau des elements à traiter
/**
 * $listDocs sous la forme :
 * array (
    2015 =>
        array (
            869017 =>
                array (
                    0 => '20150131',
                    1 => '20150228',
                    2 => '20150331',
                    3 => '20150430',
                    4 => '20150531',
                    5 => '20150630',
                    6 => '20150731',
                    7 => '20150831',
                    8 => '20150930',
                    9 => '20151031',
                    10 => '20151130',
                    11 => '20151231',
                ),
            869018 =>
                array (
                    0 => '20150131',
                    1 => '20150228',
                    2 => '20150331',
                    3 => '20150430',
                    4 => '20150531',
                    5 => '20150630',
                    6 => '20150731',
                    7 => '20150831',
                    8 => '20150930',
                    9 => '20151031',
                    10 => '20151130',
                    11 => '20151231',
                ),
        ),
    )
 */
$listDocs       = array();

/**
 * $listDocPath sous la forme :
 * array (
        869017 =>
            array (
                20150131 => 'C:\\wamp\\www\\wp_cridon_dev\\server/wp-content/uploads/releveconso\\201604\\869017_releveconso_20150131.pdf',
                20150228 => 'C:\\wamp\\www\\wp_cridon_dev\\server/wp-content/uploads/releveconso\\201604\\869017_releveconso_20150228.pdf',
                20150331 => 'C:\\wamp\\www\\wp_cridon_dev\\server/wp-content/uploads/releveconso\\201604\\869017_releveconso_20150331.pdf',
                20150430 => 'C:\\wamp\\www\\wp_cridon_dev\\server/wp-content/uploads/releveconso\\201604\\869017_releveconso_20150430.pdf',
                20150531 => 'C:\\wamp\\www\\wp_cridon_dev\\server/wp-content/uploads/releveconso\\201604\\869017_releveconso_20150531.pdf',
                20150630 => 'C:\\wamp\\www\\wp_cridon_dev\\server/wp-content/uploads/releveconso\\201604\\869017_releveconso_20150630.pdf',
                20150731 => 'C:\\wamp\\www\\wp_cridon_dev\\server/wp-content/uploads/releveconso\\201604\\869017_releveconso_20150731.pdf',
                20150831 => 'C:\\wamp\\www\\wp_cridon_dev\\server/wp-content/uploads/releveconso\\201604\\869017_releveconso_20150831.pdf',
                20150930 => 'C:\\wamp\\www\\wp_cridon_dev\\server/wp-content/uploads/releveconso\\201604\\869017_releveconso_20150930.pdf',
                20151031 => 'C:\\wamp\\www\\wp_cridon_dev\\server/wp-content/uploads/releveconso\\201604\\869017_releveconso_20151031.pdf',
                20151130 => 'C:\\wamp\\www\\wp_cridon_dev\\server/wp-content/uploads/releveconso\\201604\\869017_releveconso_20151130.pdf',
                20151231 => 'C:\\wamp\\www\\wp_cridon_dev\\server/wp-content/uploads/releveconso\\201604\\869017_releveconso_20151231.pdf',
            ),
        869018 =>
            array (
                    20150131 => 'C:\\wamp\\www\\wp_cridon_dev\\server/wp-content/uploads/releveconso\\201604\\869018_releveconso_20150131.pdf',
                    20150228 => 'C:\\wamp\\www\\wp_cridon_dev\\server/wp-content/uploads/releveconso\\201604\\869018_releveconso_20150228.pdf',
                    20150331 => 'C:\\wamp\\www\\wp_cridon_dev\\server/wp-content/uploads/releveconso\\201604\\869018_releveconso_20150331.pdf',
                    20150430 => 'C:\\wamp\\www\\wp_cridon_dev\\server/wp-content/uploads/releveconso\\201604\\869018_releveconso_20150430.pdf',
                    20150531 => 'C:\\wamp\\www\\wp_cridon_dev\\server/wp-content/uploads/releveconso\\201604\\869018_releveconso_20150531.pdf',
                    20150630 => 'C:\\wamp\\www\\wp_cridon_dev\\server/wp-content/uploads/releveconso\\201604\\869018_releveconso_20150630.pdf',
                    20150731 => 'C:\\wamp\\www\\wp_cridon_dev\\server/wp-content/uploads/releveconso\\201604\\869018_releveconso_20150731.pdf',
                    20150831 => 'C:\\wamp\\www\\wp_cridon_dev\\server/wp-content/uploads/releveconso\\201604\\869018_releveconso_20150831.pdf',
                    20150930 => 'C:\\wamp\\www\\wp_cridon_dev\\server/wp-content/uploads/releveconso\\201604\\869018_releveconso_20150930.pdf',
                    20151031 => 'C:\\wamp\\www\\wp_cridon_dev\\server/wp-content/uploads/releveconso\\201604\\869018_releveconso_20151031.pdf',
                    20151130 => 'C:\\wamp\\www\\wp_cridon_dev\\server/wp-content/uploads/releveconso\\201604\\869018_releveconso_20151130.pdf',
                    20151231 => 'C:\\wamp\\www\\wp_cridon_dev\\server/wp-content/uploads/releveconso\\201604\\869018_releveconso_20151231.pdf',
            ),
    )
 */
$listDocPath    = array();
foreach( $documents as $document ) {
    try {
        if (!empty($document[0])) { // document existe
            $fileInfo = pathinfo($document[0]);
            if (!empty($fileInfo['basename'])) {
                // filtre les fichiers selon la regle de nommage predefinie
                // ACs : <CRPCEN_releveconso_AAAAMMJJ>.pdf
                // @see \Config::$importRelevePattern
                if (preg_match_all(Config::$importRelevePattern, $fileInfo['basename'], $matches)) {
                    /**
                     * CRPCEN : $matches[1][0]
                     * releveconso : $matches[2][0]
                     * AAAAMMJJ : $matches[3][0]
                     */
                    if (!empty($matches[3][0])) {
                        // recuperation de l'annee AAAA
                        $year = substr($matches[3][0], 0, 4);
                        // exclure l'annee en cours
                        if ($year < date('Y')) {
                            // regroupement de tous les docs de meme annee par etude
                            $listDocs[$year][$matches[1][0]][] = $matches[3][0];
                            // chemin absolu correspondant au doc (utile pour la suppression)
                            $listDocPath[$matches[1][0]][$matches[3][0]] = $document[0];
                        }
                    }
                }
            }
        }
    } catch(Exception $e) {
        writeLog($e, 'purgereleveconso.log');
    }
}

// traitement des docs
$filesName = array();
foreach($listDocs as $year => $docByCRPCEN) {
    foreach($docByCRPCEN as $crpcen => $docs) {
        // tri par ordre croissant des elements
        sort($docs);

        // depile le dernier element
        array_pop($docs);
        foreach($docs as $aaaammjj) {
            if (!empty($listDocPath[$crpcen][$aaaammjj])) {
                $fileInfo = pathinfo($listDocPath[$crpcen][$aaaammjj]);
                // liste nom complet des fichiers
                // pour preparation requette de suppression dans la BDD
                $filesName[] = $fileInfo['basename'];

                // supression physique de tous les fichiers
                @unlink($listDocPath[$crpcen][$aaaammjj]);
            }
        }
    }
}

// liste nom de fichiers non vide
if (count($filesName) > 0) {
    // preparation nom de fichier pour suppression en masse
    $fileName = "'" . implode("', '", $filesName) . "'";

    // purge table cri_document
    global $wpdb;
    $wpdb->query(" DELETE FROM {$wpdb->prefix}document
                   WHERE name IN ({$fileName})
                ");
}
echo $code;