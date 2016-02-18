<?php

class Competence extends \App\Override\Model\CridonMvcModel
{
    var $display_field  = 'label';
    var $table          = '{prefix}competence';
    var $includes       = array('Matiere');
    var $belongs_to     = array(
        'Matiere' => array('foreign_key' => 'code_matiere')
    );
    
    public function create($data) {
        $id = $this->getLatest()->id;
        $id++;//increment
        if(empty($id)){//No competence in DB
            $id = 1;
        }
        $data['Competence']['id'] = $id;
        parent::create($data);
        $this->insert_id = $id;//Set manualy last insert id
        return $id;
    }
    
    public function getLatest(){
        global $wpdb;
        $sql = 'SELECT id FROM '.$wpdb->prefix.'competence ORDER BY CAST(id AS DECIMAL) DESC';
        return $wpdb->get_row($sql, OBJECT, 0);//Last in result
    }
}