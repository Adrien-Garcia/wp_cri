<?php

class Matiere extends \App\Override\Model\CridonMvcModel
{
    var $display_field  = 'label';
    var $table          = '{prefix}matiere';
    var $has_many       = array(
        'Competence' => array(
            'foreign_key' => 'code'
        ),
        'Veille' => array(
            'foreign_key' => 'id'
        ),
        'Flash' => array(
            'foreign_key' => 'id'
        ),
        'CahierCridon' => array(
            'foreign_key' => 'id'
        )
    );
    public function create($data) {
        $path = $this->upload();
        if( $path ){
            $data['Matiere']['picto'] = $path;
        }
        if(!empty($data['Matiere']['label'])){
            $data['Matiere']['virtual_name'] = sanitize_title($data['Matiere']['label']);
        }
        $id = parent::create($data);
        if( $id && (intval($data['Matiere']['question']) === 1) ){            
            $competence = mvc_model('Competence');
            $opt = array(
                'Competence' => array(
                    'id'            => $data['Matiere']['code'],
                    'label'         => $data['Matiere']['label'],
                    'short_label'   => $data['Matiere']['short_label'],
                    'code_matiere'  => $data['Matiere']['code']
                )
            );
            $competence->create($opt);
        }
        return $id;
    }
    public function save($data) {
        $path = $this->upload( $data );
        if( $path ){
            $data['Matiere']['picto'] = $path;
        }
        if(!empty($data['Matiere']['label'])){
            $data['Matiere']['virtual_name'] = sanitize_title($data['Matiere']['label']);
        }
        return parent::save($data);
    }
    /**
     * Function to upload image 
     * 
     * @param array $data Contains data of model
     * @return string|null
     */
    private function upload( $data = array() ){
        if( isset( $_FILES ) ){                
            $arr_file_type = wp_check_filetype( $_FILES['data']['name']['Matiere']['picto'] );
            $uploaded_type = $arr_file_type['type'];
            if( in_array( $uploaded_type, Config::$supported_types ) ){// check correct image
                if( !$this->checkDimension( $_FILES['data']['tmp_name']['Matiere']['picto'] ) ){
                    return null;
                }
                $upload_dir = wp_upload_dir();// the current upload directory
                $root = $upload_dir['basedir'];
                $path = $root . '/matieres/picto/';//Upload directory of image
                $isDirectoryExist = true;// Directory is already exist.
                if( !file_exists( $path )){//not yet directory
                    $isDirectoryExist = wp_mkdir_p($path);
                }
                $file = $_FILES['data']['name']['Matiere']['picto'];
                if( $isDirectoryExist && file_exists( $path.$file ) ){//if file is already exist
                    $file = mt_rand(1, 10).'_'.$_FILES['data']['name']['Matiere']['picto'];
                }
                //moving file
                if( $isDirectoryExist && move_uploaded_file( $_FILES['data']['tmp_name']['Matiere']['picto'], $path.$file ) ){
                    if( !empty( $data ) ){
                        $obj = $this->find_by_id( $data['Matiere']['id'] );
                        $picto = $obj->picto;
                        if( $picto ){
                            $tmp = explode( $upload_dir['baseurl'].'/matieres/picto/',$picto );
                            if( ( count($tmp) > 1 ) && ( $tmp[1] !== $file ) ){//remove old image
                                if( file_exists( $path.$tmp[1] ) ){
                                    unlink( $path.$tmp[1] );//erase                                    
                                }
                            }                            
                        }
                    }
                    //return URL of image
                    return $upload_dir['baseurl'].'/matieres/picto/'.$file;
                }
                
            }
        }
        return null;
    }
    
    private function checkDimension( $file ){
        list($width, $height) = getimagesize( $file );
        return ( ( Config::$maxWidthHeight['width'] < $width ) || ( Config::$maxWidthHeight['height'] < $height ) ) ? false : true;
    }
    
    
    public static function getMatieresByModelPost($model){
        global $wpdb;
        $sql = "
            SELECT m.* FROM {$wpdb->prefix}matiere m
            LEFT JOIN {$model->table} j ON m.id = j.id_matiere
            LEFT JOIN {$wpdb->prefix}posts p ON p.id = j.post_id
            WHERE j.id IS NOT NULL
            AND p.post_status = 'publish'
            GROUP BY m.id
        ";
        return $wpdb->get_results($sql);
    }
    
    public function getMatieresByNotaireQuestionAnswered(){
        global $wpdb;
        $notaire = CriNotaireData();
        $matieres = array();
        if(!$notaire){
            return $matieres;
        }
        $sql = "
            SELECT m.id,m.code,m.label,m.virtual_name FROM {$wpdb->prefix}matiere m
            JOIN {$wpdb->prefix}competence c ON m.code = c.code_matiere
            LEFT JOIN {$wpdb->prefix}question q ON q.id_competence_1 = c.id
            LEFT JOIN {$wpdb->prefix}notaire AS n ON q.client_number = n.client_number
            LEFT JOIN {$wpdb->prefix}entite AS e ON e.crpcen = n.crpcen
            WHERE e.crpcen = \"{$notaire->crpcen}\"
            AND q.id_affectation = " .CONST_QUEST_ANSWERED. "
            AND m.displayed = 1
            GROUP BY m.id
        ";
        $items = $wpdb->get_results($sql);
        // format output
        if (is_array($items) && count($items) > 0) {
            foreach ($items as $item) {
                $matieres[$item->id]['label'] = $item->label;
                $matieres[$item->id]['code'] = $item->code;
                $matieres[$item->id]['virtual_name'] = $item->virtual_name;
            }
        }
        return $matieres;
    }
}
