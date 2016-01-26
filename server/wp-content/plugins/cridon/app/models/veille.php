<?php

class Veille extends \App\Override\Model\CridonMvcModel {

    var $table          = "{prefix}veille";
    var $includes       = array('Post','Matiere');
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
     * Récupération des documents d'une veille
     * 
     * @param integer $id Id de la veille
     * @return mixed
     */
    public static function getDocuments($id){
        $options = array(
            'conditions' => array(
                'type' => 'veille',//type de document
                'id_externe' => $id //id de la veille
            )
        );
        return mvc_model('Document')->find($options);
    }

    public function getList($params){
        $params['per_page'] = !empty($params['per_page']) ? $params['per_page'] : DEFAULT_POST_PER_PAGE;
        //Set explicit join
        $params['joins'] = array(
            'Post','Matiere'
        );
        //Set conditions
        if (isset($params['conditions']) && is_array($params['conditions'])) {
            $params['conditions'] = array_merge($params['conditions'], array(
                'Post.post_status' => 'publish'
            ));
        };
        //Order by date publish
        $params['order'] = 'Post.post_date DESC' ;

        /** @var $this->model veille  */
        return Veille::paginate($params);
    }
}

?>