<?php

class QuestionsController extends BasePublicController
{
    /**
     * Add question action for Web version
     */
    public function add()
    {
        $ret = CONST_QUESTION_ACTION_SUCCESSFUL;
        $resp = CriPostQuestion();
        if (is_array($resp)) {
            if (is_array($resp['error']) && count($resp['error']) > 0) {
                $ret = $resp;
            } elseif(isset($resp['resume'])) {
                CriSendPostQuestConfirmation($resp);
            }
           // $ret
        } elseif(!$resp) {
            $ret = array(
                'error' => array( CONST_QUESTION_ACTION_ERROR )
            );
        }

        echo json_encode($ret);

        die;
    }

    /**
     * Add question action for Mobile version
     */
    public function add_json()
    {
        $notary  = $this->checkToken();
        $request = $this->getRequest();
        $success = false;
        $message = CONST_LOGIN_ERROR_MSG;
        $url     = '';
        // N'accepter que les requêtes POST
        if (!$request->isMethod('POST')) {
            $message = CONST_WS_MSG_ERROR_METHOD;
        } elseif (is_object($notary)
                  && property_exists($notary, 'id')
                  && $notary->id
        ) { // verification notaire
            $datas    = array(
                'notary' => $notary,
                'post'   => $_POST,
            );
            $response = $this->model->createFromMobile($datas);

            // recuperation erreur
            if (count($response['error']) > 0) {
                $message = $response['error'];
            } else { // aucune erreur capturée
                $success = true;
                $message = CONST_QUESTION_ACTION_SUCCESSFUL;
                $url     = mvc_public_url(array('controller' => 'notaires', 'id' => $notary->id));
            }
        }

        // output
        $encoded = $this->getRequest()->response->getResponse(array(
                'success'    => $success,
                'message'    => $message,
                'urlnotaire' => $url
            )
        );
        $this->set('encoded', $encoded);
        $this->render_view(
            'add_json',
            array(
                'layout' => 'response_json'
            )
        );
    }
}