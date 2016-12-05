<?php

class Flash extends \App\Override\Model\CridonMvcModel {
    use DocumentsHolderTrait;

    var $table     = "{prefix}flash";
    var $includes       = array('Post', 'Matiere');
    var $belongs_to     = array(
        'Post' => array('foreign_key' => 'post_id'),
        'Matiere' => array('foreign_key' => 'id_matiere')
    );
    var $display_field = 'post_id';
}

?>
