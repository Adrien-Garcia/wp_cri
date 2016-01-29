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
     * Push notification
     *
     * @param string    $type
     * @param mixed     $registrationIds
     * @param string    $message
     * @param int       $questionId
     * @return int
     */
    public function pushNotification($type, $registrationIds, $message, $questionId)
    {
        try {
            switch (strtolower($type)) { // check device type
                case 'ios':
                    // init response
                    $response = 0;

                    $registration_ids = '';
                    // check registrationId type
                    // to be converted in array if is a string (required by the API)
                    if (is_array($registrationIds)) {
                        $registration_ids = $registrationIds;
                    } elseif ($registrationIds) {
                        // @todo may be changed by pushToken validator if necessary (no doc for PHP at the moment)
                        $registration_ids = array(str_replace(' ', '', $registrationIds));
                    }
                    // APNS params
                    $badge = 1 ;
                    $sound = 'default';

                    $payload = array();
                    $payload['aps'] = array(
                        'alert' => $message,
                        'badge' => intval($badge),
                        'sound' => $sound
                    );
                    $payload = json_encode($payload);

                    // by default mode sandbox
                    $apns_url = 'gateway.sandbox.push.apple.com';
                    // APNS sandbox certificat
                    $apns_cert = CONST_APNS_SANDBOX_PEM;

                    // set server by env
                    $env = getenv('ENV');
                    if ($env === 'PROD') {
                        $apns_url  = 'gateway.push.apple.com';
                        $apns_cert = CONST_APNS_PROD_PEM;
                    }

                    if (!file_exists($apns_cert)) { // certificat introuvable
                        $error = sprintf(CONST_NOTIFICATION_ERROR, 'certificat introuvable : ' . $apns_cert);
                        writeLog($error, 'pushnotification.log');

                        // stop process
                        return $response;
                    }

                    if (is_array($registration_ids)) {
                        $stream_context = stream_context_create();
                        stream_context_set_option($stream_context, 'ssl', 'local_cert', $apns_cert);
                        stream_context_set_option($stream_context, 'ssl', 'passphrase ', CONST_APNS_PASSPHRASE);

                        $apns = stream_socket_client('ssl://' . $apns_url . ':' . CONST_APNS_PORT, $error, $error_string, 60, STREAM_CLIENT_CONNECT, $stream_context);
                        if ($apns) {
                            if ($badge > 0) { // verification badge
                                foreach ($registration_ids as $device_token) {
                                    $apns_message = chr(0) . chr(0) . chr(32) . pack('H*', str_replace(' ', '', $device_token)) . chr(0) . chr(strlen($payload)) . $payload;
                                    fwrite($apns, $apns_message);
                                }
                                @socket_close($apns);
                                @fclose($apns);

                                // no error
                                $response = 1;
                            } else { // badge invalid
                                $errorLog = sprintf(CONST_NOTIFICATION_ERROR, 'badge invalid');
                                writeLog($errorLog, 'pushnotification.log');
                            }

                        } else { // APNS not found

                            $errorLog = sprintf(CONST_NOTIFICATION_ERROR, 'impossible de se connecter au serveur APNS pour la question avec id = ' . $questionId);
                            writeLog($errorLog, 'pushnotification.log');
                        }
                    } else { // invalid pushToken
                        $errorLog = sprintf(CONST_NOTIFICATION_ERROR, 'invalid pushToken pour la question avec id = ' . $questionId);
                        writeLog($errorLog, 'pushnotification.log');
                    }

                    return $response ;

                    break;
                case 'android':
                    $msg              = array(
                        'message'  => $message,
                        'title'    => CONST_ANDROID_TITLE_MSG,
                        'subtitle' => CONST_ANDROID_SUBTITLE_MSG,
                        'vibrate'  => 1,
                        'sound'    => 'default'
                    );
                    $registration_ids = '';
                    // check registrationId type
                    // to be converted in array if is a string (required by the API)
                    if (is_array($registrationIds)) {
                        $registration_ids = $registrationIds;
                    } elseif ($registrationIds) {
                        // @todo may be changed by pushToken validator if necessary (no doc for PHP at the moment)
                        $registration_ids = array(str_replace(' ', '', $registrationIds));
                    }

                    $fields = array
                    (
                        'registration_ids' => $registration_ids,
                        'data'             => $msg
                    );

                    $headers = array
                    (
                        'Authorization: key=' . CONST_GOOGLE_API_KEY,
                        'Content-Type: application/json'
                    );

                    // init response
                    $response = 0;

                    if (is_array($registration_ids)) {
                        // Open connection
                        $ch = curl_init();

                        // Set the url, number of POST vars, POST data
                        curl_setopt( $ch, CURLOPT_URL, CONST_GOOGLE_GCM_URL );

                        curl_setopt( $ch, CURLOPT_POST, true );
                        curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
                        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

                        // Avoids problem with https certificate
                        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false);
                        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false);

                        curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $fields ) );

                        // Execute post
                        $result = curl_exec($ch);
                        if ($result === false) {
                            // error reporting
                            $error = sprintf(CONST_NOTIFICATION_ERROR, curl_error($ch));
                            reportError($error, 'Push Notification');
                        } else {
                            // no error was found
                            $response = 1;
                        }
                        // Close connection
                        curl_close($ch);
                    } else {
                        $error = sprintf(CONST_NOTIFICATION_ERROR, 'invalid pushToken pour la question avec id = ' . $questionId);
                        writeLog($error, 'pushnotification.log');
                    }

                    return $response;
                    break;

                default: // unrecognized device
                    $errorLog = sprintf(CONST_NOTIFICATION_ERROR, 'device inconnu pour la question avec id = ' . $questionId);
                    writeLog($errorLog, 'pushnotification.log');
                    break;
            }
        } catch(\Exception $e) {
            // write into logfile
            writeLog($e, 'pushnotification.log');
        }
    }
}

