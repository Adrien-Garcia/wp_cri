<?php

/**
 *
 * This file is part of project 
 *
 * File name : class_loader.php
 * Project   : wp_cridon
 *
 * @author Etech
 * @contributor Fabrice MILA
 *
 */

require_once 'UIFields.php';
require_once 'fields/UISpan.php';
require_once 'fields/UILink.php';
require_once 'fields/UIHidden.php';
require_once 'fields/UIListChild.php';
require_once 'fields/UIText.php';
require_once 'fields/UIList.php';

require_once 'database/UIDatabase.php';
require_once 'database/UIDocumentDatabase.php';
require_once 'database/UIMillesimeDatabase.php';
require_once 'database/UIMatiereDatabase.php';

require_once 'container/UIContainer.php';
require_once 'container/UIDocumentContainer.php';
require_once 'container/UIMillesimeContainer.php';
require_once 'container/UIMatiereContainer.php';

$cri_container->set('ui_matiere_container', function(){
    return new UIMatiereContainer();
});
$cri_container->set('ui_millesime_container', function(){
    return new UIMillesimeContainer();
});
$cri_container->set('ui_document_container', function(){
    return new UIDocumentContainer();
});