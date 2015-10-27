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
                if( $count_per_date <= $nb_per_date ){ //Si le nombre d'éléments n'atteint pas encore les limites autorisés
                    $date = new DateTime( $v1->$attr );
                    $tmpRes['date'] = $date->format( $format_date );//Formater la date au format voulu
                    $cls = new stdClass();
                    foreach ( $attributes as $k2=>$v2 ){ //Recréer les attributs avec celui les nouveaux customisés
                        $cls->$newAttributes[$k2] = $v1->$v2;
                    }
                    $option['id'] = $v1->join_id;
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
     * Check if user exist
     *
     * @param string $userLogin
     * @return array|null|object|void
     */
    public function isUserExist($userLogin)
    {
        global $wpdb;

        $sql = "SELECT `ID` FROM `{$wpdb->users}`
                WHERE `user_login` = %s";

        return $wpdb->get_row($wpdb->prepare($sql, $userLogin));
    }

    /**
     * Get list of existing users (optimized query)
     *
     * @return array
     */
    public function getWpUsers()
    {
        global $wpdb;

        // init data
        $users             = array();
        $users['id']       = array();
        $users['username'] = array();

        // query
        $sql = "SELECT `ID`, `user_login` FROM `{$wpdb->users}`";

        foreach ($wpdb->get_results($sql) as $items) {
            $users['id'][]       = $items->ID;
            $users['username'][] = $items->user_login;
        }

        return $users;
    }

    /**
     * Get list of existing roles (optimized query)
     *
     * @return array
     */
    public function getExistingUserRoles()
    {
        global $wpdb;

        // init data
        $users             = array();

        // query
        $sql = "SELECT DISTINCT `user_id` FROM `{$wpdb->usermeta}`
                WHERE `meta_key` = '{$wpdb->prefix}capabilities'";

        foreach ($wpdb->get_results($sql) as $items) {
            $users[] = $items->user_id;
        }

        return $users;
    }
}

