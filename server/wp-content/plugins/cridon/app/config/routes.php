<?php
/**
 * Route config for cridon
 * @package wp_cridon
 * @subpackage routes
 * @author Etech
 */

// Téléchargement des documents publics, toujours en dernier dans les routes pour éviter les problèmes des "/" dans les données cryptées
MvcRouter::public_connect('telechargement/{:id:[a-zA-Z0-9=+~_-]+}', array('controller' => 'documents', 'action' => 'publicDownload'));

// rest
MvcRouter::public_connect('rest/login', array( 'controller' =>'logins','action' => 'login'));
MvcRouter::public_connect('rest/askquestion', array( 'controller' =>'questions','action' => 'add_json'));

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

// archives routes
MvcRouter::public_connect('flashes', array('controller' => 'flashes', 'action' => 'index'));
MvcRouter::public_connect('formations', array('controller' => 'formations', 'action' => 'index'));
MvcRouter::public_connect('veilles', array('controller' => 'veilles', 'action' => 'index'));
MvcRouter::public_connect('cahier_cridons', array('controller' => 'cahier_cridons', 'action' => 'index'));
MvcRouter::public_connect('vie_cridons', array('controller' => 'vie_cridons', 'action' => 'index'));

// default
MvcRouter::public_connect('{:controller}/{:id:[\d]+}', array('action' => 'show'));
MvcRouter::public_connect('{:controller}/{:action}/{:id:[\d]+}');


//Ajax admin
MvcRouter::admin_ajax_connect(array('controller' => 'admin_documents', 'action' => 'search'));

