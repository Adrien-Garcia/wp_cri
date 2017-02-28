<?php

/**
 *
 * This file is part of project 
 *
 * File name : UIContainer.php
 * Project   : wp_cridon
 *
 * @author Etech
 * @contributor Fabrice MILA
 *
 */

abstract class UIContainer extends UIFields{

    public function __construct(){
        //Defaults
        $this->setClass('inside');
        $this->init();
    }
    
    /**
     * Set model name
     *
     * @param string $modelName
     */
    abstract protected function setModel( $modelName );
    
    /**
     * Set current object
     * 
     * @param object $object
     */
    abstract protected function setObject( $object );
    
    /**
     * Load custom JS and CSS
     */
    protected function init(){
        wp_register_style( 'ui-component-css', plugins_url('cridon/app/public/css/style.css'), false ); 
        wp_enqueue_style( 'ui-component-css' );
        wp_register_script( 'ui-component-js', plugins_url('cridon/app/public/js/ui-app.js'), array('jquery') );
        wp_enqueue_script('ui-component-js');
    }
    
    /**
     * Define content
     * 
     * @param mixed $content
     */
    abstract protected function setContent( $content );
    
    /**
     * Set title of container to be displayed
     * 
     * @param string $title
     */
    abstract protected function setTitle( $title );
    
    /**
     * Load Sortable Jquery library in model view
     */
    public function loadLibraryJsOnModel(){
        wp_enqueue_script( 'jquery-ui-sortable' );//Sortable
    }

    /**
     * Construct view
     * @param $model string - model to display
     */
    abstract protected function create($model);

    /**
     * Create left view
     * @param $modelName string - model to display
     *
     * @return string
     */
    abstract protected function createLeft($modelName);

    /**
     * Create right view
     *
     * @param $modelName string - model to display
     * @return string
     */
    abstract protected function createRight($modelName);
}
