<?php

/**
 *
 * This file is part of project 
 *
 * File name : majdocument.php
 * Project   : wp_cridon
 *
 * @author Etech
 * @contributor Fabrice MILA
 *
 */
require_once '../wp-load.php';
$document = mvc_model('Document');
$result = $document->importInitial();
echo $result;