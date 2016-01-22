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
                    $cls->link = CridonPostUrl::generatePostUrl( $model, $v1->post_name );//Obtenir le lien de l'article
                    /* object WP_Post*/
                    $cls->post = $this->postFactory->create( $v1 );
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

    /**
     * Send email for error reporting
     *
     * @param string $message
     * @param string $object
     */
    public static function reportError($message, $object)
    {
        // message content
        $message =  sprintf($message, $object);
        $env = getenv('ENV');
        //define receivers
        if ((empty($env) || ($env !== 'PROD')) && !empty(Config::$emailNotificationError['cc'])) {
            // just send to client in production mode
            $ccs = (array) Config::$emailNotificationError['cc']; //cast to guarantee array
            $to = array_pop($ccs);
        } else {
            $to = arrayGet(Config::$emailNotificationError, 'to', CONST_EMAIL_ERROR_CONTACT);
        }
        $headers = array();
        if (!empty(Config::$emailNotificationError['cc'])) {
            foreach ((array) Config::$emailNotificationError['cc'] as $cc) {
                $headers[] = 'Cc: '.$cc;
            }
        }

        // send email
        wp_mail($to, CONST_EMAIL_ERROR_SUBJECT, $message, $headers);
    }

    /**
     * Check if user connected is notary
     *
     * @return bool
     */
    public function isNotary()
    {
        global $current_user, $wpdb;

        if (is_object($current_user) && property_exists($current_user, 'ID')) {
            $query = " SELECT id FROM {$wpdb->prefix}notaire WHERE id_wp_user = {$current_user->ID} LIMIT 1 ";

            $notary = $wpdb->get_row($query);

            if (is_object($notary) && $notary->id) {
                return true;
            }
        }

        return false;
    }
}

