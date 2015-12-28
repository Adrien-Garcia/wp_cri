<?php

class Document extends MvcModel {

    var $display_field = 'name';
    var $table         = '{prefix}document';
    // option list of document type    
    public $optionDocumentType = array(
        'question'      => 'Question',
        'veille'        => 'Veille',
        'formation'     => 'Formation',
        'cahiercridon'  => 'Cahier cridon',
        'viecridon'     => 'Vie cridon',
        'flash'         => 'Flash'
    );
    /**
     * @var mixed
     */
    public $queryBuilder;

    /**
     * @var string
     */
    protected $uploadDir;

    public function create($data) {
        $data = $this->customProperties($data);
        $id = parent::create($data);
        if( $id ){
            //Update download url
            $this->update($id, array('download_url' => '/documents/download/'.$id));//Using WP_MVC to update model
        }
        return $id;
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
     * Import documents
     */
    public function import()
    {
        // doc list pour log
        $logDocList = array();
        try{
            
            $Directory = new RecursiveDirectoryIterator(CONST_IMPORT_DOCUMENT_ORIGINAL_PATH);
            $Iterator = new RecursiveIteratorIterator($Directory);
            //remove the ending symbol $ as a txt file contains a '0' behind the extension...
            $documents = new RegexIterator($Iterator, '/^.+\.'.CONST_IMPORT_FILE_TYPE.'/i', RecursiveRegexIterator::GET_MATCH);
    
            // parse la liste des fichiers
            foreach ($documents as $document) {
                // info fichier
                $document = reset($document);
                $fileInfo = pathinfo($document);
    
                // recuperation contenu fichier s'il existe
                if (file_exists($document)) {
                    $content  = file_get_contents($document);
                    $contents = explode(CONST_IMPORT_GED_CONTENT_SEPARATOR, $content);
                    // repertoire archivage des documents
                    $date = date('Ym');
                    $archivePath = CONST_IMPORT_DOCUMENT_ORIGINAL_PATH . '..'.DIRECTORY_SEPARATOR.'archives/' . $date . '/';
                    if (!file_exists($archivePath)) { // repertoire manquant
                        // creation du nouveau repertoire
                        wp_mkdir_p($archivePath);
                    }
                    if( $this->importError($contents, $fileInfo) ){
                        // les différentes informations ne sont pas présentes dans le txt.
                        rename($document, $archivePath . $fileInfo['basename']);//archivage du txt
                        continue;
                    }
                    // recuperation id question par numero
                    $options               = array();
                    $options['attributes'] = array('id');
                    $options['model']      = 'question';
                    $options['conditions'] = ' srenum = ' . $contents[Config::$GEDtxtIndexes['INDEX_NUMQUESTION']];
    
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
                        if( !empty( $docs ) && !empty($contents[Config::$GEDtxtIndexes['INDEX_VALCAB']]) ){
                            //Si le CAB du csv existe parmi les CAB dans les docs du site
                            if( in_array( $contents[Config::$GEDtxtIndexes['INDEX_VALCAB']],$cabs ) ){
                                $typeAction = 3;//mise à jour
                            }else{
                                $typeAction = 2;//complément/suite ou question non importee auparavant
                            }
                        }
                        // copy document dans Site
                        $uploadDir = wp_upload_dir();
                        $path      = $uploadDir['basedir'] . '/questions/' . $date . '/';
                        if (!file_exists($path)) { // repertoire manquant
                            // creation du nouveau repertoire
                            wp_mkdir_p($path);
                        }

                        $docName = $contents[Config::$GEDtxtIndexes['INDEX_NOMFICHIER']];
                        // 1st check if the file exists as mentionned (case insensitive)
                        if (!fileExists($fileInfo['dirname'] . DIRECTORY_SEPARATOR . $docName) && (strpos($docName, '_') !== false)) {
                            //a prefix can be mentionned in the file. Don't use it if the file is not prefixed
                            $docName = substr($docName, strpos($docName, '_') + 1);
                        }
                        $filename = $this->getFileName($path, $docName);
                        $storedInfoFile = $fileInfo['dirname'] . DIRECTORY_SEPARATOR . $docName;
                        // Then, wether the file had a '_' or not, always find the right name for the file
                        // 'fileExists' will search for the file case insensitive and return it with the right name if found
                        $fileToImport = fileExists($storedInfoFile, false);
                        if (!empty($fileToImport) && copy($fileToImport,
                                 $path . $filename)) {
                            // donnees document
                            $docData = array(
                                'Document' => array(
                                    'file_path'     => '/questions/' . $date . '/' . $filename,
                                    'download_url'  => '/documents/download/' . $question->id,
                                    'date_modified' => date('Y-m-d H:i:s'),
                                    'type'          => 'question',
                                    'id_externe'    => $question->id,
                                    'name'          => $filename,
                                    'cab'           => $contents[Config::$GEDtxtIndexes['INDEX_VALCAB']],
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
                                $documentId = $this->create($docData);
                            }else{
                                //mise à jour
                                $documentId = array_search($contents[Config::$GEDtxtIndexes['INDEX_VALCAB']], $cabs);
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
                            rename($fileToImport,
                                   $archivePath . $contents[Config::$GEDtxtIndexes['INDEX_NOMFICHIER']]);
                            // archivage source des metadonnees
                            rename($document, $archivePath . $fileInfo['basename']);
                            // Mise de la date réelle de réponse de la question
                            $this->updateQuestion($question, $contents);
                            $logDocList[] = $contents[Config::$GEDtxtIndexes['INDEX_NOMFICHIER']];
                        } else { // invalide doc
                            // message par défaut
                            $message = sprintf(CONST_IMPORT_GED_LOG_CORRUPTED_CSV_MSG, date('d/m/Y à H:i'), $document);
                            // log : envoie mail
                            if( !$storedInfoFile ){
                                // PDF inexistant
                                $message = sprintf(CONST_IMPORT_GED_LOG_CORRUPTED_PDF_MSG, date('d/m/Y à H:i'), $docName);
                            }
                            reportError($message, '');
                            writeLog( $message,'majdocument.log' );
                        }
                    } else { // doc sans question associee
    
                        // log : envoie mail
                        $message = sprintf(CONST_IMPORT_GED_LOG_DOC_WITHOUT_QUESTION_MSG, date('d/m/Y à H:i'), $contents[Config::$GEDtxtIndexes['INDEX_NOMFICHIER']]);
                        reportError($message, '');
                        writeLog( $message,'majdocument.log' );
                    }
                }
            }
    
            // log : envoie par email list des documents importés
            if (count($logDocList) > 0) {
                $listDoc = implode(', ', $logDocList);
                $message = sprintf(CONST_IMPORT_GED_LOG_SUCCESS_MSG, date('d/m/Y à H:i'), $listDoc);
                writeLog($message, 'importdocs.log');
            } else { // repertoire import vide
                // log : envoie mail
                $message = sprintf(CONST_IMPORT_GED_LOG_EMPTY_DIR_MSG, date('d/m/Y à H:i'));
                reportError($message, '');
                writeLog($message, 'importdocs.log');
            }
        } catch ( \Exception $e ){
            writeLog( $e,'majdocument.log' );
            return CONST_STATUS_CODE_GONE;
        }
        return CONST_STATUS_CODE_OK;
    }    
    
    /**
     * Get File name
     * @param string $path
     * @param string $original
     * @return string
     */
    public function getFileName( $path,$original ){
        $output = $original;
        if (file_exists($path.$original)) {
            $output = mt_rand(1, 10) . '_' . $original;
        }
        return $output;
    }

    /**
     * Increment file name
     *
     * @param string $file_path
     * @param string $filename
     * @return string
     */
    public function incrementFileName($file_path,$filename){
        $array = explode(".", $filename);
        $file_ext = end($array);
        $root_name = str_replace(('.'.$file_ext),"",$filename);
        $file = $filename;
        $i = 1;
        while(file_exists($file_path.$file)){
            $file = $root_name.'_'.$i.'.'.$file_ext;
            $i++;
        }
        return $file;
    }
    
    /**
     * Verification of the information in the import file
     * 
     * @param array $contents
     * @param array $fileInfo
     * @return boolean
     */
    protected function importError( $contents,$fileInfo ){
        if( ( count($contents) !== Config::$GEDtxtIndexes['NB_COLONNES'] ) || empty( $contents[Config::$GEDtxtIndexes['INDEX_NUMQUESTION']] ) || empty( $contents[Config::$GEDtxtIndexes['INDEX_NOMFICHIER']] ) ){
            $message = sprintf(CONST_IMPORT_GED_LOG_CORRUPTED_CSV_MSG, date('d/m/Y à H:i'), $fileInfo['basename']);
            if( !empty( $contents[Config::$GEDtxtIndexes['INDEX_NOMFICHIER']] ) ){
                $message = sprintf(CONST_IMPORT_GED_LOG_CORRUPTED_DOC_MSG, date('d/m/Y à H:i'), $contents[Config::$GEDtxtIndexes['INDEX_NOMFICHIER']]);
            }
            reportError($message, '');
            return true;
        }
        return false;
    }
    
    /**
     * Update question
     * 
     * @param object $question
     * @param array $contents
     */
    public function updateQuestion( $question, $contents ){
        if( !empty($contents[Config::$GEDtxtIndexes['INDEX_DATEREPONSE']]) ){
            if( preg_match("/([0-9]{4}-[0-9]{2}-[0-9]{2})T/", $contents[Config::$GEDtxtIndexes['INDEX_DATEREPONSE']], $matches) ){
                $d = DateTime::createFromFormat('Y-m-d', $matches[1]);
                //Vérifier la validité de la date fournie
                if( $d && $d->format('Y-m-d') == $matches[1] ){
                    $questData = array(
                        'Question' => array(
                            'id'        => $question->id,
                            'real_date' => $matches[1]
                        )
                    );
                    // mise de la date réelle de réponse
                    mvc_model('Question')->save($questData);                    
                }
            }
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
    //Encryption
    /**
     * Encrypt value
     * 
     * @param string $val
     * @return string
     */
    public function encryptVal( $val ) {
	$salt = wp_salt( 'secure_auth' );        
	$td = mcrypt_module_open (MCRYPT_RIJNDAEL_256, '', MCRYPT_MODE_ECB, '');
        $ks = mcrypt_enc_get_key_size($td);//key size
	$key = substr(md5($salt),0,$ks);//create key
	$iv = mcrypt_create_iv (mcrypt_enc_get_iv_size ($td), MCRYPT_RAND);
	mcrypt_generic_init ($td, $key, $iv);
	$encrypted_data = mcrypt_generic ($td, $val);
	mcrypt_generic_deinit ($td);
	mcrypt_module_close ($td);
	return trim($this->urlBase64Encode($encrypted_data));
    }
    
    /**
     * Decrypt value
     * 
     * @param string $val
     * @return string
     */
    public function decryptVal( $val ) {
        $salt = wp_salt( 'secure_auth' );
        //Decode base64
        $input = trim($this->urlBase64Decode($val));
        
        /**
         * @see http://php.net/manual/fr/function.mcrypt-module-open.php
         */
        
        $td = mcrypt_module_open (MCRYPT_RIJNDAEL_256, '', MCRYPT_MODE_ECB, '');
        $ks = mcrypt_enc_get_key_size($td);//key size
        $key = substr(md5($salt),0,$ks);//create key
        $iv = mcrypt_create_iv (mcrypt_enc_get_iv_size ($td), MCRYPT_RAND);
        mcrypt_generic_init ($td, $key, $iv);
        $decrypted_data = mdecrypt_generic ($td, $input);
        mcrypt_generic_deinit ($td);
        //Close algoritm module
        mcrypt_module_close ($td);
        return trim($decrypted_data);
    }
    
    /**
     * Encode in Base64
     * 
     * @param string $str
     * @return string
     */
    public function urlBase64Encode($str){
	return strtr(base64_encode($str),
            array(
		'/' => '~'
            )
	);
    }
    
    /**
     * Decode Base64
     * 
     * @param string $str
     * @return string
     */
    public function urlBase64Decode($str){
        return base64_decode(strtr($str,
            array(
                '~' => '/'
                )
            )
        );
    }    

    public function generatePublicUrl( $id ){
        $url = Config::$confPublicDownloadURL['url'].$id;
        return '/telechargement/'.$this->encryptVal($url);
    }
//End Encryption
}