<?php

class Post extends MvcModel {
    var $primary_key   = 'ID';
    var $display_field = 'post_title';
    var $table         = '{prefix}posts';
    var $has_many      = array(
        'Veille' => array(
            'foreign_key' => 'post_id'
        ),
        'Flash' => array(
            'foreign_key' => 'post_id'
        ),
        'Formation' => array(
            'foreign_key' => 'post_id'
        ),
        'CahierCridon' => array(
            'foreign_key' => 'post_id'
        ),
        'ActuCridon' => array(
            'foreign_key' => 'post_id'
        )
    );
}

?>