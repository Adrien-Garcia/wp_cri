<?php
header('Content-Type', 'text/html; charset=UTF-8');
require_once 'wp-load.php';
var_dump('TEST UTF-8 : é@à');
//CONNECT
$conf = "
(
  DESCRIPTION = (
    ADDRESS = (
      PROTOCOL = TCP
    )
    (HOST =".CONST_DB_HOST.")
    (PORT = ".CONST_DB_PORT.")
  )
  (CONNECT_DATA =
    (SERVER = DEDICATED)
    (SERVICE_NAME = ".CONST_DB_DATABASE.")
    (INSTANCE_NAME = ".CONST_DB_DATABASE.")
  )
)";
$conn = oci_connect(CONST_DB_USER, CONST_DB_PASSWORD, $conf, 'AL32UTF8');

if (!$conn || !is_resource($conn)) {
    $error = oci_error();
    $error = empty($error) ? CONST_CONNECTION_FAILED : $error;
    writeLog($error, 'connexion.log');
    reportError(CONST_EMAIL_ERROR_CATCH_EXCEPTION, $error['message']);
    throw new Exception($error['message'], $error['code']);
}


//ADD SQL HERE
$sql = 'SELECT * FROM CLCRIDON.ZQUESTV';
$sql .= ' WHERE SRENUM_0 = \'534602\'';


//EXECUTE
//remove potential ending semi-colon.
if (substr($sql, -1) == ';') {
    $sql = substr($sql, -1);
}
//parse and prepare query
$statementId = oci_parse($conn, $sql);
$isExec = oci_execute($statementId);
if (!$isExec) {
    $error = oci_error();
    $error = empty($error) ? CONST_CONNECTION_FAILED : $error['message'];
    writeLog($error, 'execute.log');
    reportError(CONST_EMAIL_ERROR_CATCH_EXCEPTION, $error);
    var_dump('ERROR');
    var_dump($error);
}

//FETCH
$data = oci_fetch_array($statementId);
var_dump($data);

//END
// Free Result
oci_free_statement($statementId);

// Close Connection
oci_close($conn);

