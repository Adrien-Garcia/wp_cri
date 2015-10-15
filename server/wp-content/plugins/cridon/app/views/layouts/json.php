<?php

header("Content-Type: application/json", true);
$data = new stdClass();
$data->status = $status;
if (isset($errors)) {
    $data->errors = $errors;
}
$encoded = json_encode($data);
echo $encoded;

