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
$document->checkQuestionsWithoutDocuments();