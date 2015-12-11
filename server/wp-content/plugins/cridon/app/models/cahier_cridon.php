<?php

/**
 * Class CahierCridon
 */

require_once 'base_model.php';

class CahierCridon extends BaseModel
{
    var $table = "{prefix}cahier_cridon";
    var $includes = array('Post');
    var $belongs_to = array(
        'Post'      => array('foreign_key' => 'post_id'),
        'Matiere'   => array('foreign_key' => 'id_matiere'),
        'principal' => array('foreign_key' => 'id_parent'),
    );
    var $has_many = array(
        'secondaires' => array(
            'foreign_key' => 'id_parent'
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
                        'post_parent' => (strtolower($data[$parser::CAHIER_PARENTID]) != 'null')?$data[$parser::CAHIER_PARENTID]:0,
                    );
                    $post_ID = wp_insert_post($post, true);

                    // pas d'erreur
                    if (!is_wp_error($post_ID)) {
                        $cahierData = array(
                            'CahierCridon' => array(
                                'id'      => $data[$parser::CAHIER_ID],
                                'post_id' => $post_ID,
                                'id_parent' => $post['post_parent'],
                                'id_matiere'      => $data[$parser::CAHIER_MATIEREID],
                            )
                        );

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
}