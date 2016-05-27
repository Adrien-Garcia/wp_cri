<?php

/**
 *
 * This file is part of project 
 *
 * File name : flashes_controller.php
 * Project   : wp_cridon
 *
 * @author Etech
 * @contributor Fabrice MILA
 *
 */
require_once 'base_actu_controller.php';
class FlashesController extends BaseActuController {
    /*
     * We use the standard function for wordpress for queries ( query_posts() ) in views
     */
   public function show() {
       if ( !CriIsNotaire() ) {
           CriRefuseAccess();
       } elseif (!CriCanAccessSensitiveInfo(CONST_CONNAISANCE_ROLE)) {
           $options = array(
               'controller' => 'notaires',
               'action'     => 'cridonline'
           );
           $publicUrl  = MvcRouter::public_url($options);
           $publicUrl.='?error=FONCTION_NON_AUTORISE';
           wp_redirect( $publicUrl, 302 );
       } else {
           parent::show();
       }
    }
    public function index()
    {
        if (!CriIsNotaire()) {
            CriRefuseAccess();
        } else {
            $this->params['per_page'] = !empty($this->params['per_page']) ? $this->params['per_page'] : DEFAULT_POST_PER_PAGE;
            //Set explicit join
            $this->params['joins'] = array(
                'Post'
            );
            //Set conditions
            $this->params['conditions'] = array(
                'Post.post_status' => 'publish'
            );
            //Order by date publish
            $this->params['order'] = 'Post.post_date DESC';
            $collection = $this->model->paginate($this->params);

            $this->set('objects', $collection['objects']);
            $this->set_pagination($collection);
        }
    }
}

?>