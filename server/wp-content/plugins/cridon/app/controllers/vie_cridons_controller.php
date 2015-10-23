<?php

/**
 *
 * This file is part of project 
 *
 * File name : vie_cridons_controller.php
 * Project   : wp_cridon
 *
 * @author Etech
 * @contributor Fabrice MILA
 *
 */

class VieCridonsController extends MvcPublicController {
    /*
     * We use the standard function for wordpress for queries ( query_posts() ) in views
     */
    public function show() {
        global $wpdb;
        global $custom_global_join;
        $custom_global_join = ' JOIN '.$this->model->table.' ON '.$wpdb->posts.'.ID = '.$this->model->table.'.post_id';
        global $custom_global_where;
        $custom_global_where = ' AND '.$this->model->table.'.id = ' . $this->params['id'];        
    }
    public function index() {
        global $wpdb;
        global $custom_global_join;
        $custom_global_join = ' JOIN '.$this->model->table.' ON '.$wpdb->posts.'.ID = '.$this->model->table.'.post_id';
    }
}

?>