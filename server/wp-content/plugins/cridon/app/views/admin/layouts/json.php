<?php

header("Content-Type: application/json", true);
$res = array();
foreach( $data as $v ){
    $cls = new stdClass();
    $cls->id = $v->id;
    $cls->name = $v->name;
    $res[] = $cls;
}
echo json_encode( $res );
