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
                    // repertoire archivage des documents
                    $archivePath = CONST_IMPORT_DOCUMENT_ORIGINAL_PATH . '/archives/' . date('YmdHi') . '/';
                    if (!file_exists($archivePath)) { // repertoire manquant
                        // creation du nouveau repertoire
                        wp_mkdir_p($archivePath);
                    }
                    if( $this->importError($contents, $fileInfo) ){
                        // les différentes informations ne sont pas présentes dans le csv.
                        rename($document, $archivePath . $fileInfo['basename']);//archivage du csv
                        continue;
                    }
                    // recuperation id question par numero
                    $options               = array();
                    $options['attributes'] = array('id');
                    $options['model']      = 'question';
                    $options['conditions'] = ' srenum = ' . $contents[CridonGedParser::INDEX_NUMQUESTION];

                    // question associée
                    $question = $this->queryBuilder->findOneByOptions($options);

                    // question exist
                    if ($question->id) {
                        //Recherche de l'existance d'un document lié à la question
                        // recuperation id question par numero
                        $options               = array();
                        $options['attributes'] = array('id,cab','file_path');
                        $options['model']      = 'document';
                        $options['conditions'] = ' id_externe = ' . $question->id . ' AND type="question"';

                        // document associé
                        $docs = $this->queryBuilder->find($options);
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
                        if( !empty( $docs ) && !empty($contents[CridonGedParser::INDEX_VALCAB]) ){
                            //Si le CAB du csv existe parmi les CAB dans les docs du site
                            if( in_array( $contents[CridonGedParser::INDEX_VALCAB],$cabs ) ){
                                $typeAction = 3;//mise à jour
                            }else{
                                $typeAction = 2;//complément/suite
                            }
                        }
                        // copy document dans Site
                        $uploadDir = wp_upload_dir();
                        $path      = $uploadDir['basedir'] . '/questions/' . date('Ym') . '/';
                        if (!file_exists($path)) { // repertoire manquant
                            // creation du nouveau repertoire
                            wp_mkdir_p($path);
                        }
                        //Si le fichier existe dèja alors ajouter un suffixe sur le nom
                        $filename = $this->getFileName($path, $contents[CridonGedParser::INDEX_NOMFICHIER]);
                        if ( @copy(CONST_IMPORT_DOCUMENT_ORIGINAL_PATH . '/' . $contents[CridonGedParser::INDEX_NOMFICHIER],
                                 $path . $filename)) {
                            // donnees document
                            $docData = array(
                                'Document' => array(
                                    'file_path'     => '/questions/' . date('Ym') . '/' . $filename,
                                    'download_url'  => '/documents/download/' . $question->id,
                                    'date_modified' => date('Y-m-d H:i:s'),
                                    'type'          => 'question',
                                    'id_externe'    => $question->id,
                                    'name'          => $filename,
                                    'cab'           => $contents[CridonGedParser::INDEX_VALCAB],
                                    'label'         => 'question/reponse'
                                )
                            );
                            //Document suite/complément
                            if( $typeAction == 2 ){
                                $label = 'Suite';
                                //Si le nom de fichier commence par 'C'
                                if(strtoupper(substr($contents[CridonGedParser::INDEX_NOMFICHIER], 0, 1)) == 'C' ){
                                    $label = 'Complément';
                                }
                                $docData['Document']['label'] = $label;
                            }
                            if( $typeAction != 3 ){
                                // insertion
                                $documentId = $this->create($docData);                                
                            }else{
                                //mise à jour
                                $documentId = array_search($contents[CridonGedParser::INDEX_VALCAB], $cabs);
                                $docData['Document']['id'] = $documentId;
                                if( $this->save($docData) ){
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
                            $this->save($docData);

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
                            if( !file_exists(CONST_IMPORT_DOCUMENT_ORIGINAL_PATH . '/' . $contents[CridonGedParser::INDEX_NOMFICHIER]) ){
                                // PDF inexistant
                                $message = sprintf(CONST_IMPORT_GED_LOG_CORRUPTED_PDF_MSG, date('d/m/Y à H:i'), $contents[CridonGedParser::INDEX_NUMQUESTION]);                                
                            }
                            reportError($message, '');
                        }
                    } else { // doc sans question associee

                        // log : envoie mail
                        $message = sprintf(CONST_IMPORT_GED_LOG_DOC_WITHOUT_QUESTION_MSG, date('d/m/Y à H:i'), $fileInfo['basename']);
                        reportError($message, '');
                    }
                }
            }

            // log : envoie par email list des documents importés
            if (count($logDocList) > 0) {
                $listDoc = implode(', ', $logDocList);
                $message = sprintf(CONST_IMPORT_GED_LOG_SUCCESS_MSG, date('d/m/Y à H:i'), $listDoc);
                reportError($message, '');
            }
        } else { // repertoire import vide
            // log : envoie mail
            $message = sprintf(CONST_IMPORT_GED_LOG_EMPTY_DIR_MSG, date('d/m/Y à H:i'));
            reportError($message, '');
        }
    }    
    
    /**
     * Get File name
     * @param string $path
     * @param string $original
     * @return string
     */
    protected function getFileName( $path,$original ){
        $output = $original;
        if (file_exists($path.$original)) {
            $output = mt_rand(1, 10) . '_' . $original;
        }
        return $output;
    }
    
    /**
     * Verification of the information in the CSV file
     * 
     * @param array $contents
     * @param array $fileInfo
     * @return boolean
     */
    protected function importError( $contents,$fileInfo ){
        if( ( count($contents) !== CridonGedParser::NB_COLONNE_CSV ) || empty( $contents[CridonGedParser::INDEX_NUMQUESTION] ) || empty( $contents[CridonGedParser::INDEX_NOMFICHIER] ) ){
            $message = sprintf(CONST_IMPORT_GED_LOG_CORRUPTED_CSV_MSG, date('d/m/Y à H:i'), $fileInfo['basename']);
            if( !empty( $contents[CridonGedParser::INDEX_NOMFICHIER] ) ){
                $message = sprintf(CONST_IMPORT_GED_LOG_CORRUPTED_DOC_MSG, date('d/m/Y à H:i'), $contents[CridonGedParser::INDEX_NOMFICHIER]);                                          
            }
            reportError($message, '');
            return true;
        }
        return false;
    }
}