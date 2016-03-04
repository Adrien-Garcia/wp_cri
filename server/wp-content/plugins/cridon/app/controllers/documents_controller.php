<?php

/**
 * Documents Controller
 */
class DocumentsController extends MvcPublicController
{

    /**
     * @var mixed tools actions container
     */
    protected $tools;

    /**
     * DocumentsController constructor.
     */
    public function __construct()
    {
        global $cri_container;
        $this->tools = $cri_container->get('tools');

        parent::__construct();
    }

    /**
     * Action download for user connected
     *
     * @throws Exception
     */
    public function download()
    {
        $document = $this->model->find_one_by_id($this->params['id']);
        if (empty($document)) {
            redirectTo404();
        }
        //Check if it's a Notaire and connected
        if (is_user_logged_in() && CriIsNotaire()) {
            $notaire = CriNotaireData();
        } else {
            $notaire = null;//for User Cridon BO and a user not connected
        }
        $model = mvc_model($document->type);
        //No model check
        if (empty($model)) {
            redirectTo404();
        }
        if (($model->name == 'Question') || in_array($document->type, Config::$accessDowloadDocument)) {
            $object = $model->find_one_by_id($document->id_externe);
            //Check user access
            $this->checkAccess($object, $notaire, $document);
        }
        //Let's begin download
        $uploadDir = wp_upload_dir();
        $file      = $uploadDir['basedir'] . $document->file_path;
        $tmp       = explode('/', $document->file_path);
        //Get file name
        $filename = $tmp[count($tmp) - 1];
        // force download
        $this->tools->forceDownload($file, $filename);
    }
    
    private function checkAccess( $object,$notaire,$document ){
        //If we are in BO, logged and not a Notaire
        if ( is_user_logged_in() && empty( $notaire ) ) {
            //If user cridon, they can download with no restriction
            return true;
        }
        //Access download document of news
        if( in_array($document->type,Config::$accessDowloadDocument) && !empty( $notaire ) ){
            return true;
        }elseif(in_array($document->type,Config::$accessDowloadDocument)){
            redirectTo404();
        //Check if question exist, document file path is valid
        }elseif( empty( $notaire ) || empty( $object ) || empty( $document->file_path ) ){
            redirectTo404();
        }        
        //Check if question is created by current user
        //$objet = Question MvcModelObject
        if( $object->client_number != $notaire->client_number ){
            redirectTo404();
        }
        return true;
    }

    /**
     * Public action download using encrypted url
     */
    public function publicDownload()
    {
        $crypted   = $this->params['id'];//encrypted value
        $decrypted = $this->model->decryptVal($crypted);//decrypt value
        if (preg_match(Config::$confPublicDownloadURL['pattern'], $decrypted, $matches)) {
            $document = $this->model->find_one_by_id($matches[1]);
            if (!$document && !empty($document->file_path)) {
                redirectTo404();
            }
            //Let's begin download
            $uploadDir = wp_upload_dir();
            $file      = $uploadDir['basedir'] . $document->file_path;
            $pathinfo  = pathinfo($document->file_path);
            //Get file name
            $filename = $pathinfo['basename'];
            // force download
            $this->tools->forceDownload($file, $filename);
        } else {
            redirectTo404();
        }
    }
}