<?php

/**
 * Class Etude
 */
class Etude extends \App\Override\Model\CridonMvcModel {
    public $primary_key = 'crpcen';
    public $display_field = 'office_name';
    public $table = '{prefix}etude';
    var $has_many       = array(
        'Notaire' => array(
            'foreign_key' => 'crpcen'
        )
    );
    var $includes       = array('Sigle');
    var $belongs_to     = array(
        'Sigle' => array('foreign_key' => 'id_sigle')
    );

    /**
     * list facture by crpcen
     *
     * @var array
     */
    var $listDocs = array();

    public function getRelatedPrices($etude) {

        // get number of members of the office
        $options = array('conditions' => array('crpcen' => $etude->crpcen, 'id_fonction' => Config::$functionsPricesCridonline));
        $nbCollaboratorEtude = mvc_model('QueryBuilder')->countItems('notaire', $options);

        $subscriptionInfos = array();
        foreach (Config::$pricesLevelsVeilles as $level => $prices) {
            // Tri du tableau de prix par clé descendante
            krsort($prices);
            foreach ($prices as $nbCollaborator => $price) {
                if ($nbCollaboratorEtude >= $nbCollaborator) {
                    $subscriptionInfos[$level] = $price;
                    break;
                }
            }
        }

        return $subscriptionInfos;
    }

    public function getSubscriptionPrice($etude, $isNextLevel = false){
        $level = ($isNextLevel && !empty($etude->next_subscription_level)) ? $etude->next_subscription_level : $etude->subscription_level;
        $prices = $this->getRelatedPrices($etude);
        return $prices[$level];
    }

    /**
     * Import facture action
     *
     * @throws Exception
     */
    public function importFacture()
    {
        $this->importByType();
    }

    /**
     * Import des fichiers de façon iteratif
     *
     * @param Iterator $documents
     * @param mixed $Iterator
     * @param int $limit
     * @param string $date
     * @param mixed $documentModel
     * @param string $type
     */
    protected function importPdf($documents, $Iterator, $limit, $date, $documentModel, $type)
    {
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

        // reafectation variable selon le type de traitement
        if ($type == 'releveconso') {
            $pathDest      = CONST_IMPORT_RELEVECONSO_PATH;
            $pattern       = Config::$importRelevePattern;
            $parserPattern = Config::$importReleveParserPattern;
            $filePath      = '/releveconso/';
            $logFile       = 'importreleveconso.log';
        }
        // parsage des documents
        foreach (new LimitIterator($documents, 0, $limit + 1) as $document) {
            try {
                if (!empty($document[0])) { // document existe
                    $fileInfo = pathinfo($document[0]);
                    if (!empty($fileInfo['basename']) && preg_match($pattern, $fileInfo['basename'], $matches)) {
                        $path = $pathDest . $date . DIRECTORY_SEPARATOR;
                        if (!file_exists($path)) { // repertoire manquant
                            // creation du nouveau repertoire
                            wp_mkdir_p($path);
                        }
                        // CRPCEN present
                        if (!empty($matches[1]) && rename($document[0], $path . $fileInfo['basename'])) {
                            $crpcen   = $matches[1];
                            $typeFact = (!empty($matches[3])) ? $matches[3] : ' ';

                            // donnees document
                            $docData = array(
                                'Document' => array(
                                    'file_path'     => $filePath . $date . '/' . $fileInfo['basename'],
                                    'download_url'  => '/documents/download/' . $crpcen,
                                    'date_modified' => date('Y-m-d H:i:s'),
                                    'type'          => $type,
                                    'id_externe'    => $crpcen,
                                    'name'          => $fileInfo['basename'],
                                    'label'         => $typeFact
                                )
                            );

                            // insertion données
                            $documentId = $documentModel->create($docData);

                            // maj download_url
                            if ($documentId) {
                                $docData = array(
                                    'Document' => array(
                                        'id'           => $documentId,
                                        'download_url' => '/documents/download/' . $documentId
                                    )
                                );
                                $documentModel->save($docData);

                                if ($type == 'facture') {
                                    $facture                    = new \stdClass();
                                    $facture->name              = $fileInfo['basename'];
                                    $facture->download_url      = get_home_url() . $docData['Document']['download_url'];

                                    // send email to notaries
                                    $this->sendEmailFacture($crpcen, $facture);
                                }

                                unset($crpcen);
                                unset($typeFact);
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
            $this->importPdf($documents, $Iterator, $limit, $date, $documentModel, $type);
        }
    }

    /**
     * Import de fichier par type (facture, releveconso)
     *
     * @param string $type
     * @throws Exception
     * @return int
     */
    protected function importByType($type = 'facture')
    {
        // bloc commun
        // offset block
        $limit      = 1000;

        // date
        $date = date('Ym');

        // model document
        $documentModel = mvc_model('Document');

        switch ($type) {
            case 'releveconso';
                // documents
                $Directory  = new RecursiveDirectoryIterator(CONST_IMPORT_RELEVECONSO_TEMP_PATH);
                $Iterator   = new RecursiveIteratorIterator($Directory);
                // filtre les fichiers selon la regle de nommage predefinie
                // ACs : <CRPCEN_releveconso_AAAAMMJJ>.pdf
                // @see \Config::$importReleveParserPattern
                $documents  = new RegexIterator($Iterator, Config::$importReleveParserPattern, RecursiveRegexIterator::GET_MATCH);

                break;
            default:
                // documents
                $Directory = new RecursiveDirectoryIterator(CONST_IMPORT_FACTURE_TEMP_PATH);
                $Iterator  = new RecursiveIteratorIterator($Directory);
                // filtre les fichiers selon la regle de nommage predefinie
                // ACs : <CRPCEN_NUMFACTURE_TYPEFACTURE_AAAAMMJJ>.pdf
                // @see \Config::$importFactureParserPattern
                $documents = new RegexIterator($Iterator, Config::$importFactureParserPattern, RecursiveRegexIterator::GET_MATCH);

                break;
        }
        // import documents
        $this->importPdf($documents, $Iterator, $limit, $date, $documentModel, $type);

        return CONST_STATUS_CODE_OK;
    }


    /**
     * Import Releve action
     *
     * @throws Exception
     */
    public function importReleveconso()
    {
        $this->importByType('releveconso');
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

        // list des notaires à notifier par etude
        $notary = $this->listNotaryToBeNotified($crpcen);

        $dest        = array();
        $office_name = '';
        if (is_array($notary) && count($notary) > 0) {
            foreach ($notary as $item) {
                if (filter_var($item->email_adress, FILTER_VALIDATE_EMAIL)) {
                    $dest[] = $item->email_adress;
                } elseif (filter_var($item->office_email_adress_1, FILTER_VALIDATE_EMAIL)) {
                    $dest[] = $item->office_email_adress_1;
                } elseif (filter_var($item->office_email_adress_2, FILTER_VALIDATE_EMAIL)) {
                    $dest[] = $item->etude->office_email_adress_2;
                } elseif (filter_var($item->office_email_adress_3, FILTER_VALIDATE_EMAIL)) {
                    $dest[] = $item->office_email_adress_3;
                }

                // nom de l'etude concernée
                $office_name = $item->office_name;
            }
        }

        // destinataire non vide
        if (count($dest) > 0) {
            $vars    = array(
                'office_name' => $office_name,
                'doc_url'     => $facture->download_url,
            );
            $message = CriRenderView('mail_notification_facture', $vars, 'custom', false);

            $env = getenv('ENV');
            if (empty($env) || ($env !== PROD)) {
                wp_mail(Config::$notificationAddressPreprod, Config::$mailSubjectNotifFacture[0], $message, $headers);
            } else {
                /**
                 * wp_mail : envoie mail destinataire multiple
                 *
                 * @see wp-includes/pluggable.php : 228
                 * @param string|array $to Array or comma-separated list of email addresses to send message.
                 */
                wp_mail($dest, Config::$mailSubjectNotifFacture[0], $message, $headers);
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
                      {$wpdb->prefix}etude ce
                      ON
                        ce.crpcen = `cn`.`crpcen`
                    WHERE
                      `cn`.`crpcen` = %s
                    AND (
                          `cn`.`id_fonction_collaborateur` IN ({$collaborator_comptable})
                          OR
                          `cn`.`id_fonction` IN ({$notary_fonction})
                    ) ";

        return $wpdb->get_results($wpdb->prepare($query, $crpcen));
    }

}
