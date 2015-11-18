<?php

class Document extends MvcModel {

    var $display_field = 'file_path';
    var $table         = '{prefix}document';
    
    public function create($data) {
        //Before insert 
        $date = new DateTime('now');
        $data[ 'Document' ][ 'date_modified' ] = $date->format('Y-m-d H:i:s');
        return parent::create($data);
    }
    public function save($data) {
        //Before update
        $date = new DateTime('now');
        $data[ 'Document' ][ 'date_modified' ] = $date->format('Y-m-d H:i:s');
        return parent::save($data);
    }

    /**
     * Import initial
     */
    public function importInitial()
    {
        $documents = glob(CONST_IMPORT_DOCUMENT_PATH . '/*');
//        echo '<pre>'; die(print_r($documents));
    }

}

?>