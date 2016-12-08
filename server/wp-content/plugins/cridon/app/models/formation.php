<?php

class Formation extends \App\Override\Model\CridonMvcModel
{
    use DocumentsHolderTrait;

    var $table = "{prefix}formation";
    var $includes = array('Post','Matiere', 'Session');
    var $belongs_to = array(
        'Post' => array('foreign_key' => 'post_id'),
        'Matiere' => array('foreign_key' => 'id_matiere')
    );
    var $has_many = array(
        'Session' => array('foreign_key' => 'id_formation')
    );
    var $display_field = 'name';
}
