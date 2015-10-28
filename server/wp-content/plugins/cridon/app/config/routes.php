<?php
/**
 * Route config for cridon
 * @package wp_cridon
 * @subpackage routes
 * @author Etech
 */

// rest
MvcRouter::public_connect('rest/{:controller}', array('action' => 'index_json', 'layouts' => 'json'));
MvcRouter::public_connect('rest/{:controller}/{:id:[\d]+}', array('action' => 'show_json', 'layout' => 'json'));
MvcRouter::public_connect('rest/{:controller}/{:action:[^\d]+}', array('layout' => 'json'));

// download file
MvcRouter::public_connect('download/question/{:id:[\d]+}',array('controller' => 'downloads','action' => 'downloadQuestion'));
MvcRouter::public_connect('download/reponse/{:id:[\d]+}',array('controller' => 'downloads','action' => 'downloadAnswer'));

// import (import notaire into wp_users)
MvcRouter::public_connect('import/notaires', array('controller' => 'notaires', 'action' => 'import'));

// default
MvcRouter::public_connect('{:controller}', array('action' => 'index'));
MvcRouter::public_connect('{:controller}/{:id:[\d]+}', array('action' => 'show'));
MvcRouter::public_connect('{:controller}/{:action}/{:id:[\d]+}');