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
require_once 'post/CridonPostStorage.php';
require_once 'post/CridonPostUrl.php';
require_once 'post/CridonPostStructure.php';
require_once 'post/CridonPostParser.php';
require_once 'post/CridonPostQuery.php';
require_once 'post/CridonPostFactory.php';
//
require_once 'CridonObjectFactory.php';
require_once 'CridonTools.php';
// Custom function used in template
require_once 'functions.php';

global $cri_container;// use global before using this
//It's an very simple dependancy injector container
$cri_container = new Container();


// Save instance of query builder
$cri_container->set('query_builder', function(){
        return new QueryBuilder();
    }
);

$cri_container->set('post_factory', function() use( $cri_container ){
        //Object CridonPostFactory depend of post_structure
        return new CridonPostFactory( $cri_container->get( 'post_structure' ) );
    }
);

$cri_container->set('post_structure', function(){
        //Structure of wp_posts
        return new CridonPostStructure();
    }
);

$cri_container->set('tools', function() use( $cri_container ){
        //Object CridonTools depend of post_factory and post_structure
        return new CridonTools( $cri_container->get( 'post_factory' ),$cri_container->get( 'post_structure' ) );
    }
);

$cri_container->set('post_query', function() use( $cri_container ){
        //To use all functions in WP
        return new CridonPostQuery( $cri_container->get( 'post_factory' ) );
    }
);

$cri_container->set('post_parser', function(){
        //Generate an array of WP_Post
        return new CridonPostParser();
    }
);

//Web service

include_once 'web-service/class_loader.php';

//UI Component

include_once 'ui-component/class_loader.php';