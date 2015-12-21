<?php

// set utf-8 encoding
header('Content-type: text/plain; charset=utf-8');

// load WP Core
require_once '../wp-load.php';

$csvParser = new CridonCsvParser();
$uploadDir = wp_upload_dir();
// Indexes for CSV files
$indexes = array(
    /* id  */
    'INDEX_ID'             => 0,
    /* libéllé */
    'INDEX_LABEL'          => 1,
    /* libéllé court */
    'INDEX_SHORT_LABEL'    => 2,
    /* code matière */
    'INDEX_CODE_MATIERE'   => 3,
    /* index Page CAB */
    'INDEX_DISPLAYED'      => 4
);
$files = glob($uploadDir['basedir'] . '/import/importsCompetence/*.csv');
$bool = array('oui' => 1,'non' => 0);
// LOGs
$count = 0;
$errorList = array();
try {
    $csvParser->enclosure = '';
    $csvParser->encoding(null, 'UTF-8');
    $csvParser->auto($files[0]);
    if (property_exists($csvParser, 'data') && intval($csvParser->error) <= 0) {
        foreach( $csvParser->data as $data ){ 
            //Nombre de colonne
            if( count($data) != 5 ){
                $errorList[] = 'Format CSV : id = '.$data[$indexes['INDEX_ID']];
                continue;
            }
            //Vérification de la présence de la matière
            if( empty($data[$indexes['INDEX_CODE_MATIERE']]) ){
                $errorList[] = 'No Matiere : id = '.$data[$indexes['INDEX_ID']];
                continue;
            }
            //Si la compétence est une matière
            if( empty( $data[$indexes['INDEX_LABEL']]) && empty( $data[$indexes['INDEX_SHORT_LABEL']])){
                $obj = mvc_model('Matiere')->find_one_by_code($data[$indexes['INDEX_CODE_MATIERE']]);
                if( $obj ){
                    $data[$indexes['INDEX_LABEL']] = $obj->label;
                    $data[$indexes['INDEX_SHORT_LABEL']] = $obj->short_label;
                }
            }
            //Conversion en valeur booléen
            if(array_key_exists( strtolower($data[$indexes['INDEX_DISPLAYED']]), $bool )){
                $data[$indexes['INDEX_DISPLAYED']] = $bool[strtolower($data[$indexes['INDEX_DISPLAYED']])];
            }else{
                $data[$indexes['INDEX_DISPLAYED']] = 0;
            }
            
            $newData = array(
                'Competence' => array(
                    'id'            => $data[$indexes['INDEX_ID']],
                    'label'         => $data[$indexes['INDEX_LABEL']],
                    'short_label'   => $data[$indexes['INDEX_SHORT_LABEL']],
                    'code_matiere'  => $data[$indexes['INDEX_CODE_MATIERE']],
                    'displayed'     => $data[$indexes['INDEX_DISPLAYED']]
                )
            );
            //Insertion de la compétence
            mvc_model('Competence')->create($newData);
            $count++; 
        }
    }else{
        $errorList[] = 'Empty CSV : '.pathinfo($files[0])['basename'];
    }
} catch (Exception $ex) {
    writeLog($ex, 'importcompetence.log');
}

echo 'OK : '.$count;
echo "\n";
echo 'KO : '.count($errorList);
writeLog($errorList, 'importcompetence.log');