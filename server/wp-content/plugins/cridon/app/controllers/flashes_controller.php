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

class FlashesController extends MvcPublicController {
    /*
     * We use the standard function for wordpress for queries ( query_posts() ) in views
     */
   public function show() {
        if ( !CriIsNotaire() ) {
            $referer = $_SERVER['HTTP_REFERER'];
            $redirect = $referer;

            if (preg_match("/.*?[\?\&]openLogin=1.*?/", $referer) === 1 && preg_match("/.*?[\?\&]messageLogin=PROTECTED_CONTENT.*?/", $referer) === 1 ) {
                wp_redirect($redirect);
                return;
            }

            if (preg_match("/.*\?.*/", $referer)) {
                $redirect .= "&";
            } else {
                $redirect .= "?";
            }

            $redirect .= "openLogin=1&messageLogin=PROTECTED_CONTENT";

            wp_redirect($redirect);
        } else {
            parent::show();
        }     
    }
    public function index() {
        if ( !CriIsNotaire() ) {
            $referer = $_SERVER['HTTP_REFERER'];
            $redirect = $referer;

            if (preg_match("/.*?[\?\&]openLogin=1.*?/", $referer) === 1 && preg_match("/.*?[\?\&]messageLogin=PROTECTED_CONTENT.*?/", $referer) === 1 ) {
                wp_redirect($redirect);
                return;
            }

            if (preg_match("/.*\?.*/", $referer)) {
                $redirect .= "&";
            } else {
                $redirect .= "?";
            }

            $redirect .= "openLogin=1&messageLogin=PROTECTED_CONTENT";

            wp_redirect($redirect);
        } else {
            $this->params['per_page'] = !empty($this->params['per_page']) ? $this->params['per_page'] : DEFAULT_POST_PER_PAGE;
            //Set explicit join
            $this->params['joins'] = array(
                'Post'
            );
            //Set conditions
            $this->params['conditions'] = array(
                'Post.post_status'=>'publish'            
            );
            //Order by date publish
            $this->params['order'] = 'Post.post_date DESC' ;
            $collection = $this->model->paginate($this->params);

            $this->set('objects', $collection['objects']);
            $this->set_pagination($collection);
        }
    }
}

?>