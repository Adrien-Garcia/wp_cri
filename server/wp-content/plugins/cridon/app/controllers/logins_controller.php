<?php

class LoginsController extends MvcPublicController
{

    public function connect()
    {
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
            $user = wp_signon($creds, false);

            // user data exist
            if (isset($user->roles[0])) {
                if ($user->roles[0] == CONST_ADMIN_ROLE) {
                    // @TODO action specific for administrator user

                } elseif($user->roles[0] == CONST_NOTAIRE_ROLE) {
                    // redirection url for user logged in
                    $ret = mvc_public_url(array('controller' => 'notaires', 'action' => 'espace-notaire'));
                }
            }
        }

        echo json_encode($ret);

        die;
    }
}