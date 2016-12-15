<?php

class VieCridon extends \App\Override\Model\CridonMvcModel {

    use DocumentsHolderTrait;

    var $table          = "{prefix}vie_cridon";
    var $includes       = array('Post');
    var $belongs_to     = array(
        'Post' => array('foreign_key' => 'post_id')
    );
    var $display_field = 'post_id';
}

?>
