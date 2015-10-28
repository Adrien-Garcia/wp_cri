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
    private $postFactory; // Create clean object WP_Post 
    private $postStructure; // Retrieve structure of table wp_posts 
    
    public function __construct( $postFactory,$postStructure ) {        
        $this->postFactory = $postFactory;
        $this->postStructure = $postStructure;
    }
    /**
     * Return field of post on query
     * 
     * @return string 
     */
    public function getFieldPost(){
        return $this->postStructure->getFieldPost();
    }
    /**
     * Get post column name
     * 
     * @return array
     */
    public function getPostColumn(){
        return $this->postStructure->getPostColumn();
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
            if( count( $data ) - 1 === $key ){ // Si nous arrivons déjà à la fin
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
        $option = array(
            'controller' => $model.'s',
            'action'     => 'show'
        );
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
                            // Nous avons la correspondance anciens attributs=>array('matiere')
                            // et nouveaux attributs => array('matiere'=>$fields)
                            if( is_array( $newAttributes[$v2] ) ){
                                $cls->$v2 = CridonObjectFactory::create( $v1, $v2, $newAttributes[$v2] );
                            }else{
                                $cls->$newAttributes[$k2] = $v1->$v2;                                
                            }
                        }                        
                    }
                    $cls->link = CridonPostUrl::generatePostUrl( $model, $v1->join_id );//Obtenir le lien de l'article
                    $option['id'] = $v1->join_id;
                    /* object WP_Post*/
                    $cls->post = $this->postFactory->create( $v1 );
                    /**/
                    $cls->link = MvcRouter::public_url($option);
                    $tmpNews[] = $cls;
                    if( count( $val ) - 1 === $k1 ){// Si nous sommes déjà à la fin faire un push dans le tableau final
                        $tmpRes[$index] = $tmpNews;
                        $newData[] = $tmpRes;
                    }
                }else{ //Si le nombre d'objet est atteint alors mettre dans le tableau final
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
        return $this->postFactory->create( $object );
    }
}

