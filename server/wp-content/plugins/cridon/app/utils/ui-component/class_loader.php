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
require_once 'UIDocumentDatabase.php';
require_once 'UIMillesimeDatabase.php';
require_once 'UIFields.php';
require_once 'UIContainer.php';
require_once 'fields/UISpan.php';
require_once 'fields/UILink.php';
require_once 'fields/UIHidden.php';
require_once 'fields/UIListChild.php';
require_once 'fields/UIText.php';
require_once 'fields/UIList.php';

$cri_container->set('ui_container', function(){
        return new UIContainer();
    }
);