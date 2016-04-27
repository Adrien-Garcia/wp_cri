<?php

/**
 * Class Etude
 */
class Etude extends \App\Override\Model\CridonMvcModel {
    public $primary_key = 'crpcen';
    public $display_field = 'office_name';
    public $table = '{prefix}etude';
    var $has_many       = array(
        'Notaire' => array(
            'foreign_key' => 'crpcen'
        )
    );
    var $includes       = array('Sigle');
    var $belongs_to     = array(
        'Sigle' => array('foreign_key' => 'id_sigle')
    );

    public function getSubscriptionPrice($etude,$isNext = true){


        $level = ($isNext && !empty($etude->next_subscription_level)) ? $etude->next_subscription_level : $etude->subscription_level;

        $options = array('conditions' => array('crpcen' => $etude->crpcen));
        $nbCollaboratorEtude = count(mvc_model('QueryBuilder')->countItems('notaire', $options));
        $prices = Config::$pricesLevelsVeilles[0][$level];
        krsort($prices);
        // Tri du tableau de prix par clé descendante
        foreach($prices as $nbCollaborator => $price) {
            if ($nbCollaboratorEtude >= $nbCollaborator) {
                return $price;
            }
        }
    }

    /**
     * Import facture action
     *
     * @throws Exception
     */
    public function importFacture()
    {
        $this->importByType();
    }

    /**
     * Import des fichiers de façon iteratif
     *
     * @param Iterator $documents
     * @param mixed $Iterator
     * @param int $limit
     * @param string $date
     * @param mixed $documentModel
     * @param string $type
     */
    protected function importPdf($documents, $Iterator, $limit, $date, $documentModel, $type)
    {
        // destination
        $pathDest      = CONST_IMPORT_FACTURE_PATH;
        // pattern import (recuperation des infos par nom de fichier)
        $pattern       = Config::$importFacturePattern;
        // patter de parsage de fichier dans repertoire source
        $parserPattern = Config::$importFactureParserPattern;
        // chemin de base
        $filePath      = '/factures/';
        // fichier log
        $logFile       = 'importfactures.log';

        // reafectation variable selon le type de traitement
        if ($type == 'releveconso') {
            $pathDest      = CONST_IMPORT_RELEVECONSO_PATH;
            $pattern       = Config::$importRelevePattern;
            $parserPattern = Config::$importReleveParserPattern;
            $filePath      = '/releveconso/';
            $logFile       = 'importreleveconso.log';
        }
        // parsage des documents
        foreach (new LimitIterator($documents, 0, $limit + 1) as $document) {
            try {
                if (!empty($document[0])) { // document existe
                    $fileInfo = pathinfo($document[0]);
                    if (!empty($fileInfo['basename']) && preg_match($pattern, $fileInfo['basename'], $matches)) {
                        $path = $pathDest . $date . DIRECTORY_SEPARATOR;
                        if (!file_exists($path)) { // repertoire manquant
                            // creation du nouveau repertoire
                            wp_mkdir_p($path);
                        }
                        // CRPCEN present
                        if (!empty($matches[1]) && rename($document[0], $path . $fileInfo['basename'])) {
                            $crpcen   = $matches[1];
                            $typeFact = (!empty($matches[3])) ? $matches[3] : ' '; // vide pour autres que facture

                            // donnees document
                            $docData = array(
                                'Document' => array(
                                    'file_path'     => $filePath . $date . '/' . $fileInfo['basename'],
                                    'download_url'  => '/documents/download/' . $crpcen,
                                    'date_modified' => date('Y-m-d H:i:s'),
                                    'type'          => $type,
                                    'id_externe'    => $crpcen,
                                    'name'          => $fileInfo['basename'],
                                    'label'         => $typeFact
                                )
                            );

                            // insertion données
                            $documentId = $documentModel->create($docData);

                            // maj download_url
                            if ($documentId) {
                                $docData = array(
                                    'Document' => array(
                                        'id'           => $documentId,
                                        'download_url' => '/documents/download/' . $documentId
                                    )
                                );
                                $documentModel->save($docData);

                                unset($crpcen);
                                unset($typeFact);
                            }
                        }
                        // liberation de variables
                        unset($matches);
                    }
                    // liberation des variables
                    unset($fileInfo);
                    unset($document);
                }
            } catch (Exception $e) {
                // renommage fichier d'erreur
                rename($document[0], $document[0] . '.error');

                writeLog($e, $logFile);
            }
        }

        $documents = new RegexIterator($Iterator, $parserPattern, RecursiveRegexIterator::GET_MATCH);
        // test s'il y a encore de fichier
        $documents->next();
        $doc = $documents->current();
        if (!empty($doc)) {
            // appel action d'import
            $documents->rewind();
            $this->importPdf($documents, $Iterator, $limit, $date, $documentModel, $type);
        }
    }

    /**
     * Import de fichier par type (facture, releveconso)
     *
     * @param string $type
     * @throws Exception
     */
    protected function importByType($type = 'facture')
    {
        // bloc commun
        // offset block
        $limit      = 1000;

        // date
        $date = date('Ym');

        // model document
        $documentModel = mvc_model('Document');

        switch ($type) {
            case 'releveconso';
                // documents
                $Directory  = new RecursiveDirectoryIterator(CONST_IMPORT_RELEVECONSO_TEMP_PATH);
                $Iterator   = new RecursiveIteratorIterator($Directory);
                // filtre les fichiers selon la regle de nommage predefinie
                // ACs : <CRPCEN_releveconso_AAAAMMJJ>.pdf
                // @see \Config::$importReleveParserPattern
                $documents  = new RegexIterator($Iterator, Config::$importReleveParserPattern, RecursiveRegexIterator::GET_MATCH);

                break;
            default:
                // documents
                $Directory = new RecursiveDirectoryIterator(CONST_IMPORT_FACTURE_TEMP_PATH);
                $Iterator  = new RecursiveIteratorIterator($Directory);
                // filtre les fichiers selon la regle de nommage predefinie
                // ACs : <CRPCEN_NUMFACTURE_TYPEFACTURE_AAAAMMJJ>.pdf
                // @see \Config::$importFactureParserPattern
                $documents = new RegexIterator($Iterator, Config::$importFactureParserPattern, RecursiveRegexIterator::GET_MATCH);

                break;
        }
        // import documents
        $this->importPdf($documents, $Iterator, $limit, $date, $documentModel, $type);
    }


    /**
     * Import Releve action
     *
     * @throws Exception
     */
    public function importReleveconso()
    {
        $this->importByType('releveconso');
    }
}