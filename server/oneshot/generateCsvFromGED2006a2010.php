<?php

// set utf-8 encoding
header('Content-type: text/plain; charset=utf-8');

// load WP Core
require_once '../wp-load.php';

// associated "matiere"
$associatedMat = array();
$modelMat = new Matiere();
$matieres = $modelMat->find();
foreach ($matieres as $matiere) {
    $associatedMat[$matiere->short_label] = $matiere->code;
}

// associated support
$associatedSupport = array(
    'Let.'  => 1,
    'Diane'  => 4,
    'Nofac'  => 5,
    '48H'  => 6,
    'UR48'  => 6,
    'UR48H'  => 6,
    'SEM'  => 7,
    'URSEM'  => 7,
);

// Indexes in parsed XML files using "simplexml_load_file"
$indexes = array(
    /* index SRENUM  */
    'SRENUM'            => 1,
    /* index SRENUM  */
    'CRPCEN'            => 2,
    /* index Objet  */
    'OBJET'             => 14,
    /* index matiere  */
    'MATIERE'           => 4,
    /* index Juriste  */
    'JURISTE'           => 8,
    /* index Support  */
    'SUPPORT'           => 5,
    /* index Date affectation  */
    'DATE_AFFECTATION'  => 6,
    /* index Date de reponse  */
    'DATE_REPONSE'      => 7,
    /* index Suite  */
    'SUITE'             => 9,
);

// data
$data = array();

// LOG
$errorDocList = array();

// documents
$Directory = new RecursiveDirectoryIterator(CONST_IMPORT_DOCUMENT_ORIGINAL_PATH.'BackupCourriersPDF2006'.DIRECTORY_SEPARATOR);
$Iterator = new RecursiveIteratorIterator($Directory);
// remove the ending symbol $ as a txt file contains a '0' behind the extension...
$documents = new RegexIterator($Iterator, '/^.+\.xml/i', RecursiveRegexIterator::GET_MATCH);

foreach($documents as $document) {
    $contents = array();
    try{
        $crxml = simplexml_load_file($document[0]);

        // valid XML File
        if(!method_exists($crxml->Index_Document[$indexes['SRENUM']]->VALEUR_NUMERIQUE, '__toString')
                || !method_exists($crxml->Index_Document[$indexes['SRENUM']]->ID_INDEX, '__toString')
                || !method_exists($crxml->Index[$indexes['SRENUM']]->ID_INDEX, '__toString')){
            throw new \ErrorException('Il y a une erreur dans le fichier '.pathinfo($document[0])['basename']. ' ('.$document[0].')');
        }

        $shortLabel = $crxml->Index_Document[$indexes['MATIERE']]->VALEUR_TEXTE->__toString();
        $supportLabel = $crxml->Index_Document[$indexes['SUPPORT']]->VALEUR_TEXTE->__toString();

        $contents[] = intval($crxml->Index_Document[$indexes['SRENUM']]->VALEUR_NUMERIQUE->__toString()); // SRENUM
        $contents[] = $crxml->Index_Document[$indexes['CRPCEN']]->VALEUR_TEXTE->__toString(); // CRPCEN
        $contents[] = utf8_decode($crxml->Index_Document[$indexes['OBJET']]->VALEUR_TEXTE->__toString()); // Objet
        $contents[] = isset($associatedMat[$shortLabel]) ? $associatedMat[$shortLabel] : $shortLabel; // Competence
        $contents[] = utf8_decode($crxml->Index_Document[$indexes['JURISTE']]->VALEUR_TEXTE->__toString()); // Juriste
        $contents[] = isset($associatedSupport[$supportLabel]) ? $associatedSupport[$supportLabel] : $supportLabel; // Support
        $contents[] = 4; // Code affectation
        $contents[] = date('Y-m-d', strtotime($crxml->Index_Document[$indexes['DATE_AFFECTATION']]->VALEUR_DATE->__toString())); // Date Creation
        $contents[] = date('Y-m-d', strtotime($crxml->Index_Document[$indexes['DATE_AFFECTATION']]->VALEUR_DATE->__toString())); // Date affectation
        $contents[] = date('Y-m-d', strtotime($crxml->Index_Document[$indexes['DATE_REPONSE']]->VALEUR_DATE->__toString())); // Date de reponse
        $contents[] = $crxml->Document->NOM_DOC_SOURCE->__toString(); // PDF
        $contents[] = $crxml->Index_Document[$indexes['SUITE']]->VALEUR_TEXTE->__toString(); // Suite

        // data
        $data[] = $contents;
    } catch(Exception $ex) {
        $errorDocList[] = $ex->getMessage();
    }
}
// create csv file
$csv = new parseCSV();
$csv->output('questions2006_2010.csv', $data, array('SRENUM', 'CRPCEN', 'Objet', 'Competence', 'Juriste', 'Support', 'Code affectation', 'Date Creation', 'Date affectation', 'Date de reponse', 'PDF', 'Suite'), ';');

writeLog($errorDocList, 'import2006_2010.log');