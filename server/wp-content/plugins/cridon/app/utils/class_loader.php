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
// All Posts Class
require_once 'post/CridonPostUrl.php';
require_once 'post/CridonPostParser.php';
require_once 'post/CridonPostQuery.php';
require_once 'post/CridonPostFactory.php';
//
require_once 'CridonTools.php';
require_once 'functions.php';

global $cri_container;// use global before using this

$cri_container = new Container();


// Save instance of query builder
$cri_container->set('query_builder', function(){
        return new QueryBuilder();
    }
);

$cri_container->set('post_factory', function(){
        return new CridonPostFactory();
    }
);

$cri_container->set('tools', function() use( $cri_container ){
        return new CridonTools( $cri_container->get( 'post_factory' ) );
    }
);

$cri_container->set('post_query', function(){
        return new CridonPostQuery();
    }
);

$cri_container->set('post_parser', function(){
        return new CridonPostParser();
    }
);
