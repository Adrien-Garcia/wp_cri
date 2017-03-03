<?php

/**
 * Class LoginsController
 * @author Etech
 * @contributor Joelio
 */
class LoginsController extends MvcPublicController
{

    /**
     * Connection action
     */
    public function connect()
    {
        global $current_user;
        // Check if our nonce is set.
        if (!isset($_REQUEST['token'])) {
            return;
        }

        $nonce = $_REQUEST['token'];

        // Verify that the nonce is valid.
        if (!wp_verify_nonce($nonce, 'process_login_nonce')) {
            return;
        }

        $ret = 'invalidlogin';

        // find the notaire by login and password (similarly action on ERP)
        $model = mvc_model('notaire');
        $notaires = $model->findByLoginAndPassword($_REQUEST['login'], $_REQUEST['password']);
        if ($notaires) {
            $user_login = $_REQUEST['login'] . CONST_LOGIN_SEPARATOR . $notaires->id;

            // wp credential params
            $creds                  = array();
            $creds['user_login']    = $user_login;
            $creds['user_password'] = $_REQUEST['password'];

            if (isset($_REQUEST['keep'])) {
                $creds['remember'] = true;
            }

            // user signon action
            $current_user = wp_signon($creds, false);

            // user data exist
            if ($current_user->data->ID) {
                $dashboardAccess = $model->userCanAccessSensitiveInfo(CONST_DASHBOARD_ROLE);
                $ret = mvc_public_url(array('controller' => 'notaires', 'action' => $dashboardAccess ? 'show' : 'profil', 'id' => $notaires->id));
            }
        }

        echo json_encode($ret);

        die;
    }

    /**
     * Lost password action
     */
    public function lostPassword()
    {
        // Check if our nonce is set.
        if (!isset($_REQUEST['token'])) {
            return;
        }

        $nonce = $_REQUEST['token'];

        // Verify that the nonce is valid.
        if (!wp_verify_nonce($nonce, 'process_lostpwd_nonce')) {
            return;
        }

        $ret = 'invalidlogin';

        // find the notaire by CRPCEN and email
        $model    = mvc_model('notaire');
        $notary   = $model->findByLoginAndEmail($_REQUEST['crpcen'], $_REQUEST['email']);

        // only an individual email is valid
        if (is_object($notary)
            && property_exists($notary, 'email_adress')
            && $notary->email_adress
        ) {
            // message content
            $message =  sprintf(CONST_EMAIL_CONTENT, $notary->web_password);

            // send mdp by email
            if (wp_mail($notary->email_adress, CONST_EMAIL_SUBJECT, $message)) {
                $ret = 'success';
            }
        }

        echo json_encode($ret);

        die;
    }
    
    //Webservice
    
    /**
     * Action pour la connexion mobile
     * 
     * @global CridonContainer $cri_container
     */
    public function login()
    {
        global $cri_container;
        $request = $cri_container->get('request');
        $success = false;
        $message = CONST_LOGIN_ERROR_MSG;
        $token   = false;
        $notaire = new stdClass();

        //N'accepter que les requêtes POST
        if (!$request->isMethod('POST')) {
            $message = CONST_WS_MSG_ERROR_METHOD;
        } else {
            $model = mvc_model('notaire');//load model notaire
            // Check if Notaire exist with this login and password
            $method = $request->getMethod();
            $notaire = $model->findByLoginAndPassword($request->get( $method, 'login' ), $request->get( $method, 'password' ) );

            if (!empty($notaire)) {
                //Validate role
                if (!in_array(CONST_QUESTIONECRITES_ROLE,RoleManager::getUserRoles($notaire))){
                    $message = CONST_WS_LOGIN_ROLE_ERROR_MSG;
                } else {
                    $token = $this->generateToken($model, $notaire);
                    //No token generated
                    if ($token) {
                        $success = true;
                        $message = CONST_WS_MSG_SUCCESS;
                    }
                }
            }
        }
        //output token
        $encoded = $request->response->getResponse(array(
                'success'            => $success,
                'message'            => $message,
                CONST_TOKEN_NAME_VAR => $token,
                'civilite'           => property_exists($notaire, 'civilite') ? $notaire->civilite : '',
                'nom'                => property_exists($notaire, 'nom') ? $notaire->nom : '',
                'prenom'             => property_exists($notaire, 'prenom') ? $notaire->prenom : ''
            )
        );
        $this->set('encoded', $encoded);
        $this->render_view(
            'login',
            array(
                'layout' => 'response_json'
            )
        );
    }
    
    /**
     * Attempt to authenticate
     * 
     * @return boolean|object
     */
    protected function generateToken( $model, $notaire ){
        if( empty( $notaire ) ){ 
            return false;
        }
        return $model->generateToken( $notaire->id, $notaire->crpcen, $notaire->web_password );
    }
        
    //End webservice
}
