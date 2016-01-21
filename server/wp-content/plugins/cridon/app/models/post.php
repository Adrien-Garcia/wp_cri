<?php

class Post extends \App\Override\Model\CridonMvcModel {
    var $primary_key   = 'ID';
    var $display_field = 'post_title';
    var $table         = '{prefix}posts';
    var $has_many      = array(
        'Veille' => array(
            'foreign_key' => 'ID'
        ),
        'Flash' => array(
            'foreign_key' => 'ID'
        ),
        'Formation' => array(
            'foreign_key' => 'ID'
        ),
        'CahierCridon' => array(
            'foreign_key' => 'ID'
        ),
        'VieCridon' => array(
            'foreign_key' => 'ID'
        )
    );
}

?>