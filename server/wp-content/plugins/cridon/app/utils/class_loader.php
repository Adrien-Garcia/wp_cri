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
$dir        = dirname( realpath( __FILE__ ) );
$pluginRoot = dirname( $dir );
require_once 'Container.php';
require_once $pluginRoot.'/models/query_builder.php';
require_once 'CridonTools.php';
require_once 'functions.php';

global $cri_container;// use global before using this

$cri_container = new Container();


// Save instance of query builder
$cri_container->set('query_builder', function(){
        return new QueryBuilder();
    }
);

$cri_container->set('tools', function(){
        return new CridonTools();
    }
);