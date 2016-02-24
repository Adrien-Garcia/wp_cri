<?php
/**
 * Created by PhpStorm.
 * User: valbert
 * Date: 19/11/2015
 * Time: 20:03
 */
//interface
require_once 'cridon.dbconnect.lib.php';

//ODBC
require_once 'cridon.odbcadapter.lib.php';

//OCI
require_once 'cridon.oci.lib.php';

// autoloader for namespace
require_once __DIR__ . '/../override/Autoloader.php';
\App\Override\Autoloader::register();
