<?php

class LoginsController extends MvcPublicController
{

    public function connect()
    {
        // Check if our nonce is set.
        if ( ! isset( $_REQUEST['token'] ) )
            return ;

        $nonce = $_REQUEST['token'];

        // Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $nonce, 'process_login_nonce' ) )
            return ;

        global $wpdb;

        $ret = 'true' ;

        $creds = array();
        $creds['user_login'] = $_REQUEST['login'];
        $creds['user_password'] = $_REQUEST['password'];

        if(isset($_REQUEST['keep']))
            $creds['remember'] = true;

        $user = wp_signon( $creds, false );
        if(!property_exists($user, 'data'))
        {
            $ret = 'invalidlogin';
        }
        elseif(isset($user->roles[0]) && $user->roles[0] == 'administrator')
        {
//            $ret = 'admin';
        }
        else
        {
            $ret = mvc_public_url(array('controller' => 'notaires', 'action' => 'espace-notaire'));
        }

        echo json_encode($ret);

        die;
    }
}