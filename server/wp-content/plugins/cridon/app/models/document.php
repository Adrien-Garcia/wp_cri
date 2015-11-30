<?php

class Document extends MvcModel {

    var $display_field = 'name';
    var $table         = '{prefix}document';

    /**
     * @var mixed
     */
    protected $queryBuilder;

    /**
     * @var string
     */
    protected $uploadDir;

    public function create($data) {
        $data = $this->customProperties($data);
        return parent::create($data);
    }

    public function save($data) {
        $data = $this->customProperties($data);
        return parent::save($data);
    }

    protected function customProperties($data) {
        //Before insert OR update
        $date = new DateTime('now');
        $data[ 'Document' ][ 'date_modified' ] = $date->format('Y-m-d H:i:s');
        if ($data['Document']['type'] != 'question') {
            $path = $this->upload();
            if ($path) {
                if (empty($data['Document']['name'])) {
                    $name = explode(DIRECTORY_SEPARATOR, $path);
                    $name = array_pop($name);
                    $data['Document']['name'] = $name;
                }
                $data['Document']['file_path'] = $path;
            }
        }
        return $data;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        global $wpdb;
        $this->wpdb         = $wpdb;
        $this->queryBuilder = mvc_model('QueryBuilder');

        $this->setUploadDir();

        parent::__construct();
    }

    /**
     * Function to upload doc
     *
     * @param array $data Contains data of model
     * @return string|null
     */
    private function upload( $data = array() ){
        if( isset( $_FILES ) ){
            $upload_dir = wp_upload_dir();// the current upload directory
            $root = $upload_dir['basedir'];
            $date = new DateTime('now');
            $path = $root . '/documents/'.$date->format('Ym');//Upload directory
            $isDirectoryExist = true;// Directory is already exist.
            if( !file_exists( $path )){//not yet directory
                $isDirectoryExist = wp_mkdir_p($path);
            }
            $file = $_FILES['data']['name']['Document']['file_path'];
            if( $isDirectoryExist && file_exists( $path.DIRECTORY_SEPARATOR.$file ) ){//if file is already exist
                $file = mt_rand(1, 10).'_'.$_FILES['data']['name']['Document']['file_path'];
            }
            //moving file
            if( $isDirectoryExist && move_uploaded_file( $_FILES['data']['tmp_name']['Document']['file_path'], $path.DIRECTORY_SEPARATOR.$file ) ){
                if( !empty( $data ) ){
                    $obj = $this->find_by_id( $data['Document']['id'] );
                    $file_path = $obj->file_path;
                    if( $file_path ){
                        $tmp = explode( $path, $file_path );
                        if( ( count($tmp) > 1 ) && ( $tmp[1] !== $file ) ){//remove old doc
                            if( file_exists( $path.DIRECTORY_SEPARATOR.$tmp[1] ) ){
                                unlink( $path.DIRECTORY_SEPARATOR.$tmp[1] );//erase
                            }
                        }
                    }
                }
                //return relative path on server
                $relative = substr($path, strlen($root));
                return $relative.DIRECTORY_SEPARATOR.$file;
            }

        }
        return null;
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
                                    'name'          => $contents[CridonGedParser::INDEX_NOMFICHIER],
                                    'cab'           => $contents[CridonGedParser::INDEX_VALCAB]
                                )
                            );
                            //Document suite/complément
                            if( $typeAction == 2 ){
                                $docData['Document']['label'] = 'suite/complément';
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
                            $message = sprintf(CONST_IMPORT_GED_LOG_CORRUPTED_DOC_MSG, date('d/m/Y à H:i'), $fileInfo['basename']);
                            reportError($message, '');
                        }
                    } else { // doc sans question associee
                        // archivage source des metadonnees
                        rename($document, $archivePath . $fileInfo['basename']);

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
     * Insert new document
     *
     * @param array $docData
     * @return bool|int
     */
    public function insertDoc($docData)
    {
        $documentId = 0;

        // donnees document
        if (isset($docData['Document']['type']) && $docData['Document']['type']) {
            $docData['Document']['file_path'] = isset($docData['Document']['file_path'])?$docData['Document']['file_path']:'';
            $docData['Document']['download_url'] = isset($docData['Document']['download_url'])?$docData['Document']['download_url']:'';
            $docData['Document']['id_externe'] = isset($docData['Document']['id_externe'])?$docData['Document']['id_externe']:0;

            $files = pathinfo( $docData['Document']['file_path']);
            if (isset($files['filename'])) {
                $docData['Document']['name'] = $files['filename'];
            }

            // insertion
            $documentId = $this->create($docData);

            // maj download_url
            if (!$docData['Document']['download_url']) {
                $docData = array(
                    'Document' => array(
                        'id'           => $documentId,
                        'download_url' => '/documents/download/' . $documentId
                    )
                );
                $this->save($docData);
            }
        }

        return $documentId;
    }

    /**
     * @return string
     */
    public function getUploadDir()
    {
        return $this->uploadDir;
    }

    /**
     * @param null|string $uploadDir
     */
    public function setUploadDir($uploadDir = null)
    {
        $this->uploadDir = $uploadDir;
        if (!$this->uploadDir) {
            $upload_dir      = wp_upload_dir();// the current upload directory
            $root            = $upload_dir['basedir'];
            $date            = new DateTime('now');
            $path            = $root . '/documents/' . $date->format('Ym');//Upload directory
            $this->uploadDir = $path;
        }
    }
}