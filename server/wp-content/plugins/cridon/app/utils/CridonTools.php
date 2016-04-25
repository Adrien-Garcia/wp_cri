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
        global $wpdb;
        global $current_user,$lastQueryFindNotaire,$lastQueryFindNotaireData;
        if( $lastQueryFindNotaire === true ){
            //No duplicate query for current user
            $notaireData = $lastQueryFindNotaireData;
        } elseif (is_object($current_user) && property_exists($current_user, 'ID') && !empty($current_user->ID)) {
            // get notaire by id_wp_user
            $query = " SELECT id FROM {$wpdb->prefix}notaire WHERE id_wp_user = {$current_user->ID} LIMIT 1 ";
            $lastQueryFindNotaireData = $notaireData = $wpdb->get_row($query);;
            $lastQueryFindNotaire = true;
        }

        return !empty($notaireData);
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
                        'alert' => $message['message'],
                        'badge' => intval($badge),
                        'sound' => $sound
                    );
                    $payload['dest'] = $message['urlnotaire'];
                    $payload = json_encode($payload);

                    if (!file_exists(CONST_APNS_PEM)) { // certificat introuvable
                        $error = sprintf(CONST_NOTIFICATION_ERROR, 'certificat introuvable : ' . CONST_APNS_PEM);
                        writeLog($error, 'pushnotification.log');

                        // stop process
                        return $response;
                    }

                    if (is_array($registration_ids)) {
                        $stream_context = stream_context_create();
                        stream_context_set_option($stream_context, 'ssl', 'local_cert', CONST_APNS_PEM);
                        stream_context_set_option($stream_context, 'ssl', 'passphrase ', CONST_APNS_PASSPHRASE);

                        $apns = stream_socket_client('ssl://' . CONST_APNS_URL . ':' . CONST_APNS_PORT, $error, $error_string, 60, STREAM_CLIENT_CONNECT, $stream_context);
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
                        'message'  => json_encode($message),
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

                        // Set request method to POST
                        curl_setopt( $ch, CURLOPT_POST, true );
                        // Set our custom headers
                        curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
                        // Get the response back as string instead of printing it
                        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

                        // Avoids problem with https certificate
                        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false);
                        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false);

                        // Set JSON post data
                        curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $fields ) );

                        // Execute post
                        $result = curl_exec($ch);
                        if ($result === false) {
                            // error reporting
                            $errorLog = sprintf(CONST_NOTIFICATION_ERROR, curl_error($ch));
                            reportError($errorLog, 'Push Notification');
                            writeLog($errorLog, 'pushnotification.log');
                        } else {
                            // no error was found
                            $response = 1;
                        }
                        // Close connection
                        curl_close($ch);
                    } else {
                        $errorLog = sprintf(CONST_NOTIFICATION_ERROR, 'invalid pushToken pour la question avec id = ' . $questionId);
                        writeLog($errorLog, 'pushnotification.log');
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

    /*
     * Downloading file
     *
     * @param string $file
     * @param string $name
     * @param string $mime_type
     * @return resource
     */
    public function forceDownload($file, $name, $mime_type='')
    {
        if (!is_readable($file)) {
            die('File not found or inaccessible!');
        }
        $size             = filesize($file);
        $name             = rawurldecode($name);
        $known_mime_types = array(
            "pdf"  => "application/pdf",
            "txt"  => "text/plain",
            "html" => "text/html",
            "htm"  => "text/html",
            "exe"  => "application/octet-stream",
            "zip"  => "application/zip",
            "doc"  => "application/msword",
            "xls"  => "application/vnd.ms-excel",
            "ppt"  => "application/vnd.ms-powerpoint",
            "gif"  => "image/gif",
            "png"  => "image/png",
            "jpeg" => "image/jpg",
            "jpg"  => "image/jpg",
            "php"  => "text/plain"
        );
        if ($mime_type == '') {
            $file_extension = strtolower(substr(strrchr($file, "."), 1));
            if (array_key_exists($file_extension, $known_mime_types)) {
                $mime_type = $known_mime_types[$file_extension];
            } else {
                $mime_type = "application/force-download";
            };
        };
        //turn off output buffering to decrease cpu usage
        @ob_end_clean();
        // required for IE, otherwise Content-Disposition may be ignored
        if (ini_get('zlib.output_compression')) {
            ini_set('zlib.output_compression', 'Off');
        }
        $name = explode('/', $name);
        $name = $name[count($name) - 1];
        header('Content-Type: ' . $mime_type);
        header('Content-Disposition: attachment; filename="' . $name . '"');
        header("Content-Transfer-Encoding: binary");
        header('Accept-Ranges: bytes');
        /* The three lines below basically make the
        download non-cacheable */
        header("Cache-control: private");
        header('Pragma: private');
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        // multipart-download and download resuming support
        if (isset($_SERVER['HTTP_RANGE'])) {
            list($a, $range) = explode("=", $_SERVER['HTTP_RANGE'], 2);
            list($range) = explode(",", $range, 2);
            list($range, $range_end) = explode("-", $range);
            $range = intval($range);
            if (!$range_end) {
                $range_end = $size - 1;
            } else {
                $range_end = intval($range_end);
            }
            $new_length = $range_end - $range + 1;
            header("HTTP/1.1 206 Partial Content");
            header("Content-Length: $new_length");
            header("Content-Range: bytes $range-$range_end/$size");
        } else {
            $new_length = $size;
            header("Content-Length: " . $size);
        }
        /* Will output the file itself */
        $chunksize  = 1 * (1024 * 1024); //you may want to change this
        $bytes_send = 0;
        if ($file = fopen($file, 'r')) {
            if (isset($_SERVER['HTTP_RANGE'])) {
                fseek($file, $range);
            }

            while (!feof($file) && (!connection_aborted()) && ($bytes_send < $new_length)) {
                $buffer = fread($file, $chunksize);
                print($buffer);
                flush();
                $bytes_send += strlen($buffer);
            }
            fclose($file);
        } else {
            //If no permissiion
            die('Error - can not open file.');
        }
        die();
    }

    /**
     * Cleaning data
     *
     * @param mixed $data
     * @return mixed
     */
    public function clean( $data )
    {
        $clean_input = Array();
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $clean_input[$k] = $this->clean($v);
            }
        } else {
            $clean_input = trim(strip_tags($data));
        }

        return $clean_input;
    }

    /**
     * Renvoie une / toutes fonction(s) collaborateur
     *
     * @return array|null|object
     */
    public function getFunctionCollaborator()
    {
        global $wpdb;

        $sql = " SELECT f.`id` as `id_fonction`,f.`label` as `notaire_fonction_label`,fc.`id` as `id_fonction_collaborateur`,fc.`label` as `collaborateur_fonction_label`
        FROM `{$wpdb->prefix}fonction` f
        LEFT JOIN ( SELECT * FROM `{$wpdb->prefix}fonction_collaborateur` WHERE displayed = ".CONST_DISPLAYED." ) fc ON f.`id` = fc.`id_fonction_notaire`
        WHERE f.`displayed` =".CONST_DISPLAYED."
        AND f.`id` in (  ".CONST_NOTAIRE_SALARIE."
                        ,".CONST_NOTAIRE_SALARIEE."
                        ,".CONST_NOTAIRE_COLLABORATEUR.")";


        return $wpdb->get_results($sql);
    }

    /**
     * Redirect by escaping header already send by...
     * useful outside the mvc_controller block
     *
     * @see MvcController::redirect
     * @param string $location the url to be redirected
     * @param int    $status
     */
    public function redirect($location = '', $status=302)
    {
        // MvcDispatcher::dispatch() doesn't run until after the WP has already begun to print out HTML, unfortunately, so
        // this will almost always be done with JS instead of wp_redirect().
        if (headers_sent()) {
            $html = '
                <script type="text/javascript">
                    window.location = "'.$location.'";
                </script>';
            echo $html;
        } else {
            wp_redirect($location, $status);
        }

        die();

    }

    /**
     * Check Collaborator office
     *
     * @param int    $collaborator_id
     * @param mixed  $notary
     * @return mixed
     */
    public function isSameOffice($collaborator_id, $notary)
    {
        if (is_object($notary) && property_exists($notary, 'crpcen')) {
            global $wpdb;

            $sql = "SELECT
                         COUNT(`cn`.`id`) nb
                    FROM
                        `{$wpdb->prefix}notaire` cn
                    WHERE
                        `cn`.`id` = %d
                    AND
                        `cn`.`crpcen` = %s";

            $result = $wpdb->get_row($wpdb->prepare($sql, $collaborator_id, $notary->crpcen));

            return $result->nb;
        }

        return false;
    }
}

