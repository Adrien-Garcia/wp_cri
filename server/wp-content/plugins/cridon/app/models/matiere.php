<?php

class Matiere extends MvcModel
{
    var $display_field  = 'label';
    var $table          = '{prefix}matiere';
    var $has_many       = array(
        'Competence' => array(
            'foreign_key' => 'code_matiere'
        ),
        'Matiere' => array(
            'foreign_key' => 'id_matiere'
        )
    );
    public function create($data) {
        $path = $this->upload();
        if( $path ){
            $data['Matiere']['picto'] = $path;
        }
        return parent::create($data);
    }
    public function save($data) {
        $path = $this->upload( $data );
        if( $path ){
            $data['Matiere']['picto'] = $path;
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
                if( !file_exists( $path )){//not yet directory
                    mkdir( $path, 775 ,true);
                }
                $file = $_FILES['data']['name']['Matiere']['picto'];
                if( file_exists( $path.$file ) ){//if file is already exist
                    $file = mt_rand(1, 10).'_'.$_FILES['data']['name']['Matiere']['picto'];
                }
                //moving file
                if( move_uploaded_file( $_FILES['data']['tmp_name']['Matiere']['picto'], $path.$file ) ){
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
    
}