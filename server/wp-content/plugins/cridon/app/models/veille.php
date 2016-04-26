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
        } else {
            $params['conditions'] = array('Post.post_status' => 'publish');
        };
        //Order by date publish
        $params['order'] = 'Post.post_date DESC' ;

        /** @var $this->model veille  */
        return Veille::paginate($params);
    }

    /**
     * Check if user can access content of page
     *
     * @param mixed $object
     * @return bool
     * @throws Exception
     */
    public function userCanAccessSingle($object)
    {
        if (isset($object->params['id']) && $object->params['id']) {
            // notary data
            $notaryData = mvc_model('Notaire')->getUserConnectedData();
            // veilles data
            $veille     = $this->associatePostWithDocumentByPostName($object->params['id']);
            // get user cap level
            $capLevel    = 'access_level_' . $veille->level;
            // subscription_level must be >= veille_level
            if (!in_array($notaryData->category, Config::$notaryNoDefaultOffice)
                && ($notaryData->etude->subscription_level >= $veille->level || $notaryData->etude->end_subscription_date_veille >= date('Y-m-d'))
            ) { // Categ OFF
                return (current_user_can($capLevel));
            } else { // Categ DIV or ORG
                return (current_user_can($capLevel));
            }
        } else {
            return false;
        }
    }
}