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
        //All Matiere
        $matieres = mvc_model('Matiere')->find();
        //Filter by Matiere
        if( isset($_GET['matiere']) && !empty($_GET['matiere']) ){
            $q = esc_sql(strip_tags(urldecode($_GET['matiere'])));
            $virtual_names = explode(',',$q);
            $this->params['conditions'] = array(
                'Post.post_status'=>'publish',
                'Matiere.virtual_name'=> $virtual_names         
            );
            foreach($matieres as $matiere){
                if( in_array($matiere->virtual_name,$virtual_names) ){
                    $matiere->filtered = true;
                }else{
                    $matiere->filtered = false;
                }
            }
        }else{
            foreach($matieres as $matiere){
                $matiere->filtered = false;
            }
        }
        //Order by date publish
        $this->params['order'] = 'Post.post_date DESC' ;
        $collection = $this->model->paginate($this->params);
        
        $this->set('objects', $collection['objects']);
        $this->set('matieres',$matieres);//All matieres
        $this->set_pagination($collection);
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
}

?>