<?php

class Flash extends \App\Override\Model\CridonMvcModel {
    var $table     = "{prefix}flash";
    var $includes       = array('Post', 'Matiere');
    var $belongs_to     = array(
        'Post' => array('foreign_key' => 'post_id'),
        'Matiere' => array('foreign_key' => 'id_matiere')
    );
    var $display_field = 'post_id';
    public function delete($id) {
        $qb = new QueryBuilder();
        $model = $qb->find( array( 'attributes' => array('id,post_id'), 'model' => $this->name , 'conditions' => 'id = '.$id ) );
        if( !empty( $model ) ){
            if( $model[0]->post_id != null ){
                //Delete post
                $qb->deletePost( $model[0]->post_id );
            }
        }
        //Delete document
        $qb->deleteDocument( $this , $id );
        parent::delete($id);
    }
    /**
     * Récupération des documents d'une flash
     *
     * @param integer $id Id de la flash
     * @return mixed
     */
    public static function getDocuments($id){
        $options = array(
            'conditions' => array(
                'type' => 'flash',//type de document
                'id_externe' => $id //id de la flash
            )
        );
        return mvc_model('Document')->find($options);
    }
}

?>