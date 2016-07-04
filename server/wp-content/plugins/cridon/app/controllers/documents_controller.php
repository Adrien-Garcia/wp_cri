<?php

/**
 * Documents Controller
 */
class DocumentsController extends BasePublicController
{

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

        // filter type of document for model associations
        if (!in_array($document->type, Config::$exceptedDocTypeForModel)) {
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
    
    private function checkAccess( $object,$notaire,$document )
    {
        //If we are in BO, logged and not a Notaire
        if (is_user_logged_in() && empty($notaire)) {
            //If user cridon, they can download with no restriction
            return true;
        }
        //Access download document of news
        if (in_array($document->type, Config::$restrictedDownloadByTypeLevel)
            && !empty($notaire)
            && !mvc_model('Veille')->userCanAccessSingle($object,$notaire)) {
            // user connected && document allowed for download but document was restricted for specific level
            $this->redirect(mvc_public_url(
                    array(
                        'controller' => 'notaires',
                        'action'     => 'cridonline'
                    )
                ).'?error=NIVEAU_VEILLE_INSUFFISANT'
            );
        } elseif (empty($notaire)) { // force user to login before downloading
            $config = assocToKeyVal(Config::$data, 'model', 'controller');//get config
            $url = mvc_public_url(
                array(
                    'controller' => $config[$object->__model_name],
                    'action'     => 'show',
                    'id'         => $object->post->post_name,
                )
            );
            if (in_array($object->__model_name, Config::$modelWithIdDocImplemented)){
                $url.= '?id_doc='.$document->id;
            }
            CriRefuseAccess('PROTECTED_CONTENT',$url);
        } elseif (empty($object) || empty($document->file_path)) { // Check if question exist, document file path is valid
            redirectTo404();
        }
        //Check if question is created by current user
        //$objet = Question MvcModelObject
        if (isset($object->client_number) && $object->client_number != $notaire->client_number) {
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
            //Test if it's a protected document
            if (in_array($document->type,Config::$restrictedDownloadByTypeLevel)){
                $this->params['id'] = $document->id;
                $this->download();
            } else {
                //Let's begin download
                $uploadDir = wp_upload_dir();
                $file = $uploadDir['basedir'] . $document->file_path;
                $pathinfo = pathinfo($document->file_path);
                //Get file name
                $filename = $pathinfo['basename'];
                //download
                $this->tools->forceDownload($file, $filename);
            }
        }else{
            redirectTo404();
        }
    }
}
