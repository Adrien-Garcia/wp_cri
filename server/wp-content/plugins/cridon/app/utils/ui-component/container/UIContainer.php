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

    protected $content;
    protected $title;
    protected $currentObject;
    protected $currentModel;
    protected $type;

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
    public function setModel( $modelName ){
        $model = mvc_model($modelName);
        $this->type = $modelName;
        $this->currentModel = $model;
    }

    /**
     * Set current object
     *
     * @param object $object
     */
    public function setObject( $object ){
        $this->currentObject = $object;
    }

    /**
     * Define content
     *
     * @param mixed $content
     */
    public function setContent( $content ){
        $this->content = $content;
    }

    /**
     * Set title of container to be displayed
     *
     * @param string $title
     */
    public function setTitle( $title ){
        $this->title = $title;
    }
    
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
     * Load Sortable Jquery library in model view
     */
    public function loadLibraryJsOnModel(){
        wp_enqueue_script( 'jquery-ui-sortable' );//Sortable
    }

    /**
     * Construct view
     * @param $model string - model to display
     */
    public function create($model){
        $input = new UIText();
        $input->setPlaceholder('Rechercher');
        $input->setClass('relationship_search');
        $input->setId('uiSearch');
        $html = ' <div class="field relationship_search">';
        $html .= '<p class="label">'
            . '<label for="acf-field-document_sur_les_contenus">'.$this->title.'</label></p>';
        $html .= '<input type="hidden" id="ui-document-type" value="'.$this->type.'" />';
        $html .= '<div class="cri_relationship has-search">';
        $html .= '<div class="relationship_left">';
        $html .= '<table class="widefat"><thead><tr><th>';
        $html .= $input->create();
        $html .= '</th></tr></thead></table>';
        $html .= $this->createLeft($model);
        $html .= '</div>';
        $html .= '<div class="relationship_right">';
        $html .= $this->createRight($model);
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        echo $html;
    }

    abstract protected function prepareLeftData();

    /**
     * Create left view
     *
     * @return string
     */
    protected function createLeft(){
        $data = $this->prepareLeftData();
        $ul = new UIList();
        $ul->setClass('bl relationship_list ui-sortable');
        if( empty( $data ) ){
            return $ul->create();
        }

        $lists = array();
        foreach( $data as $left ){
            $li = new UIListChild();
            $a = new UILink();
            $a->setText( $left->name );
            $a->setId( 'ui_a'.$left->id );
            $span1 = new UISpan();
            $span1->setClass('relationship-item-info');
            $span1->setText($this->modelName);
            $span2 = new UISpan();
            $span2->setClass('cri-button-add');
            $a->setContent(array($span1,$span2));
            $li->setContent($a);
            $lists[] = $li;
        }
        $ul->setContent( $lists );
        return $ul->create();
    }

    abstract protected function prepareRightData();

    /**
     * Create right view
     *
     * @return string
     */
    protected function createRight(){
        $data = $this->prepareRightData();
        $ul = new UIList();
        $ul->setClass('bl relationship_list');
        if( empty( $data ) ){
            return $ul->create();
        }
        $lists = array();
        foreach( $data as $right ){
            $li = new UIListChild();
            $a = new UILink();
            $a->setText( $right->name );
            $a->setId( 'ui_a'.$right->id );
            $span1 = new UISpan();
            $span1->setClass('relationship-item-info');
            $span1->setText($this->modelName);
            $span2 = new UISpan();
            $span2->setClass('cri-button-remove');
            $hidden = new UIHidden();
            $hidden->setName('ui'.$this->modelName.'[]');
            $hidden->setValue('ui_a'.$right->id);
            $a->setContent(array($span1,$span2,$hidden));
            $li->setContent($a);
            $lists[] = $li;
        }
        $ul->setContent( $lists );
        return $ul->create();
    }

    abstract protected function save();

}
