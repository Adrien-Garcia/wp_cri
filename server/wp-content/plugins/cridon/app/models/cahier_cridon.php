<?php

/**
 * Class CahierCridon
 */


class CahierCridon extends \App\Override\Model\CridonMvcModel
{
    var $table = "{prefix}cahier_cridon";
    var $includes = array('Post', 'CahierCridon');
    var $belongs_to = array(
        'Post'      => array('foreign_key' => 'post_id'),
        'Matiere'   => array('foreign_key' => 'id_matiere'),
        'CahierCridon' => array('foreign_key' => 'id_parent'),
    );
    var $has_many = array(
        'CahierCridon' => array(
            'foreign_key' => 'id'
        )
    );
    var $display_field = 'post_id';

    public function delete($id)
    {
        $qb    = new QueryBuilder();
        $model = $qb->find(array(
                               'attributes' => array('id,post_id'),
                               'model'      => $this->name,
                               'conditions' => 'id = ' . $id
                           ));
        if (!empty($model)) {
            if ($model[0]->post_id != null) {
                //Delete post
                $qb->deletePost($model[0]->post_id);
            }
        }
        //Delete document
        $qb->deleteDocument($this, $id);
        parent::delete($id);
    }

    public function importIntoSite()
    {
        // wp query builder
        global $wpdb;

        // recupere tous les fichiers
        $documents = glob(CONST_IMPORT_CAHIER_PATH . '/*.csv');

        $parser = new CridonCahierParser();

        $parser->enclosure = '';
        $parser->encoding(null, 'UTF-8');
        $parser->auto($documents[0]);
        // Csv data
        $datas = $parser->data;

        if (is_array($datas) && count($datas) > 0) {
            foreach ($datas as $data) {
                if (isset($data[$parser::CAHIER_ID]) && intval($data[$parser::CAHIER_ID]) > 0) {
                    // conversion date au format mysql
                    $postDate = '0000-00-00 00:00:00';
                    if (isset($data[$parser::CAHIER_DATE])) {
                        if (preg_match("/^(\d+)\/(\d+)\/(\d+)$/", $data[$parser::CAHIER_DATE])) {
                            $dateTime = date_create_from_format('d/m/Y', $data[$parser::CAHIER_DATE]);
                            $postDate = $dateTime->format('Y-m-d');
                        }
                    }
                    $post = array(
                        'post_title' => $data[$parser::CAHIER_TITLE],
                        'post_date' => $postDate,
                        'post_date_gmt' => get_gmt_from_date($postDate),
                        'post_status' => 'publish',
                        'post_type' => 'post',
                        'post_author' => 3,
                        'post_parent' => (strtolower($data[$parser::CAHIER_PARENTID]) != 'null')? $data[$parser::CAHIER_PARENTID] : 0,
                    );
                    $post_ID = wp_insert_post($post, true);

                    // pas d'erreur
                    if (!is_wp_error($post_ID)) {
                        $cahierData = array(
                            'CahierCridon' => array(
                                'id'      => $data[$parser::CAHIER_ID],
                                'post_id' => $post_ID,
                                'id_matiere'      => $data[$parser::CAHIER_MATIEREID],
                            )
                        );
                        if (!empty($post['post_parent'])) {
                            $cahierData['CahierCridon']['id_parent'] = $post['post_parent'];
                        }
                        // id insertion
                        $id = $this->create($cahierData);

                        // doc associée
                        $file = CriRecursiveFindingFileInDirectory(CONST_IMPORT_CAHIER_PATH, $data[$parser::CAHIER_PDF]);
                        if ($file) {
                            $destinationDir = mvc_model('Document')->getUploadDir();
                            if (!file_exists($destinationDir)) { // repertoire manquant
                                // creation du nouveau repertoire
                                wp_mkdir_p($destinationDir);
                            }
                            if (@copy($file, $destinationDir . '/' . $data[$parser::CAHIER_PDF])) {
                                $date       = new DateTime('now');
                                $filePath   = '/documents/' . $date->format('Ym');
                                $docData = array();
                                $docData['Document']['type'] = strtolower($this->name);
                                $docData['Document']['file_path'] = $filePath . '/' . $data[$parser::CAHIER_PDF];
                                $docData['Document']['id_externe'] = $id;

                                // insertion document
                                mvc_model('document')->insertDoc($docData);
                            }
                        }
                    }
                }
            }
        }
    }
    /**
     * Récupération des documents d'une cahiercridon
     *
     * @param integer $id Id de la cahiercridon
     * @return mixed
     */
    public static function getDocuments($id){
        $options = array(
            'conditions' => array(
                'type' => 'cahiercridon',//type de document
                'id_externe' => $id //id de la cahier cridon
            )
        );
        return mvc_model('Document')->find($options);
    }

    /**
     * Overrice MvcModel::paginate
     *
     * @param array $options
     * @return array
     */
    public function paginate($options=array()){
        global $wpdb;

        $options['page'] = empty($options['page']) ? 1 : intval($options['page']);
        $options['per_page'] = empty($options['per_page']) ? $this->per_page : intval($options['per_page']);
        $limit = '';
        if( $options['per_page'] ){
            //put in query the limit
            $limit = $this->db_adapter->get_limit_sql($options);
        }

        // default query
        $query = 'SELECT c, ca, p, pca FROM
                    (
                        SELECT `c`.* FROM ' . $this->name . ' c
                        LEFT JOIN Post cp ON c.`post_id` = `cp`.`ID`
                        WHERE c.`id_parent` IS NULL
                        ORDER BY cp.`post_date`desc
                        ' . $limit . '
                    ) [' . $this->name . '] c
                    LEFT JOIN Post p ON c.`post_id` = `p`.`ID`
                    LEFT JOIN ' . $this->name . ' ca ON ca.`id_parent` = c.id
                    LEFT JOIN Post pca ON ca.`post_id` = `pca`.`ID`
                    ORDER BY p.`post_date` desc'
        ;

        $q =  new \App\Override\Model\QueryStringModel($query);

        // Total query for pagination
        $query_count ='
                SELECT COUNT(*) FROM ' . $this->table . ' c
                WHERE c.`id_parent` IS NULL';

        $total_count = $wpdb->get_var($query_count);

        // admin query
        if (is_admin()) {
            $where = '';
            if(isset($options['conditions'])){
                $where = $this->getWhere($options);
            }
            $query = 'SELECT c, ca, cp FROM
                    (
                        SELECT `c`.* FROM ' . $this->name . ' c
                        LEFT JOIN Post p ON c.`post_id` = `p`.`ID`
                        LEFT JOIN Matiere m ON m.`id` = `c`.`id_matiere`
                        ' . $where . '
                        ' . $limit . '
                    ) [' . $this->name . '] c
                    LEFT JOIN Post cp ON c.`post_id` = `cp`.`ID`
                    LEFT JOIN ' . $this->name . ' ca ON ca.`id_parent` = c.id';

            // Total query for pagination
            $query_count ='
                SELECT COUNT(*) AS count  FROM ' . $this->table . ' c
                        LEFT JOIN ' . $wpdb->posts . ' p ON c.`post_id` = `p`.`ID`
                        LEFT JOIN ' . $wpdb->prefix . 'matiere m ON m.`id` = `c`.`id_matiere`
                        ' . $where ;
            $q =  new \App\Override\Model\QueryStringModel($query);
            $total_count = $wpdb->get_var($query_count);
        }

        // custom options
        $opt = array(
            'CahierCridon' => array(
                'foreign_key' => 'id_parent'
            )
        );
        $objects = $this->getResults($q);
        $objects = $q->processAppendChild($objects, $opt);
        $response = array(
            'objects' => $objects,
            'total_objects' => $total_count,
            'total_pages' => ceil($total_count/$options['per_page']),
            'page' => $options['page']
        );
        return $response;
    }

    /**
     * Get parent and childs for a given parent id
     * @param int $id_parent
     * @return object $response
     */
    public function get_parent_and_childs($id_parent){
        $parent = $this->get_parent($id_parent);
        $childs = $this->get_childs($id_parent);

        $response = array(
            'parent' => $parent,
            'childs' => $childs
        );
        return $response;
    }

    protected function get_childs($id_parent){
        //We place the cahier_cridon fields after the other to keep the id ; otherwise, it's replaced by the matiere id.
        $options ['fields'] = 'm.*,p.*,c.*';
        $options ['join'] = array(
            'matiere' => array(
                'table' => 'matiere m',
                'column' => ' c.id_matiere = m.id'
            ),
            'post' => array(
                'table' => 'posts p',
                'column' => ' c.post_id = p.id'
            )
        );
        $options ['conditions'] = 'c.id_parent = '.$id_parent;
        $options ['synonym'] = 'c';
        return mvc_model('QueryBuilder')->findAll('cahier_cridon', $options, 'c.id');
    }

    protected function get_parent($id_parent){
        $options ['join'] = array(
            'post' => array(
                'table' => 'posts p',
                'column' => ' c.post_id = p.id'
            )
        );
        $options ['conditions'] = 'c.id = '.$id_parent;
        $options ['synonym'] = 'c';
        return mvc_model('QueryBuilder')->findAll('cahier_cridon', $options, 'c.id');
    }

    /**
     * Get results
     *
     * @param mixed $q
     * @return array
     */
    protected function getResults($q)
    {
        global $wpdb;

        $datas = $wpdb->get_results( $q->getQueryParser()->getQuery() );
        $results = $this->processDatas($q, $datas);
        return $results;
    }

    /**
     * Process data
     *
     * @param mixed $q
     * @param mixed $datas
     * @return array
     */
    protected function processDatas($q, $datas)
    {
        $new_datas = array();

        //browse result
        foreach( $datas as $data ){
            $new_datas[] = $this->newObject($q, $data);
        }
        return $new_datas;
    }

    /**
     * Fetching data in array
     *
     * @param mixed $q
     * @param mixed $data
     * @return MvcModelObject
     */
    protected function newObject($q, $data){
        $from = $q->getQueryParser()->getQueryFrom();
        $pObj = new \MvcModelObject( $from );//Model in FROM
        $pObj->mvc_model = clone $from;//clone to get exactly same object
        //get selected model query
        $models = $q->getQueryParser()->getSelectedModel();
        //Browse
        foreach( $models as $model ) {
            $iterator = 0;//iterator for alias

            $cObj            = new \MvcModelObject($model['model']);//construct model object
            $cObj->mvc_model = clone $model['model'];//clone to get exactly same object

            //retreive from $data field of the table (model)
            foreach ($model['model']->schema as $field => $val) {
                //only for current model
                if ($from->name === $model['model']->name) {
                    //Put in parent object (in FROM) his field
                    if (property_exists($pObj, $field)) {
                        $cObj->$field = isset($data->{$model['alias'] . $iterator}) ? $data->{$model['alias'] . $iterator} : null;
                        if( $field === $model['model']->primary_key){
                            $cObj->__id = $data->{$model['alias'].$iterator};
                        }
                        if( $field === $model['model']->display_field){
                            $cObj->__name = $data->{$model['alias'].$iterator};
                        }
                        $cObj->mvc_model->$field = isset($data->{$model['alias'] . $iterator}) ? $data->{$model['alias'] . $iterator} : null;
                    } else {
                        $pObj->$field            = isset($data->{$model['alias'] . $iterator}) ? $data->{$model['alias'] . $iterator} : null;

                        if( $field === $model['model']->primary_key){
                            $pObj->__id = $data->{$model['alias'].$iterator};
                        }
                        if( $field === $model['model']->display_field){
                            $pObj->__name = $data->{$model['alias'].$iterator};
                        }
                        $pObj->mvc_model->$field = isset($data->{$model['alias'] . $iterator}) ? $data->{$model['alias'] . $iterator} : null;
                    }
                } else {
                    $cObj->mvc_model->$field = isset($data->{$model['alias'] . $iterator}) ? $data->{$model['alias'] . $iterator} : null;
                    $cObj->$field            = isset($data->{$model['alias'] . $iterator}) ? $data->{$model['alias'] . $iterator} : null;
                    $cObj->__model_alias     = $model['alias'];

                    if( $field === $model['model']->primary_key){
                        $cObj->__id = $data->{$model['alias'].$iterator};
                    }
                    if( $field === $model['model']->display_field){
                        $cObj->__name = $data->{$model['alias'].$iterator};
                    }
                }
                $iterator++;
            }
            if ($from->name !== $model['model']->name) { // normal join
                //name used for attribute
                $name = strtolower($model['model']->name);
                if ($model['alias'] == 'p') {
                    $pObj->$name = $cObj;
                }
            } else { // self join
                $name = \MvcInflector::tableize($model['model']->name);

                $pObj->$name = array($cObj);
            }
        }

        // check if child exist and append associated post model
        if (property_exists($pObj, 'cahier_cridons')) {
            $tItems = $pObj->cahier_cridons;
            if (property_exists($cObj, '__model_alias')
                && $cObj->__model_alias == 'pca' // post cahier_cridons
                && is_array($tItems)
                && isset($tItems[0])
                && $tItems[0]->post_id == $cObj->ID
            ) {
                $tItems[0]->post = $cObj;
            }
        }

        return $pObj;
    }
}
