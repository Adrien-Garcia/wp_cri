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
            if ($user->data->ID) {
                $ret = mvc_public_url(array('controller' => 'notaires', 'action' => 'show', 'id' => $notaires->id));
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
        $notaires = $model->findByLoginAndEmail($_REQUEST['crpcen'], $_REQUEST['email']);

        // only an individual email is valid
        if (is_array($notaires) && count($notaires) == 1) {
            // message content
            $message =  sprintf(CONST_EMAIL_CONTENT, $notaires[0]->web_password);

            // send mdp by email
            if (wp_mail($notaires[0]->email_adress, CONST_EMAIL_SUBJECT, $message)) {
                $ret = 'success';
            }
        }

        echo json_encode($ret);

        die;
    }
}