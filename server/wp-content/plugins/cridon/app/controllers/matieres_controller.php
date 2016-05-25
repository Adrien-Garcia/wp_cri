<?php

/**
 *
 * This file is part of project
 *
 * File name : matieres_controller.php
 * Project   : wp_cridon
 * @author   : JetPulp
 * @contributor : Renaud Amsellem
 *
 */
require_once 'base_public_controller.php';

class MatieresController extends BasePublicController
{
    //unique matiere selected, default null
    protected static $currentMatiereSelected = null;

    function show(){
        $matiere = $this->params['id'];
        if (!empty($matiere)) {
            //$virtual_name = array(esc_sql(strip_tags($matiere)));
            $matiere = mvc_model('matiere')->find_one_by_virtual_name(esc_sql(strip_tags($matiere)));
            if ($matiere){
                self::$currentMatiereSelected = $matiere;
            }
            $this->params['conditions'] = array(
                'Matiere.virtual_name'=> $matiere->virtual_name
            );
            $veille = new Veille;
            $collection = $veille->getList($this->params);
            $matieres = Matiere::getMatieresByModelPost($veille);
            foreach($matieres as $mat){
                if($mat->virtual_name == $matiere->virtual_name){
                    $mat->filtered = true;
                } else {
                    $mat->filtered = false;
                }
            }
            if (!$collection['objects']){
                redirectTo404();
            } else {
                add_action('wp_head', array($this, 'rssVeilles'));
                $this->set('h1', $matiere->label);
                $this->set('objects', $collection['objects']);
                $this->set('matieres',$matieres);
                $this->set_pagination($collection);
                add_action('wp_head', array($this, 'addMetaHeader') );//hook WP to append in header
            };
        } else {
            redirectTo404();
        }
    }

    public function rssVeilles(){
        if (!empty(self::$currentMatiereSelected)) {
            $title = sprintf(Config::$rss['title_mat'],self::$currentMatiereSelected->label);
            $feed = mvc_public_url(array('controller' => 'veilles','action' =>'feedFilter', 'id' =>self::$currentMatiereSelected->id));
        } else {
            $title = Config::$rss['title'];
            $feed = mvc_public_url(array('controller' => 'veilles','action' =>'feed'));
        }

        $options = array(
            'locals' => array(
                'title'         => $title,
                'feed'          => $feed
            )
        );
        $this->render_view_with_view_vars('layouts/rssLink', $options);
    }

    public  function addMetaHeader() {
        $matiere = self::$currentMatiereSelected;
        $meta_title = !empty($matiere->meta_title) ? $matiere->meta_title : Config::$listingVeille['meta_title'];
        $meta_description = !empty($matiere->meta_description) ? $matiere->meta_description : Config::$listingVeille['meta_description'];
        //generate url
        $canonical = mvc_public_url(array('controller' => MvcInflector::tableize($matiere->__model_name),'id' => $matiere->virtual_name));
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
    /**
     * @override
     */
    public function set_pagination($collection) {
        $this->name = 'veilles';
        $this->action = 'index';
        if (!empty(self::$currentMatiereSelected)) {
            $this->params['matieres'][] = self::$currentMatiereSelected->virtual_name;
        }
        parent::set_pagination($collection);
        if( isset( $this->pagination['add_args'] ) ){
            if( isset( $this->pagination['add_args']['controller'] ) ){
                unset( $this->pagination['add_args']['controller'] );
            }
            if( isset( $this->pagination['add_args']['action'] ) ){
                unset( $this->pagination['add_args']['action'] );
            }
            if( isset( $this->pagination['add_args']['id'] ) ){
                unset( $this->pagination['add_args']['id'] );
            }
        }
    }
}
