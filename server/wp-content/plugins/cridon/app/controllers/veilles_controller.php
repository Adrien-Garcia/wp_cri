<?php

/**
 *
 * This file is part of project 
 *
 * File name : veilles_controller.php
 * Project   : wp_cridon
 *
 * @author Etech
 * @contributor Fabrice MILA
 *
 */

class VeillesController extends MvcPublicController {
    //unique matiere selected, default null
    protected static $currentMatiereSelected = null;
    
    public function index() {
        $this->params['per_page'] = !empty($this->params['per_page']) ? $this->params['per_page'] : DEFAULT_POST_PER_PAGE;
        //Set explicit join
        $this->params['joins'] = array(
            'Post','Matiere'
        );
        //Set conditions
        $this->params['conditions'] = array(
            'Post.post_status'=>'publish'            
        );
        //Order by date publish
        $this->params['order'] = 'Post.post_date DESC' ;
        $collection = $this->model->paginate($this->params);
        $matiere = null;
        if( isset($_GET['matiere']) && !empty($_GET['matiere']) && is_array($_GET['matiere']) ){
            //unique matiere
            if(count($_GET['matiere']) === 1){
                $matiere = mvc_model('matiere')->find_one_by_virtual_name(esc_sql(strip_tags($_GET['matiere'][0])));
                if($matiere){
                    self::$currentMatiereSelected = $matiere;
                }
            }
        }
        if($matiere){
            $this->set('h1', $matiere->label);
        } else {
            $this->set('h1', Config::$listingVeille['h1']);
        }
        //selected matiere
        $this->set('matiere', $matiere);
        $this->set('objects', $collection['objects']);
        $this->set_pagination($collection);
        add_action('wp_head', array($this, 'addMetaHeader') );//hook WP to append in header
    }

    public  function addMetaHeader() {
        $meta_title = Config::$listingVeille['meta_title'];
        $meta_description = Config::$listingVeille['meta_description'];
        $canonical = mvc_public_url(array('controller' => MvcInflector::tableize($this->model->name)));
        //unique matiere
        if( self::$currentMatiereSelected !== null ){
            $matiere = self::$currentMatiereSelected;
            $meta_title = $matiere->meta_title;
            $meta_description = $matiere->meta_description;
            //generate url
            $canonical = mvc_public_url(array('controller' => MvcInflector::tableize($matiere->__model_name),'id' => $matiere->virtual_name));            
        }
        //variable to output in view
        $options = array(
            'locals' => array(
                'meta_title'        => $meta_title,
                'meta_description'  => $meta_description,
                'canonical'         => $canonical
            )
        );
        //render view meta
        $this->render_view_with_view_vars('veilles/meta', $options);
    }
    public function show() {
        if ( !CriIsNotaire() ) {
            CriRefuseAccess();
        } else {
            parent::show();
        }
    }

    /**
     * @override
     */
    public function set_pagination($collection) {
        parent::set_pagination($collection);
        if( isset( $this->pagination['add_args'] ) ){
            if( isset( $this->pagination['add_args']['controller'] ) ){
                unset( $this->pagination['add_args']['controller'] );
            }
            if( isset( $this->pagination['add_args']['action'] ) ){
                unset( $this->pagination['add_args']['action'] );
            }
        }
    }
    
}

?>