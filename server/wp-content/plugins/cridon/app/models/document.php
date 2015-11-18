<?php

class Document extends MvcModel {

    var $display_field = 'file_path';
    var $table         = '{prefix}document';

    /**
     * @var mixed
     */
    protected $queryBuilder;

    public function create($data) {
        //Before insert 
        $date = new DateTime('now');
        $data[ 'Document' ][ 'date_modified' ] = $date->format('Y-m-d H:i:s');
        return parent::create($data);
    }
    public function save($data) {
        //Before update
        $date = new DateTime('now');
        $data[ 'Document' ][ 'date_modified' ] = $date->format('Y-m-d H:i:s');
        return parent::save($data);
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        global $wpdb;
        $this->wpdb         = $wpdb;
        $this->queryBuilder = mvc_model('QueryBuilder');

        parent::__construct();
    }

    /**
     * Import initial
     */
    public function importInitial()
    {
        // doc list pour log
        $logDocList = array();

        // recupere tous les fichiers
        $documents = glob(CONST_IMPORT_DOCUMENT_ORIGINAL_PATH . '/*.' . CONST_IMPORT_FILE_TYPE);

        // repertoire non vide
        if (count($documents) > 0) {
            // parse la liste des fichiers
            foreach ($documents as $document) {
                // info fichier
                $fileInfo = pathinfo($document);

                // recuperation contenu fichier s'il existe
                if (file_exists($document)) {
                    $content  = file_get_contents($document);
                    $contents = explode(CONST_IMPORT_GED_CONTENT_SEPARATOR, $content);

                    // recuperation id question par numero
                    $options               = array();
                    $options['attributes'] = array('id');
                    $options['model']      = 'question';
                    $options['conditions'] = ' srenum = ' . $contents[CridonGedParser::INDEX_NUMQUESTION];

                    // question associée
                    $question = $this->queryBuilder->findOneByOptions($options);

                    // repertoire archivage des documents
                    $archivePath = CONST_IMPORT_DOCUMENT_ORIGINAL_PATH . '/archives/' . date('YmdHi') . '/';
                    if (!file_exists($archivePath)) { // repertoire manquant
                        // creation du nouveau repertoire
                        wp_mkdir_p($archivePath);
                    }

                    // question exist
                    if ($question->id) {
                        // copy document dans Site
                        $uploadDir = wp_upload_dir();
                        $path      = $uploadDir['basedir'] . '/questions/' . $question->id . '/';
                        if (!file_exists($path)) { // repertoire manquant
                            // creation du nouveau repertoire
                            wp_mkdir_p($path);
                        }
                        if (@copy(CONST_IMPORT_DOCUMENT_ORIGINAL_PATH . '/' . $contents[CridonGedParser::INDEX_NOMFICHIER],
                                 $path . $contents[CridonGedParser::INDEX_NOMFICHIER])) {
                            // donnees document
                            $docData = array(
                                'Document' => array(
                                    'file_path'     => '/questions/' . $question->id . '/' . $contents[CridonGedParser::INDEX_NOMFICHIER],
                                    'download_url'  => '/documents/download/' . $question->id,
                                    'date_modified' => date('Y-m-d H:i:s'),
                                    'type'          => 'question',
                                    'id_externe'    => $question->id,
                                )
                            );

                            // insertion
                            $documentId = mvc_model('Document')->create($docData);

                            // maj download_url
                            $docData = array(
                                'Document' => array(
                                    'id'           => $documentId,
                                    'download_url' => '/documents/download/' . $documentId
                                )
                            );
                            mvc_model('Document')->save($docData);

                            // archivage PDF
                            rename(CONST_IMPORT_DOCUMENT_ORIGINAL_PATH . '/' . $contents[CridonGedParser::INDEX_NOMFICHIER],
                                   $archivePath . $contents[CridonGedParser::INDEX_NOMFICHIER]);
                            // archivage source des metadonnees
                            rename($document, $archivePath . $fileInfo['basename']);

                            $logDocList[] = $contents[CridonGedParser::INDEX_NOMFICHIER];
                        } else { // invalide doc
                            // archivage source des metadonnees
                            rename($document, $archivePath . $fileInfo['basename']);

                            // log : envoie mail
                            $message = sprintf(CONST_IMPORT_GED_LOG_CORRUPTED_DOC_MSG, date('d/m/Y à H:i'), $fileInfo['basename']);
                            reportError($message, CONST_EMAIL_ERROR_SUBJECT);
                        }
                    } else { // doc sans question associee
                        // archivage source des metadonnees
                        rename($document, $archivePath . $fileInfo['basename']);

                        // log : envoie mail
                        $message = sprintf(CONST_IMPORT_GED_LOG_DOC_WITHOUT_QUESTION_MSG, date('d/m/Y à H:i'), $fileInfo['basename']);
                        reportError($message, CONST_EMAIL_ERROR_SUBJECT);
                    }
                }
            }

            // log : envoie par email list des documents importés
            if (count($logDocList) > 0) {
                $listDoc = implode(', ', $logDocList);
                $message = sprintf(CONST_IMPORT_GED_LOG_SUCCESS_MSG, date('d/m/Y à H:i'), $listDoc);
                reportError($message, CONST_EMAIL_ERROR_SUBJECT);
            }
        } else { // repertoire import vide
            // log : envoie mail
            $message = sprintf(CONST_IMPORT_GED_LOG_EMPTY_DIR_MSG, date('d/m/Y à H:i'));
            reportError($message, CONST_EMAIL_ERROR_SUBJECT);
        }
    }
}