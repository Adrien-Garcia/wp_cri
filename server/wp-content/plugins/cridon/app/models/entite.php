<?php

/**
 * Class Entite
 */
class Entite extends \App\Override\Model\CridonMvcModel {
    public $primary_key = 'crpcen'; // not PK in DB, but used for most of the relations
    public $display_field = 'office_name';
    public $table = '{prefix}entite';
    public $has_many       = array(
        'Notaire' => array(
            'foreign_key' => 'crpcen'
        ),
        'Session' => array(
            'foreign_key' => 'id'
        )
    );
    public $has_and_belongs_to_many = array(
        'Entite' => array(
            'foreign_key' => 'crpcen',
            'association_foreign_key' => 'id_organisme',
            'join_table' => '{prefix}organisme_etude',
            'fields' => array('id','name','is_cridon','address','postal_code','city','phone_number','email')
        )
    );
    public $includes       = array('Sigle');
    public $belongs_to     = array(
        'Sigle' => array('foreign_key' => 'id_sigle')
    );

    /**
     * list facture by crpcen
     *
     * @var array
     */
    public $listDocs = array();

    /**
     * Get all 3 level prices for a given entite
     * @param $entite
     * @return array
     * @throws Exception
     */
    public function getRelatedPrices($entite) {

        // get number of active members of the office
        $options = array(
            'conditions' => array(
                'n.crpcen' => $entite->crpcen,
                'n.id_fonction' => Config::$functionsPricesCridonline,
                'u.user_status' => CONST_STATUS_ENABLED
            ),
            'synonym' => 'n',
            'join' => array(
                array(
                    'table'  => 'users u',
                    'column' => ' n.id_wp_user = u.id'
                ),
            )
        );
        $nbCollaboratorEntite = mvc_model('QueryBuilder')->countItems('notaire', $options, 'n.id');

        $subscriptionInfos = array();
        foreach (get_option('cridonline_prices_year_N') as $level => $prices) {
            // Tri du tableau de prix par clé descendante
            krsort($prices);
            foreach ($prices as $nbCollaborator => $price) {
                if ($nbCollaboratorEntite >= $nbCollaborator) {
                    $subscriptionInfos[$level] = $price;
                    break;
                }
            }
        }

        return $subscriptionInfos;
    }

    /**
     * Get current / next subscription price for a given entite
     * @param $entite
     * @param bool $isNextLevel
     * @return mixed
     */
    public function getSubscriptionPrice($entite, $isNextLevel = false){
        $level = ($isNextLevel && !empty($entite->next_subscription_level)) ? $entite->next_subscription_level : $entite->subscription_level;
        $prices = $this->getRelatedPrices($entite);
        return $prices[$level];
    }

    /**
     * Returns an array of all informations of a subscription : price, dates and front message
     *
     * @param $cridonlineLevel
     * @param int $promo
     * @return array|bool
     * @throws Exception
     */
    public function getSubscriptionInfos($cridonlineLevel,$promo = CONST_NO_PROMO){
        $notaire = CriNotaireData();
        $entite = mvc_model('Entite')->find_one_by_crpcen($notaire->crpcen);
        // La promotion existe-t-elle ?
        if (!in_array($cridonlineLevel,array(CONST_CRIDONLINE_LEVEL_2,CONST_CRIDONLINE_LEVEL_3))
            || !in_array($promo,array(CONST_NO_PROMO,CONST_PROMO_CHOC,CONST_PROMO_PRIVILEGE))){
            return false;
        }
        // La promotion s'applique-t-elle ?
        if (!in_array($cridonlineLevel, Config::$promo_available_for_level[$promo])){
            $promo = CONST_NO_PROMO;
        }
        $prices = $this->getRelatedPrices($entite);
        $label_offre = ($_REQUEST['level'] == CONST_CRIDONLINE_LEVEL_2 ? CONST_CRIDONLINE_LABEL_LEVEL_2 : CONST_CRIDONLINE_LABEL_LEVEL_3);
        if ($promo == CONST_NO_PROMO){
            //PAS D'OFFRE
            $price = $prices[$cridonlineLevel];
            $start_subscription_date = date('Y-m-d');
            $end_subscription_date = date('Y-m-d', strtotime('+' . CONST_CRIDONLINE_SUBSCRIPTION_DURATION_DAYS . 'days'));
            $echeance_subscription_date = date('Y-m-d', strtotime($end_subscription_date .'-'. CONST_CRIDONLINE_ECHEANCE_MONTH . 'month'));
            $front_message = sprintf(Config::$cridonlineMessages['no_promo'],$label_offre,$price);
        } elseif ($promo == CONST_PROMO_CHOC) {
            //CRIDONLINE PREMIUM OU EXCELLENCE + OFFRE CHOC : Abonnement au niveau 2 ou 3 aec la fin d'année en cours offerte
            $price = $prices[$cridonlineLevel];
            $start_subscription_date = date('Y-01-01', strtotime('+1 year')); // NextYear-01-01
            $end_subscription_date = date('Y-12-01', strtotime('+1 year')); // NextYear-12-31
            $echeance_subscription_date = date('Y-10-31', strtotime('+1 year')); // NextYear-10-31
            $front_message = sprintf(Config::$cridonlineMessages['promo_choc'],$label_offre,$price,date('Y'));
        } else {
            //CRIDONLINE EXCELLENCE + OFFRE PRIVILÉGIÉ : Abonnement au niveau 3 au prix du niveau 2 ; 2 ans d'engagement
            $price = $prices[CONST_CRIDONLINE_LEVEL_2];
            $start_subscription_date = date('Y-m-d');
            $end_subscription_date = date('Y-m-d', strtotime('+' . CONST_CRIDONLINE_SUBSCRIPTION_DURATION_DAYS . 'days'));
            $echeance_subscription_date = date('Y-m-d', strtotime($end_subscription_date .'-'. CONST_CRIDONLINE_ECHEANCE_MONTH . 'month'));
            $front_message = sprintf(Config::$cridonlineMessages['promo_privilege'],$price);
        }
        return array(
            'price' => $price,
            'start_subscription_date' => $start_subscription_date,
            'echeance_subscription_date' => $echeance_subscription_date,
            'end_subscription_date' => $end_subscription_date,
            'front_message' => $front_message
        );
    }

    /**
     * @return string of 6 random uppercase caracters ([A-Z0-9])
     */
    public function getRandomPromoCode(){
        return strtoupper(substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 6));
    }

    /**
     * Get all cridonline prices : year N and N+1
     * @return array
     */
    public function getAllCridonlinePrices() {
        return array(
            'year_N' => get_option('cridonline_prices_year_N'),
            'year_N_plus_1' => get_option('cridonline_prices_year_N_plus_1')
        );
    }

    /**
     * Updates year N cridonline prices with N+1 prices
     * @return bool
     */
    public function yearlyUpdateCridonlinePrices(){
        update_option('cridonline_prices_year_N',get_option('cridonline_prices_year_N_plus_1'));
        return true;
    }

    /**
     * Import facture action
     *
     * @param $sendMail bool Should notification mail be sent (default yes)
     *
     * @return int Status code
     *
     * @throws Exception
     */
    public function importFacture($sendMail = true)
    {
        return $this->importByType(CONST_DOC_TYPE_FACTURE, $sendMail);
    }

    /**
     * Import des fichiers de façon iteratif
     *
     * @param Iterator $documents
     * @param mixed $Iterator
     * @param int $limit
     * @param string $date
     * @param Document $documentModel
     * @param string $type
     * @param $sendMail bool Should notification mail be sent (default yes)
     */
    protected function importPdf($documents, $Iterator, $limit, $date, $documentModel, $type, $sendMail = true)
    {
        switch ($type) {
            case CONST_DOC_TYPE_RELEVECONSO;
                $pathDest      = CONST_IMPORT_RELEVECONSO_PATH;
                $pattern       = Config::$importRelevePattern;
                $parserPattern = Config::$importReleveParserPattern;
                $filePath      = '/releveconso/';
                $logFile       = 'importreleveconso.log';

                break;
            case CONST_DOC_TYPE_FACTURE;
                // destination
                $pathDest      = CONST_IMPORT_FACTURE_PATH;
                // pattern import (recuperation des infos par nom de fichier)
                $pattern       = Config::$importFacturePattern;
                // patter de parsage de fichier dans repertoire source
                $parserPattern = Config::$importFactureParserPattern;
                // chemin de base
                $filePath      = '/factures/';
                // fichier log
                $logFile       = 'importfactures.log';

                break;
            default:
                return ;
        }

        // parsage des documents
        foreach (new LimitIterator($documents, 0, $limit + 1) as $document) {
            try {
                if (!empty($document[0])) { // document existe
                    $fileInfo = pathinfo($document[0]);
                    if (!empty($fileInfo['basename']) && preg_match($pattern, $fileInfo['basename'], $matches)) {
                        // CRPCEN present
                        if (!empty($matches[1])) {
                            $crpcen = $matches[1];
                            if ($type === CONST_DOC_TYPE_FACTURE) {
                                //Rappel : <CRPCEN_TYPEPIECE_NUMFACTURE_TYPEFACTURE_AAAAMMJJ>.pdf
                                $typePiece = (!empty($matches[2])) ? $matches[2] : '';
                                $numFacture = (!empty($matches[3])) ? $matches[3] : '';
                                $typeFact = (!empty($matches[4])) ? $matches[4] : '';
                                $dateFact = (!empty($matches[5])) ? $matches[5] : '';
                            } else { //$type = 'releveconso'
                                //Rappel : <CRPCEN_releveconso_AAAAMMJJ>.pdf
                                $dateFact = (!empty($matches[3])) ? $matches[3] : '';
                            }

                            $date = substr($dateFact, 0, 6);
                            if ($date) {
                                $path = $pathDest . $date . DIRECTORY_SEPARATOR;
                                if (!file_exists($path)) { // repertoire manquant
                                    // creation du nouveau repertoire
                                    wp_mkdir_p($path);
                                }
                                // Add or update file to the right location
                                $moved = rename($document[0], $path . $fileInfo['basename']);

                                // As filepath won't change when file is updated, model Document won't change either
                                // Only work with model during insert, the above operation is sufficient for update
                                $documentPath = $filePath . $date . '/' . $fileInfo['basename'];
                                $exist = $documentModel->find_one(array(
                                    'conditions' => array(
                                        'file_path' => $documentPath,
                                    )
                                ));
                                if ($moved && empty($exist)) {
                                    // donnees document
                                    $docData = array(
                                        'Document' => array(
                                            'file_path'         => $documentPath,
                                            'download_url'      => '/documents/download/' . $crpcen,
                                            'date_modified'     => date('Y-m-d H:i:s'),
                                            'type'              => $type,
                                            'id_externe'        => $crpcen,
                                            'name'              => $fileInfo['basename'],
                                            'label'             => empty($dateFact) ? '' : $dateFact,
                                            'numero_facture'    => empty($numFacture) ? '' : $numFacture,
                                            'type_piece'        => empty($typePiece) ? '' : $typePiece,
                                            'type_facture'      => empty($typeFact) ? '' : $typeFact,
                                        )
                                    );

                                    // insertion données
                                    $documentId = $documentModel->create($docData);

                                    // maj download_url
                                    if ($documentId) {

                                        if ($type === CONST_DOC_TYPE_FACTURE && $sendMail) {
                                            $facture = new \stdClass();
                                            $facture->name = $fileInfo['basename'];
                                            $facture->download_url = home_url().$documentModel->generatePublicUrl($documentId);

                                            // send email to notaries
                                            $this->sendEmailFacture($crpcen, $facture);
                                        }

                                        unset($crpcen);
                                        unset($numFacture);
                                        unset($typePiece);
                                        unset($typeFact);
                                    }
                                }
                            }
                        }
                        // liberation de variables
                        unset($matches);
                    }
                    // liberation des variables
                    unset($fileInfo);
                    unset($document);
                }
            } catch (Exception $e) {
                // renommage fichier d'erreur
                rename($document[0], $document[0] . '.error');

                writeLog($e, $logFile);
            }
        }

        $documents = new RegexIterator($Iterator, $parserPattern, RecursiveRegexIterator::GET_MATCH);
        // test s'il y a encore de fichier
        $documents->next();
        $doc = $documents->current();
        if (!empty($doc)) {
            // appel action d'import
            $documents->rewind();
            $this->importPdf($documents, $Iterator, $limit, $date, $documentModel, $type, $sendMail);
        }
    }

    /**
     * Import de fichier par type (facture, releveconso)
     *
     * @param string $type
     * @param $sendMail bool Should notification mail be sent (default yes)
     * @throws Exception
     * @return int
     */
    protected function importByType($type, $sendMail = true)
    {
        // bloc commun
        // offset block
        $limit      = 1000;

        // date
        $date = date('Ym');

        /** @var $documentModel Document */
        $documentModel = mvc_model('Document');

        if (!in_array($type, array(CONST_DOC_TYPE_FACTURE, CONST_DOC_TYPE_RELEVECONSO))){
            return CONST_STATUS_CODE_GONE;
        }
        switch ($type) {
            case CONST_DOC_TYPE_RELEVECONSO;
                // documents
                $Directory  = new RecursiveDirectoryIterator(CONST_IMPORT_RELEVECONSO_TEMP_PATH);
                $Iterator   = new RecursiveIteratorIterator($Directory);
                // filtre les fichiers selon la regle de nommage predefinie
                // ACs : <CRPCEN_releveconso_AAAAMMJJ>.pdf
                // @see \Config::$importReleveParserPattern
                $documents  = new RegexIterator($Iterator, Config::$importReleveParserPattern, RecursiveRegexIterator::GET_MATCH);

                break;
            case CONST_DOC_TYPE_FACTURE;
                // documents
                $Directory = new RecursiveDirectoryIterator(CONST_IMPORT_FACTURE_TEMP_PATH);
                $Iterator  = new RecursiveIteratorIterator($Directory);
                // filtre les fichiers selon la regle de nommage predefinie
                // ACs : <CRPCEN_NUMFACTURE_TYPEFACTURE_AAAAMMJJ>.pdf
                // @see \Config::$importFactureParserPattern
                $documents = new RegexIterator($Iterator, Config::$importFactureParserPattern, RecursiveRegexIterator::GET_MATCH);

                break;
            default:
                return CONST_STATUS_CODE_GONE;
        }
        // import documents
        $this->importPdf($documents, $Iterator, $limit, $date, $documentModel, $type, $sendMail);

        return CONST_STATUS_CODE_OK;
    }


    /**
     * Import Releve action
     *
     * @param $sendMail bool Should notification mail be sent (default yes)
     *
     * @return int Status code
     *
     * @throws Exception
     */
    public function importReleveconso($sendMail = true)
    {
        return $this->importByType(CONST_DOC_TYPE_RELEVECONSO, $sendMail);
    }

    /**
     * Envoie email de notification
     *
     * @param string $crpcen
     * @param object $facture
     */
    public function sendEmailFacture($crpcen, $facture)
    {
        // en-tete email
        $headers = array('Content-Type: text/html; charset=UTF-8');

        // list des notaires à notifier par entite
        $notary = $this->listNotaryToBeNotified($crpcen);

        $dest        = array();
        $display_documents_url = true;
        $office_dest = array();
        $office_name = '';
        if (is_array($notary) && count($notary) > 0) {
            foreach ($notary as $item) {
                if (filter_var($item->email_adress, FILTER_VALIDATE_EMAIL)) {
                    $dest[] = $item->email_adress;
                } elseif (filter_var($item->office_email_adress_1, FILTER_VALIDATE_EMAIL)) {
                    $office_dest[] = $item->office_email_adress_1;
                } elseif (filter_var($item->office_email_adress_2, FILTER_VALIDATE_EMAIL)) {
                    $office_dest[] = $item->entite->office_email_adress_2;
                } elseif (filter_var($item->office_email_adress_3, FILTER_VALIDATE_EMAIL)) {
                    $office_dest[] = $item->office_email_adress_3;
                }
                // nom de l'entite concernée
                $office_name = $item->office_name;
            }
        }
        if (empty($dest)){
            $dest = $office_dest;
            $display_documents_url = false;
        }

        // destinataire non vide
        if (count($dest) > 0) {
            array_unique($dest);
            $vars    = array(
                'office_name'           => $office_name,
                'display_documents_url' => $display_documents_url,
                'doc_url'               => $facture->download_url,
            );
            $message = CriRenderView('mail_notification_facture', $vars, 'custom', false);

            $env = getenv('ENV');
            if (empty($env) || ($env !== PROD)) {
                if ($env === 'PREPROD') {
                    $dest = Config::$notificationAddressPreprod;
                } else {
                    $dest = Config::$notificationAddressDev;
                }
                wp_mail($dest, Config::$mailSubjectNotifFacture, $message, $headers);
            } else {
                /**
                 * wp_mail : envoie mail destinataire multiple
                 *
                 * @see wp-includes/pluggable.php : 228
                 * @param string|array $to Array or comma-separated list of email addresses to send message.
                 */
                wp_mail($dest, Config::$mailSubjectNotifFacture, $message, $headers);
            }
        }
    }

    /**
     * Recuperation liste des notaires associés
     *
     * @param string $crpcen
     * @return array|null|object
     */
    protected function listNotaryToBeNotified($crpcen)
    {
        global $wpdb;

        // Collaborateur comptable
        $collaborator_comptable = implode(', ', Config::$notaryFunctionCollaboratorComptableId);

        // Notaire fonction
        $notary_fonction = implode(', ', Config::$allowedNotaryFunction);

        // requette
        $query = "  SELECT
                      `cn`.`email_adress`,
                      `ce`.`office_name`,
                      `ce`.`office_email_adress_1`,
                      `ce`.`office_email_adress_2`,
                      `ce`.`office_email_adress_3`
                    FROM
                      `{$wpdb->prefix}notaire` cn
                    LEFT JOIN
                      `{$wpdb->prefix}fonction_collaborateur` cfc
                      ON
                        `cfc`.`id` = `cn`.`id_fonction_collaborateur`
                    LEFT JOIN
                      {$wpdb->prefix}entite ce
                      ON
                        ce.crpcen = `cn`.`crpcen`
                    INNER JOIN
                      {$wpdb->prefix}users u
                      ON
                        cn.id_wp_user = `u`.`ID`
                    WHERE
                      `cn`.`crpcen` = %s
                    AND
                      `u`.`user_status` = %s
                    AND (
                          `cn`.`id_fonction_collaborateur` IN ({$collaborator_comptable})
                          OR
                          `cn`.`id_fonction` IN ({$notary_fonction})
                    ) ";

        return $wpdb->get_results($wpdb->prepare($query, $crpcen, CONST_STATUS_ENABLED));
    }

    /**
     * Récupère tous les organismes dont dépend l'étude du crpcen passé en entré
     * @param string $crpcen of the entite
     * @return object[] organisms
     */
    public function getOrganismesAssociatedToEntite ($crpcen) {
        global $wpdb;
        if (empty($crpcen)){
            return array();
        }
        $query = '
            SELECT *
                FROM '.$wpdb->prefix.'entite as O
                JOIN '.$wpdb->prefix.'organisme_etude as OE ON O.id = OE.id_organisme
                AND OE.crpcen = '.$crpcen.'
                ';

        return $wpdb->get_results($query);
    }

}
