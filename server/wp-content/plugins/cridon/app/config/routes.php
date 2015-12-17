<?php
/**
 * Route config for cridon
 * @package wp_cridon
 * @subpackage routes
 * @author Etech
 */

// Téléchargement des documents publics, toujours en dernier dans les routes pour éviter les problèmes des "/" dans les données cryptées
MvcRouter::public_connect('telechargement/{:id:[a-zA-Z0-9=+\/_-]+}', array('controller' => 'documents', 'action' => 'publicDownload'));

// rest
MvcRouter::public_connect('rest/login', array( 'controller' =>'logins','action' => 'login'));
MvcRouter::public_connect('rest/{:controller}', array('action' => 'index_json', 'layouts' => 'json'));
MvcRouter::public_connect('rest/{:controller}/{:id:[\d]+}', array('action' => 'show_json', 'layout' => 'json'));
MvcRouter::public_connect('rest/{:controller}/{:action:[^\d]+}', array('layout' => 'json'));

// mes questions
MvcRouter::public_connect('notaires/{:id:[\d]+}/questions', array('controller' => 'notaires', 'action' => 'questions'));
// mon profil
MvcRouter::public_connect('notaires/{:id:[\d]+}/profil', array('controller' => 'notaires', 'action' => 'profil'));
// regles de facturation
MvcRouter::public_connect('notaires/{:id:[\d]+}/facturation', array('controller' => 'notaires', 'action' => 'facturation'));

// mon dashboard
MvcRouter::public_connect('notaires/{:id:[\d]+}/contentdashboard', array('controller' => 'notaires', 'action' => 'contentdashboard'));
// mes questions
MvcRouter::public_connect('notaires/{:id:[\d]+}/contentquestions', array('controller' => 'notaires', 'action' => 'contentquestions'));
// mon profil
MvcRouter::public_connect('notaires/{:id:[\d]+}/contentprofil', array('controller' => 'notaires', 'action' => 'contentprofil'));
// regles de facturation
MvcRouter::public_connect('notaires/{:id:[\d]+}/contentfacturation', array('controller' => 'notaires', 'action' => 'contentfacturation'));


// default
MvcRouter::public_connect('{:controller}', array('action' => 'index'));
MvcRouter::public_connect('{:controller}/{:id:[\d]+}', array('action' => 'show'));
MvcRouter::public_connect('{:controller}/{:action}/{:id:[\d]+}');


//Ajax admin
MvcRouter::admin_ajax_connect(array('controller' => 'admin_documents', 'action' => 'search'));

