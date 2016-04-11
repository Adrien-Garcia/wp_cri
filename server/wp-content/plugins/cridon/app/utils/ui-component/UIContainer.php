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

class UIContainer extends UIFields{
    
    private $content;
    private $title;
    private $database;
    private $currentObject;
    private $currentModel;
    private $type;

    public function __construct(){
        $this->database = new UIDatabase();
        //Defaults
        $this->setClass('inside');
        $this->setTitle( 'Documents' );
        //
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
     * Load Sortable Jquery library in model view
     */
    public function loadLibraryJsOnModel(){
        wp_enqueue_script( 'jquery-ui-sortable' );//Sortable
    }
    
    /**
     * Construct view
     */
    public function create(){
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
        $html .= $this->createLeft();
        $html .= '</div>';
        $html .= '<div class="relationship_right">';
        $html .= $this->createRight();
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        echo $html;
    }
    
    /**
     * Create left view
     * 
     * @return string
     */
    protected function createLeft(){
        //Fill with not associated documents
        $data = $this->database->find( array('conditions' => array(
            'type' => $this->type,
            'id_externe' => 0,
        ) ) );
        $this->left = $this->createItems($data);;
        $ul = new UIList();
        $ul->setClass('bl relationship_list ui-sortable');
        if( empty( $this->left ) ){
            return $ul->create();
        }
        
        $lists = array();
        foreach( $this->left as $left ){
            $li = new UIListChild();
            $a = new UILink();
            $a->setText( $left->name );
            $a->setId( 'ui_a'.$left->id );
            $span1 = new UISpan();
            $span1->setClass('relationship-item-info');
            $span1->setText('document');
            $span2 = new UISpan();
            $span2->setClass('cri-button-add');
            $a->setContent(array($span1,$span2));
            $li->setContent($a);
            $lists[] = $li;
        }
        $ul->setContent( $lists );
        return $ul->create();
    } 
    /**
     * Create right view
     * 
     * @return string
     */
    protected function createRight(){
        if( $this->currentObject != null ){
            $options = array(
                'conditions' => array(
                    'type' => $this->type,
                    'id_externe'=>  $this->currentObject->id,
                ) 
            );
            $data = $this->database->find( $options );

            //Documents of current object ( model ) 
            $this->right = $this->createItems($data);
        }
        $ul = new UIList();
        $ul->setClass('bl relationship_list');
        if( empty( $this->right ) ){
            return $ul->create();
        }        
        $lists = array();
        foreach( $this->right as $right ){
            $li = new UIListChild();
            $a = new UILink();
            $a->setText( $right->name );
            $a->setId( 'ui_a'.$right->id );
            $span1 = new UISpan();
            $span1->setClass('relationship-item-info');
            $span1->setText('document');
            $span2 = new UISpan();
            $span2->setClass('cri-button-remove');
            $hidden = new UIHidden();
            $hidden->setName('uiDocument[]');
            $hidden->setValue('ui_a'.$right->id);
            $a->setContent(array($span1,$span2,$hidden));
            $li->setContent($a);
            $lists[] = $li;
        }
        $ul->setContent( $lists );
        return $ul->create();
    }

    protected function createItems($data) {
        $res = array();
        foreach( $data as $v ){
            $cls = new stdClass();
            $cls->id = $v->id;
            $fileinfo = explode('/', $v->file_path);
            $cls->name = array_pop($fileinfo);
            $res[] = $cls;
        }
        return $res;
    }
    
    public function save(){
        if( isset( $_POST ) && !empty( $_POST['uiDocument'] )  ){
            $data = array();
            foreach( $_POST['uiDocument'] as $doc ){
                $cls = new stdClass();
                $ptn = "/ui_a/";
                $id  = preg_replace($ptn, '', $doc);
                $cls->id = $id;
                $cls->id_externe = $this->currentObject->id;
                $cls->type = strtolower($this->currentModel->name);
                $data[] = $cls;
            }
            $this->database->save($data);
        }else{
            if( !empty( $this->currentObject ) ){
                $data = new stdClass();
                $data->id_externe = $this->currentObject->id;
                $data->type = strtolower($this->currentModel->name);
                $this->database->deleteAll($data);
            }
        }
    }
}
