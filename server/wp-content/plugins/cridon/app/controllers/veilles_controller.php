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
require_once 'base_actu_controller.php';
class VeillesController extends BaseActuController {

    //unique matiere selected, default null
    protected static $currentMatiereSelected = null;

    /**
     * @var \Matiere
     */
    protected $currentMatSelectedModel;

    /**
     * @var array
     */
    protected $virtualNames = array();

    /**
     * @var mixed
     */
    protected $matieres;

    public function index() {
        $veille = new Veille;
        $this->matieres = Matiere::getMatieresByModelPost($veille);
        if ( isset($_GET['matieres']) && is_array($_GET['matieres']) && count($_GET['matieres']) > 0 ) {
            // apply filters
            $this->applyFilters($_GET['matieres']);
        } else {
            foreach ($this->matieres as $mat) {
                $mat->filtered = false;
            }
        }
        if ($this->currentMatSelectedModel) {
            self::$currentMatiereSelected = $this->currentMatSelectedModel;
            $this->set('h1', $this->currentMatSelectedModel->label);
        } else {
            $this->set('h1', Config::$listingVeille['h1']);
        }
        $collection = $this->model->getList($this->params);
        //selected matiere
        $this->set('matieres', $this->matieres);
        $this->set('objects', $collection['objects']);
        $this->set_pagination($collection);
        add_action('wp_head', array($this, 'addMetaHeader') );//hook WP to append in header
    }

    /**
     * Apply criteria filters
     *
     * @param array $criteria
     * @throws Exception
     */
    protected function applyFilters($criteria)
    {
        if (count($criteria) === 1) {
            $this->currentMatSelectedModel = mvc_model('matiere')->find_one_by_virtual_name(esc_sql(strip_tags($criteria[0])));
            if ($this->currentMatSelectedModel) {
                self::$currentMatiereSelected = $this->currentMatSelectedModel;
            }
        }

        foreach ($criteria as $mat){
            $this->virtualNames[] = esc_sql(strip_tags($mat));
        }
        foreach($this->matieres as $mat){
            if( in_array($mat->virtual_name, $this->virtualNames) ){
                $mat->filtered = true;
            }else{
                $mat->filtered = false;
            }
        }
        $this->params['conditions'] = array(
            'Matiere.virtual_name'=> $this->virtualNames
        );
    }

    public  function addMetaHeader() {
        $meta_title = Config::$listingVeille['meta_title'];
        $meta_description = Config::$listingVeille['meta_description'];
        $canonical = mvc_public_url(array('controller' => MvcInflector::tableize($this->model->name)));
        //unique matiere
        if( self::$currentMatiereSelected !== null ){
            $matiere = self::$currentMatiereSelected;
            $meta_title = !empty($matiere->meta_title) ? $matiere->meta_title : $meta_title;
            $meta_description = !empty($matiere->meta_description) ? $matiere->meta_description : $meta_description;
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

    /**
     * Clean array
     *
     * @param array $data
     */
    protected function clean(&$data){
        $data = array_unique($data);
        foreach ( $data as $k => $v ){
            if( !is_numeric($v) ){
                unset($data[$k]);
            }
        }
    }
    //RSS feed
    public function feed(){
        $options = array();
        $options['joins'] = array(
            'Post','Matiere'
        );
        //Set conditions
        $options['conditions'] = array(
            'Post.post_status'=>'publish'
        );
        //Order by date publish
        $$options['order'] = 'Post.post_date DESC' ;
        $title = Config::$rss['title'];//Title of RSS
        $objects = $this->model->find($options);
        $this->set('title',$title);
        $this->set('objects',$objects);
        $this->set('description',Config::$rss['description']);
        $this->render_view('feed', array('layout' => 'feed'));
    }

    public function feedFilter(){
        $matiere = mvc_model('Matiere')->find_one_by_id($this->params['id']);
        if(!$matiere){//no matiere found
            redirectTo404();
        }
        $options = array();
        $options['joins'] = array(
            'Post','Matiere'
        );
        //Set conditions
        $options['conditions'] = array(
            'Post.post_status'=>'publish',
            'Matiere.id'=> $this->params['id']
        );
        //Order by date publish
        $options['order'] = 'Post.post_date ASC' ;
        $objects = $this->model->find($options);
        $title = sprintf(Config::$rss['title_mat'], $matiere->label );//Title of RSS
        $this->set('title',$title);
        $this->set('objects',$objects);
        $this->set('description',Config::$rss['description']);
        $this->render_view('feed', array('layout' => 'feed'));
    }

}

?>
