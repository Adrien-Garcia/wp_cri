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

    public function getSubscriptionPrice($etude,$isNext = true){


        $level = ($isNext && !empty($etude->next_subscription_level)) ? $etude->next_subscription_level : $etude->subscription_level;

        $options = array('conditions' => array('crpcen' => $etude->crpcen));
        $nbCollaboratorEtude = count(mvc_model('QueryBuilder')->countItems('notaire', $options));
        $prices = Config::$pricesLevelsVeilles[0][$level];
        krsort($prices);
        // Tri du tableau de prix par clé descendante
        foreach($prices as $nbCollaborator => $price) {
            if ($nbCollaboratorEtude >= $nbCollaborator) {
                return $price;
            }
        }
    }

    /**
     * Import facture action
     *
     * @throws Exception
     */
    public function importFacture()
    {
        // documents
        $Directory  = new RecursiveDirectoryIterator(CONST_IMPORT_FACTURE_TEMP_PATH);
        $Iterator   = new RecursiveIteratorIterator($Directory);
        $documents  = new RegexIterator($Iterator, '/^.+\.pdf$/i', RecursiveRegexIterator::GET_MATCH);

        // offset block
        $limit      = 1000;

        // date
        $date = date('Ym');

        // document entity
        $documentModel = mvc_model('Document');

        $this->importPdf($documents, $Iterator, $limit, $date, $documentModel);

        // send email to notary
        $this->sendEmailFacture();
    }

    /**
     * Import des fichiers de façon iteratif
     *
     * @param array $documents
     * @param mixed $Iterator
     * @param int $limit
     * @param string $date
     * @param mixed $documentModel
     */
    protected function importPdf($documents, $Iterator, $limit, $date, $documentModel)
    {
        foreach(new LimitIterator($documents, 0, $limit + 1) as $document) {
            try {
                if (!empty($document[0])) { // document existe
                    $fileInfo = pathinfo($document[0]);
                    if (!empty($fileInfo['basename'])) {
                        // filtre les fichiers selon la regle de nommage predefinie
                        // ACs : <CRPCEN_NUMFACTURE_TYPEFACTURE_AAAAMMJJ>.pdf
                        // @see \Config::$importFacturePattern
                        if (preg_match_all(Config::$importFacturePattern, $fileInfo['basename'], $matches)) {
                            $path = CONST_IMPORT_FACTURE_PATH . $date . DIRECTORY_SEPARATOR;
                            if (!file_exists($path)) { // repertoire manquant
                                // creation du nouveau repertoire
                                wp_mkdir_p($path);
                            }
                            // CRPCEN present
                            if (!empty($matches[1][0]) && copy($document[0], $path . $fileInfo['basename'])) {
                                $crpcen   = $matches[1][0];
                                $typeFact = $matches[3][0];

                                // donnees document
                                $docData = array(
                                    'Document' => array(
                                        'file_path'     => '/factures/' . $date . '/' . $fileInfo['basename'],
                                        'download_url'  => '/documents/download/' . $crpcen,
                                        'date_modified' => date('Y-m-d H:i:s'),
                                        'type'          => CONST_DOC_TYPE_FACTURE,
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

                                    // regroupement doc par etude
                                    $docs                    = new \stdClass();
                                    $docs->name              = $fileInfo['basename'];
                                    $docs->download_url      = get_home_url() . $docData['Document']['download_url'];
                                    $this->listDocs[$crpcen] = clone $docs;

                                    // archivage fichier
                                    $archivePath = CONST_IMPORT_FACTURE_TEMP_PATH . DIRECTORY_SEPARATOR . 'archives/' . $date . '/';
                                    if (!file_exists($archivePath)) { // repertoire manquant
                                        // creation du nouveau repertoire
                                        wp_mkdir_p($archivePath);
                                    }
                                    rename($document[0], $archivePath . $fileInfo['basename'] . '.' . $date);
                                }
                            }
                        }
                    }
                    // liberation des variables
                    unset($fileInfo);
                    unset($document);
                    unset($archivePath);
                    unset($matches);
                    unset($crpcen);
                    unset($typeFact);
                }
            } catch(Exception $e) {
                // renommage fichier d'erreur
                rename($document[0],
                       str_replace(
                           array('.pdf', '.PDF', '.Pdf'),
                           array('.pdf.error', '.PDF.error', '.Pdf.error'),
                           $document[0]
                       )
                );

                writeLog($e, 'importfactures.log');
            }
        }

        $documents  = new RegexIterator($Iterator, '/^.+\.pdf$/i', RecursiveRegexIterator::GET_MATCH);
        // test s'il y a encore de fichier
        $documents->next();
        $doc = $documents->current();
        if (!empty($doc)) {
            // appel action d'import
            $documents->rewind();
            $this->importPdf($documents, $Iterator, $limit, $date, $documentModel);
        }
    }

    /**
     * Envoie email de notification
     */
    public function sendEmailFacture()
    {
        // list doc non vide
        if (count($this->listDocs) > 0) {
            // en-tete email
            $headers = array('Content-Type: text/html; charset=UTF-8');
            // parse liste doc
            foreach ($this->listDocs as $crpcen => $facture) {
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
                        wp_mail(Config::$notificationAddressPreprod, Config::$mailSubjectNotifFacture[0], $message,
                                $headers);
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