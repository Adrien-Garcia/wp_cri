<?php

require_once 'base_model.php';

class VieCridon extends BaseModel {
    var $table          = "{prefix}vie_cridon";
    var $includes       = array('Post');
    var $belongs_to     = array(
        'Post' => array('foreign_key' => 'post_id')
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
     * Récupération des documents d'une viecridon
     *
     * @param integer $id Id de la viecridon
     * @return mixed
     */
    public static function getDocuments($id){
        $options = array(
            'conditions' => array(
                'type' => 'viecridon',//type de document
                'id_externe' => $id //id de la viecridon
            )
        );
        return mvc_model('Document')->find($options);
    }
}

?>