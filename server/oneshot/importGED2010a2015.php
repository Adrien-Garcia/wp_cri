<?php
/**
 * Created by JETPULP.
 * User: valbert
 * Date: 08/12/2015
 * Time: 15:14
 *
 * Import des documents de la GED de 2010 au 30/09/2015.
 * Format d'import :
 * présence d'un fichier csv avec les métadonnées, et des fichiers PDFs isolés dans l'arborescence des dossiers
 */

// set utf-8 encoding
header('Content-type: text/plain; charset=utf-8');
if (!defined( 'WP_ADMIN' )) {
    define('WP_ADMIN', true);
}
// load WP Core
require_once '../wp-load.php';

// Indexes for CSV files
$indexes = array(
    /* index Nom du fichier PDF  */
    'INDEX_ID'              => 0,
    /* index N° Question */
    'INDEX_NUMQUESTION'     => 1,
    /* index Nombre de pages du document PDF */
    'INDEX_NBPAGEDOC'       => 2,
    /* index Date de numérisation du document PDF */
    'INDEX_DATENUM'         => 3,
    /* index Page CAB */
    'INDEX_PAGECAB'         => 4,
    /* index Valeur CAB */
    'INDEX_VALCAB'          => 5,
    /* index PDF Path */
    'INDEX_PDF'             => 6,
    /* index Date de numérisation du document PDF */
    'INDEX_DATETRANS'       => 7,
);

$csvFiles = glob(CONST_IMPORT_DOCUMENT_ORIGINAL_PATH.'BackupCourriersPDF'.DIRECTORY_SEPARATOR.'*.csv');
$csvFile = reset($csvFiles);

//OFFSET
$offset = 0;
$pack = $limit = (int) isset($argv[2]) ? (int) $argv[2] : (isset($_GET['pack']) ? $_GET['pack'] : 0);
if (isset($argv[1]) && !empty($pack)) {
    $offset = (int) $argv[1] * $pack;
    $limit = $offset + $pack;
} elseif (isset($_GET['offset']) && !empty($pack)) {
    $offset = (int) $_GET['offset'] * $pack;
    $limit = $offset + $pack;
}

//argv[3] -> is delta ? (only uploading files ; no update)
if ((!empty($argv[3]) && $argv[3] == 'delta') || (!empty($_GET['delta']) && $_GET['delta'] == true)){
    $delta = true;
}

// LOGs
$logDocList = array();
$errorDocList = array();

$csvParser = new parseCSV();
$csvParser->delimiter = ";";
$csvParser->heading = $offset ? false : true;
if (!empty($pack)) {
    $csvParser->parse($csvFile, $offset, $limit);
} else {
    $csvParser->parse($csvFile);
}

/**
 * @var $modelDoc Document
 */
$modelDoc = mvc_model('Document');
foreach ($csvParser->data as $row => $data) {
    $contents = array_values($data);// transform into numeric keys in order to use $indexes (more convenient)

    // extract transmission date for current PDF
    $transDate = explode(' ', $contents[$indexes['INDEX_DATETRANS']])[0];
    $transDate = explode('-', $transDate);
    $transDate = $transDate[0].$transDate[1]; //YYYYMM
    // repertoire archivage des documents
    $archivePath = CONST_IMPORT_DOCUMENT_ORIGINAL_PATH . '..'.DIRECTORY_SEPARATOR.'archives'. DIRECTORY_SEPARATOR .$transDate . DIRECTORY_SEPARATOR;
    if (!file_exists($archivePath)) { // repertoire manquant
        // creation du nouveau repertoire
        wp_mkdir_p($archivePath);
    }

    // recuperation id question par numero
    $srenum = $contents[$indexes['INDEX_NUMQUESTION']];
    while (strlen($srenum) < 6) {
        $srenum = '0'.$srenum;
    }
    $options               = array();
    $options['attributes'] = array('id');
    $options['model']      = 'question';
    $options['conditions'] = ' srenum = \'' . $srenum .'\'';
    // question associée
    $question = $modelDoc->queryBuilder->findOneByOptions($options);

    // question exist
    if ($question->id) {
        //Recherche de l'existance d'un document lié à la question
        // recuperation id question par numero
        $options               = array();
        $options['attributes'] = array('id,cab','file_path');
        $options['model']      = 'document';
        $options['conditions'] = ' id_externe = ' . $question->id . ' AND type="question"';

        // document associé
        $docs = $modelDoc->queryBuilder->find($options);
        /*
         * Type d'action à effectué
         * 1 : insertion de nouveau document
         * 2 : insertion de nouveau document suite/complément
         * 3 : mise à jour du document
         */
        $typeAction = 1;//ajout simple
        $cabs = array();//tableau contenant les CAB des docs en base
        $filepaths = array();//tableau contenant les chemins des docs en base
        foreach ( $docs as $doc ){
            $cabs[$doc->id] = $doc->cab;
            $filepaths[$doc->id] = $doc->file_path;
        }
        $cabs = array_unique($cabs);
        //Si le CAB existe dans csv
        if( !empty( $docs ) && !empty($contents[$indexes['INDEX_VALCAB']]) ){
            //Si le CAB du csv existe parmi les CAB dans les docs du site
            if( in_array( $contents[$indexes['INDEX_VALCAB']],$cabs ) ){
                if ($delta){
                    continue;
                }
                $typeAction = 3;//mise à jour
            }else{
                $typeAction = 2;//complément/suite
            }
        }
        // copy document dans Site
        $uploadDir = wp_upload_dir();
        $path      = $uploadDir['basedir'] . '/questions/' . $transDate . '/';
        if (!file_exists($path)) { // repertoire manquant
            // creation du nouveau repertoire
            wp_mkdir_p($path);
        }

        $docName = str_replace('\\', DIRECTORY_SEPARATOR, $contents[$indexes['INDEX_PDF']]);
        $docPath = substr(
            $docName,
            strpos($docName, 'BackupCourriersPDF') + strlen('BackupCourriersPDF') + 1,
            strrpos($docName, DIRECTORY_SEPARATOR)
        );
        $docName = substr($docName, strrpos($docName, DIRECTORY_SEPARATOR) + 1);
        if (($iPos = strpos($docName, '_')) !== false) {
            //a prefix can be mentionned in the file. Don't use it.
            $docName = substr($docName, $iPos + 1);
        }
        $filename = $modelDoc->getFileName($path, $docName);
        // preserve file as exists in metadata : fileExists might change it into FALSE if not found
        $storedInfoFile = CONST_IMPORT_DOCUMENT_ORIGINAL_PATH . 'BackupCourriersPDF' . DIRECTORY_SEPARATOR . $docPath;
        $fileToImport = fileExists($storedInfoFile, false);
        if (!empty($fileToImport) && copy($fileToImport,
            $path . $filename)) {
            // donnees document
            $docData = array(
                'Document' => array(
                    'file_path'     => '/questions/' . $transDate . '/' . $filename,
                    'download_url'  => '/documents/download/' . $question->id,
                    'date_modified' => date('Y-m-d H:i:s'),
                    'type'          => 'question',
                    'id_externe'    => $question->id,
                    'name'          => $filename,
                    'cab'           => $contents[$indexes['INDEX_VALCAB']],
                    'label'         => 'question/reponse'
                )
            );
            //Document suite/complément
            if (strpos($contents[Config::$GEDtxtIndexes['INDEX_VALCAB']], 'C') !== false) {
                $docData['Document']['label'] = 'Complément';
            } elseif (strpos($contents[Config::$GEDtxtIndexes['INDEX_VALCAB']], 'S') !== false) {
                $docData['Document']['label'] = 'Suite';
            }
            
            if( $typeAction != 3 ){
                // insertion
                $documentId = $modelDoc->create($docData);
            }else{
                //mise à jour
                $documentId = array_search($contents[$indexes['INDEX_VALCAB']], $cabs);
                $docData['Document']['id'] = $documentId;
                if( $modelDoc->save($docData) ){
                    if( isset($filepaths[$documentId]) && file_exists($uploadDir['basedir'].$filepaths[$documentId])){
                        unlink( $uploadDir['basedir'].$filepaths[$documentId] );
                    }
                }
            }

            // maj download_url
            $docData = array(
                'Document' => array(
                    'id'           => $documentId,
                    'download_url' => '/documents/download/' . $documentId
                )
            );
            $modelDoc->save($docData);

            // archivage PDF
            rename($fileToImport,
                $archivePath . $filename);
            $modelDoc->updateQuestion($question, $contents);
            $logDocList[] = $filename;
        } else {
            // message par défaut
            /*$message = sprintf(CONST_IMPORT_GED_LOG_CORRUPTED_CSV_MSG, date('d/m/Y à H:i'), $docName);
            // log : envoie mail
            if( !$fileToImport ){
                // PDF inexistant
                $message = sprintf(CONST_IMPORT_GED_LOG_CORRUPTED_PDF_MSG, date('d/m/Y à H:i'), $contents[$indexes['INDEX_NUMQUESTION']]);
                $message .= "\n PATH Chemin sur le serveur : ".$storedInfoFile;
            }*/
            //reportError($message, '');
            $errorDocList[] = '404 File : ' . $storedInfoFile . ' (doc_name : ' . $docName .')';
        }
    } else { // doc sans question associee

        // log : envoie mail
        //$message = sprintf(CONST_IMPORT_GED_LOG_DOC_WITHOUT_QUESTION_MSG, date('d/m/Y à H:i'), $contents[$indexes['INDEX_VALCAB']]);
        //reportError($message, '');
        $errorDocList[] = '404 Quest : ' . $contents[$indexes['INDEX_NUMQUESTION']];
    }
}

echo 'OK : '.count($logDocList);
echo "\n";
echo 'KO : '.count($errorDocList);
writeLog($errorDocList, 'import2010_2015.log');
