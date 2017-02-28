<?php

/**
 *
 * This file is part of project 
 *
 * File name : UIDocumentContainer.php
 * Project   : wp_cridon
 *
 */

class UIDocumentContainer extends UIContainer {

    private $content;
    private $title;
    private $documentDatabase;
    private $currentObject;
    private $currentModel;
    private $type;

    public function __construct(){
        $this->documentDatabase  = new UIDocumentDatabase();
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
        //Fill with not associated documents
        $data = $this->documentDatabase->find( array('conditions' => array(
            'type' => $this->type,
            'id_externe' => '',
        ) ) );
        $this->left = $this->createDocumentItems($data);
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
            //Get documents for current model
            $options = array(
                'conditions' => array(
                    'type' => $this->type,
                    'id_externe' => $this->currentObject->id,
                )
            );
            $data = $this->documentDatabase->find($options);
            //Documents of current object ( model )
            $this->right = $this->createDocumentItems($data);
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

    protected function createDocumentItems($data) {
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
        if( isset( $_POST ) ) {
            //Update documents in database
            if (!empty( $_POST['uiDocument'] )  ){
                $data = array();
                foreach( $_POST['uiDocument'] as $doc ){
                    $cls = new stdClass();
                    $cls->id = preg_replace("/ui_a/", '', $doc);
                    $cls->id_externe = $this->currentObject->id;
                    $cls->type = strtolower($this->currentModel->name);
                    $data[] = $cls;
                }
                $this->documentDatabase->save($data);
            } else {
                if (!empty($this->currentObject)) {
                    $data = new stdClass();
                    $data->id_externe = $this->currentObject->id;
                    $data->type = strtolower($this->currentModel->name);
                    $this->documentDatabase->deleteAll($data);
                }
            }
        }
    }
}
