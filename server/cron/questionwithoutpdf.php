<?php

/**
 *
 * This file is part of project 
 *
 * File name : cron-question-without-doc.php
 * Project   : wp_cridon
 *
 * @author Etech
 * @contributor Fabrice MILA
 *
 */

require_once '../wp-load.php';
$document = mvc_model('Question');
$result = $document->checkQuestionsWithoutDocuments();
if( $result ){
    echo 'Un mail a été envoyé';
}else{
    echo 'Aucun mail n\'a été envoyé';
}