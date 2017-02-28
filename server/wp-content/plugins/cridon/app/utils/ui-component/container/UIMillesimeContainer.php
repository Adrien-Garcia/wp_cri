<?php

/**
 *
 * This file is part of project 
 *
 * File name : UIMillesimeContainer.php
 * Project   : wp_cridon
 *
 */

class UIMillesimeContainer extends UIContainer {
    
    private $content;
    private $title;
    private $millesimeDatabase;
    private $currentObject;
    private $currentModel;
    private $type;

    public function __construct(){
        $this->millesimeDatabase = new UIMillesimeDatabase();
        parent::__construct();
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

    /**
     * Create left view
     * @param $modelName string - model to display
     *
     * @return string
     */
    protected function createLeft($modelName){
        // We give the possibility to add millesime from Y-1 to Y+2
        $this->left = array();
        for ($i= -1 ;$i < 3 ;$i++){
            $left       = new stdClass();
            $left->id = $left->name = date("Y",strtotime($i." year"));
            $this->left [] = $left;
        }
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
            $span1->setText($modelName);
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
     * @param $modelName string - model to display
     * @return string
     */
    protected function createRight($modelName){
        if( $this->currentObject != null ){
            //Get millésime for current model
            $options = array(
                'conditions' => array(
                    'id_formation' => $this->currentObject->id,
                )
            );
            $data = $this->millesimeDatabase->find($options);
            $this->right = array();
            foreach ($data as $item){
                $right = new stdClass();
                $right->id = $right->name = $item->year;
                $this->right [] = $right;
            }
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
            $span1->setText($modelName);
            $span2 = new UISpan();
            $span2->setClass('cri-button-remove');
            $hidden = new UIHidden();
            $hidden->setName('ui'.$modelName.'[]');
            $hidden->setValue('ui_a'.$right->id);
            $a->setContent(array($span1,$span2,$hidden));
            $li->setContent($a);
            $lists[] = $li;
        }
        $ul->setContent( $lists );
        return $ul->create();
    }
    
    public function save(){
        if( isset( $_POST ) ) {
            // Remove all millesime for current formation in database
            $this->millesimeDatabase->deleteAll($this->currentObject->id);
            if (!empty( $_POST['uiMillesime'] ) ){
                $data = array();
                foreach( $_POST['uiMillesime'] as $millesime ){
                    $cls = new stdClass();
                    $cls->year  = preg_replace("/ui_a/", '', $millesime);
                    $cls->id_formation = $this->currentObject->id;
                    $data[] = $cls;
                }
                $this->millesimeDatabase->save($data);
            }
        }
    }
}
