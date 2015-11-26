<?php
/**
 * Route config for cridon
 * @package wp_cridon
 * @subpackage routes
 * @author Etech
 */

// rest
MvcRouter::public_connect('rest/login', array( 'controller' =>'logins','action' => 'login'));
MvcRouter::public_connect('rest/{:controller}', array('action' => 'index_json', 'layouts' => 'json'));
MvcRouter::public_connect('rest/{:controller}/{:id:[\d]+}', array('action' => 'show_json', 'layout' => 'json'));
MvcRouter::public_connect('rest/{:controller}/{:action:[^\d]+}', array('layout' => 'json'));

// import (import notaire action)
MvcRouter::public_connect('import/notaires', array('controller' => 'notaires', 'action' => 'import'));

// import solde
MvcRouter::public_connect('import/soldes', array('controller' => 'notaires', 'action' => 'importsolde'));

// import initial questions
MvcRouter::public_connect('questions/importinitial', array('controller' => 'questions', 'action' => 'importinitial'));

// import initial document
MvcRouter::public_connect('documents/importinitial', array('controller' => 'documents', 'action' => 'importinitial'));

// mes questions
MvcRouter::public_connect('notaires/{:id:[\d]+}/questions', array('controller' => 'notaires', 'action' => 'questions'));
// mon profil
MvcRouter::public_connect('notaires/{:id:[\d]+}/profil', array('controller' => 'notaires', 'action' => 'profil'));
// regles de facturation
MvcRouter::public_connect('notaires/{:id:[\d]+}/facturation', array('controller' => 'notaires', 'action' => 'facturation'));

// default
MvcRouter::public_connect('{:controller}', array('action' => 'index'));
MvcRouter::public_connect('{:controller}/{:id:[\d]+}', array('action' => 'show'));
MvcRouter::public_connect('{:controller}/{:action}/{:id:[\d]+}');


//Ajax admin
MvcRouter::admin_ajax_connect(array('controller' => 'admin_documents', 'action' => 'search'));
