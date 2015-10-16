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
            if( $tmpDate == $value->$attr ){
                $tmp[] = $value;
                $tmpDate = $value->$attr;
            }else{
                $aSplit[] = $tmp;
                $tmp = array();
                $tmp[] = $value;
                $tmpDate = $value->$attr;
            }
            if( count( $data ) - 1 === $key ){
                $aSplit[] = $tmp;
            }
        }
        return $aSplit;
    }

    /**
     * Build new data with under array
     * 
     * @param type $model Model name
     * @param type $data Results of query
     * @param type $attr correspond in date in this context
     * @param type $attributes Old attributes in the result
     * @param type $newAttributes New attributes to return
     * @param type $nb_per_date Number of objects in date
     * @param type $index Index of array who contain objects
     * @param type $format_date Date format of date
     * @return array
     */
    public function buildSubArray( $model,$data,$attr,$attributes,$newAttributes,$nb_per_date,$index,$format_date ){
        $newData = array();     
        $aSplit = $this->splitArray( $data,$attr );
        $option = array(
            'controller' => $model.'s',
            'action'     => 'show'
        );
        foreach( $aSplit as $val ){
            $count_per_date = 1;
            $tmpRes = array();
            $tmpNews = array();
            foreach( $val as $k1=>$v1){
                if( $count_per_date <= $nb_per_date ){
                    $date = new DateTime( $v1->$attr );
                    $tmpRes['date'] = $date->format( $format_date );
                    $cls = new stdClass();
                    foreach ( $attributes as $k2=>$v2 ){
                        $cls->$newAttributes[$k2] = $v1->$v2;
                    }
                    $option['id'] = $v1->join_id;
                    $cls->link = MvcRouter::public_url($option);
                    $tmpNews[] = $cls;
                    if( count( $val ) - 1 === $k1 ){
                        $tmpRes[$index] = $tmpNews;
                        $newData[] = $tmpRes;
                    }
                }else{
                    $tmpRes[$index] = $tmpNews;
                    $newData[] = $tmpRes;
                    break;
                }
                $count_per_date++;				
            }
        }
        return $newData;
    }
}

