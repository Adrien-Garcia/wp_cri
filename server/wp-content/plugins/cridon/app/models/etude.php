<?php

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
     * Import Releve action
     *
     * @throws Exception
     */
    public function importReleveconso()
    {
        // documents
        $Directory  = new RecursiveDirectoryIterator(CONST_IMPORT_RELEVECONSO_TEMP_PATH);
        $Iterator   = new RecursiveIteratorIterator($Directory);
        $documents  = new RegexIterator($Iterator, '/^.+\.pdf$/i', RecursiveRegexIterator::GET_MATCH);

        // offset block
        $limit      = 1000;

        // date
        $date = date('Ym');

        $documentModel = mvc_model('Document');

        $this->importRelevePdf($documents, $Iterator, $limit, $date, $documentModel);
    }

    /**
     * Import des fichiers de façon iteratif
     *
     * @param array $documents
     * @param mixed $Iterator
     * @param int $limit
     * @param string $date
     * @param mixed $documentModel
     */
    protected function importRelevePdf($documents, $Iterator, $limit, $date, $documentModel)
    {
        foreach(new LimitIterator($documents, 0, $limit + 1) as $document) {
            try {
                if (!empty($document[0])) { // document existe
                    $fileInfo = pathinfo($document[0]);
                    if (!empty($fileInfo['basename'])) {
                        // filtre les fichiers selon la regle de nommage predefinie
                        // ACs : <CRPCEN_releveconso_AAAAMMJJ>.pdf
                        // @see \Config::$importRelevePattern
                        if (preg_match_all(Config::$importRelevePattern, $fileInfo['basename'], $matches)) {
                            $path = CONST_IMPORT_RELEVECONSO_PATH . $date . DIRECTORY_SEPARATOR;
                            if (!file_exists($path)) { // repertoire manquant
                                // creation du nouveau repertoire
                                wp_mkdir_p($path);
                            }
                            // CRPCEN present
                            if (!empty($matches[1][0]) && rename($document[0], $path . $fileInfo['basename'])) {
                                $crpcen      = $matches[1][0];

                                // nouveau document
                                if (!$documentModel->isDocExiste($fileInfo['basename'])) {
                                    // donnees document
                                    $docData = array(
                                        'Document' => array(
                                            'file_path'     => '/releveconso/' . $date . '/' . $fileInfo['basename'],
                                            'download_url'  => '/documents/download/' . $crpcen,
                                            'date_modified' => date('Y-m-d H:i:s'),
                                            'type'          => CONST_DOC_TYPE_RELEVECONSO,
                                            'id_externe'    => $crpcen,
                                            'name'          => $fileInfo['basename'],
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
                                    }
                                }
                            }
                        }
                    }
                    // liberation des variables
                    unset($fileInfo);
                    unset($document);
                    unset($matches);
                    unset($crpcen);
                }
            } catch(Exception $e) {
                // renommage fichier d'erreur
                rename($document[0],
                       str_replace(
                           array('.pdf', '.PDF', '.Pdf'),
                           array('.pdf.error', '.PDF.error', '.Pdf.error'),
                           $document[0]
                       )
                );

                writeLog($e, 'importreleveconso.log');
            }
        }

        $documents  = new RegexIterator($Iterator, '/^.+\.pdf$/i', RecursiveRegexIterator::GET_MATCH);
        // test s'il y a encore de fichier
        $documents->next();
        $doc = $documents->current();
        if (!empty($doc)) {
            // appel action d'import
            $documents->rewind();
            $this->importRelevePdf($documents, $Iterator, $limit, $date, $documentModel);
        }
    }

}