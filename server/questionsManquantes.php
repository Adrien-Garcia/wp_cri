<?php
//header('Content-Type', 'text/html; charset=UTF-8');

require_once 'wp-load.php';
var_dump('TEST UTF-8 : é@à');
//CONNECT
//HOST =10.115.100.192)


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
    //reportError(CONST_EMAIL_ERROR_CATCH_EXCEPTION, $error['message']);
    throw new Exception($error['message'], $error['code']);
}
/*
$sql = 'SELECT columns_name \'SRENUM_0\', \'data_type\'
FROM \'all_tab_columns\'
WHERE table_name=\'ZQUEST\'';*/
/*
$sql = 'select *
from user_tab_columns
where table_name = \''.CONST_DB_TABLE_QUESTTEMP.'\'
order by column_id';
*/
$sql = 'select *
from '.CONST_DB_TABLE_QUESTTEMP.'
';
//EXECUTE
//remove potential ending semi-colon.
if (substr($sql, -1) == ';') {
    $sql = substr($sql, -1);
}
//parse and prepare query
$statementId = oci_parse($conn, $sql);
var_dump($statementId);
$isExec = oci_execute($statementId);
var_dump($isExec);
if (!$isExec) {
    $error = oci_error();
    $error = empty($error) ? CONST_CONNECTION_FAILED : $error['message'];
    writeLog($error, 'execute.log');
    //reportError(CONST_EMAIL_ERROR_CATCH_EXCEPTION, $error);
    var_dump('ERROR');
    var_dump($error);
}
$data = oci_fetch_array($statementId);
while (!empty($data)) {

    var_dump('data : ');
    var_dump($data);
    $data = oci_fetch_array($statementId);

}
var_dump('data : ');
var_dump($data);

/*
//FETCH
$insert = '<br>';

while (!empty($data = oci_fetch_array($statementId))){
    //var_dump($data);
    $insert.='(';
    for ($i=0;$i<=34;$i++){
        if (strpos($data[$i],'-JAN-')){
            $data[$i] = substr($data[$i],0,2) . '/01/' . substr($data[$i],7,2);
        } elseif (strpos($data[$i],'-FEB-')){
            $data[$i] = substr($data[$i],0,2) . '/02/' . substr($data[$i],7,2);
        }
        $insert.='\'';
        $insert.=$data[$i];
        $insert.='\'';
        if ($i != 34) {
            $insert.=',';
        } else {
            $insert.=')';
        }
    }

    $insert.='<br>';
}

print_r ($insert);
*/
//END
// Free Result
oci_free_statement($statementId);

// Close Connection
oci_close($conn);

