<?php

/**
 *
 * This file is part of project 
 *
 * File name : class_loader.php
 * Project   : wp_cridon
 *
 * @author Etech
 * @contributor Fabrice MILA
 *
 */

require_once 'http/CridonRequest.php';
require_once 'http/CridonServer.php';
require_once 'http/CridonResponse.php';
require_once 'CridonUser.php';
require_once 'CridonSecurity.php';
require_once 'RestServer.php';

$cri_container->set('security', function() use ($cri_container){
        return new CridonSecurity( $cri_container->get('request') );
    }
);

$cri_container->set('server', function(){
        return new CridonServer();
    }
);

$cri_container->set('response', function(){
        return new CridonResponse();
    }
);

$cri_container->set('request', function() use ($cri_container){
        return new CridonRequest( $cri_container->get('server'),$cri_container->get('response') );
    }
);
