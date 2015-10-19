<?php

/**
 *
 * This file is part of project 
 *
 * File name : CridonTools.php
 * Project   : wp_cridon
 *
 * @author Etech
 * @contributor Fabrice MILA
 *
 */

class CridonTools {
    private $wp_posts_column;//Contains Post table column
    private $fieldPost; //used in query
    private $postFactory; // Create clean object WP_Post 
    
    public function __construct( $postFactory ) {
        $this->wp_posts_column = array();
        $this->fieldPost = null;
        $this->postFactory = $postFactory;
        $this->postFactory->setTools( $this );
        $this->getColumnNameOfPost();
    }
    /**
     * Return field of post on query
     * 
     * @return string 
     */
    public function getFieldPost(){
        if( !$this->fieldPost ){
            $this->getColumnNameOfPost();
        }
        return $this->fieldPost;
    }
    /**
     * Get post column name
     * 
     * @return array
     */
    public function getPostColumn(){
        if( !$this->wp_posts_column ){
            $this->getColumnNameOfPost();
        }
        return $this->wp_posts_column;
    }
    /**
     * Get all column name of table cri_posts
     * @global type $wpdb
     */
    private function getColumnNameOfPost(){
        global $wpdb;       
        $this->fieldPost = '';
        $table_name = $wpdb->prefix . 'posts';
        foreach ( $wpdb->get_col( "DESC " . $table_name, 0 ) as $column_name ) {
            $this->wp_posts_column[] = $column_name;
            $this->fieldPost .= 'p.'.$column_name.','; 
        }        
    }
    /**
     * Split array
     * 
     * @param array $data
     * @param string $attr
     * @return array
     */
    private function splitArray( $data,$attr ){
        $aSplit = array();
        $tmp = array();
        $tmpDate = ( empty( $data ) ) ? null : $data[0]->$attr;
        foreach ( $data as $key=>$value ){
            if( $tmpDate == $value->$attr ){//Si la date est toujours les mêmes alors stocker la valeur
                $tmp[] = $value;
                $tmpDate = $value->$attr;
            }else{
                $aSplit[] = $tmp;
                $tmp = array();
                $tmp[] = $value;
                $tmpDate = $value->$attr;// l'itération courante
            }
            if( count( $data ) - 1 === $key ){//Si nous arrivons déjà à la fin
                $aSplit[] = $tmp;
            }
        }
        return $aSplit;
    }

    /**
     * Build new data with under array
     * 
     * @param string $model Model name
     * @param array $data Results of query
     * @param string $attr correspond in date in this context
     * @param integer $nb_per_date Number of objects in date
     * @param string $index Index of array who contain objects
     * @param string $format_date Date format of date
     * @param array $attributes Old attributes in the result
     * @param array $newAttributes New attributes to return
     * @return array
     */
    public function buildSubArray( $model,$data,$attr,$nb_per_date,$index,$format_date,$attributes = null,$newAttributes = null ){
        $newData = array();     
        $aSplit = $this->splitArray( $data,$attr );//Reconstruit le tableau en ayant plusieurs petits tableaux contenant les mêmes dates
 
        foreach( $aSplit as $val ){
            $count_per_date = 1;
            $tmpRes = array();
            $tmpNews = array();
            foreach( $val as $k1=>$v1){
                if( $count_per_date <= $nb_per_date ){//Si le nombre d'éléments n'atteint pas encore les limites autorisés
                    $date = new DateTime( $v1->$attr );
                    $tmpRes['date'] = $date->format( $format_date );//Formater la date au format voulu
                    $cls = new stdClass();
                    if( $attributes ){
                        foreach ( $attributes as $k2=>$v2 ){//Recréer les attributs avec celui les nouveaux customisés
                            $cls->$newAttributes[$k2] = $v1->$v2;
                        }                        
                    }
                    $cls->link = CridonPostUrl::generatePostUrl( $model, $v1->join_id );//Obtenir le lien de l'article
                    /* object WP_Post*/
                    $cls->post = $this->postFactory->create( $v1 );
                    /**/
                    $option['id'] = $v1->join_id;
                    $tmpNews[] = $cls;
                    if( count( $val ) - 1 === $k1 ){// Si nous sommes déjà à la fin faire un push dans le tableau final
                        $tmpRes[$index] = $tmpNews;
                        $newData[] = $tmpRes;
                    }
                }else{//Si le nombre d'objet est atteint alors mettre dans le tableau final
                    $tmpRes[$index] = $tmpNews;
                    $newData[] = $tmpRes;
                    break;
                }
                $count_per_date++;				
            }
        }
        return $newData;
    }
    
    /**
     * Create clean object WP_Post
     * 
     * @param object $object Represent stdClass
     * @return \WP_Post
     */
    public function createPost( $object ){
        $this->postFactory->setTools( $this );
        return $this->postFactory->create( $object );
    }
}

