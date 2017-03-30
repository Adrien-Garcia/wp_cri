<?php


class Formation extends \App\Override\Model\CridonMvcModel
{
    use DocumentsHolderTrait;

    public $table = "{prefix}formation";
    public $includes = array('Post','Matiere', 'Session');
    public $belongs_to = array(
        'Post' => array('foreign_key' => 'post_id'),
    );
    public $has_many = array(
        'Session' => array(
            'foreign_key' => 'id'
        )
    );

    public $has_and_belongs_to_many = array (
        'Matiere' => array(
            'foreign_key' => 'id_formation',
            'association_foreign_key' => 'id_matiere',
            'join_table' => '{prefix}formation_matiere',
            'fields' => array('id','code','label','short_label','displayed','picto','virtual_name','question','color')
        )
    );

    public $display_field = 'name';

    /**
     * @var string
     */
    const IMPORT_ODBC_OPTION = 'odbc';

    /**
     * @var string
     */
    const IMPORT_OCI_OPTION = 'oci';

    /**
     * @var DBConnect
     */
    protected $adapter;
    
    /**
     * Retrieve all Matieres for the formation
     *
     * @return \MvcModelObject
     */
    public function getMatieres ($formation = null) {
        global $wpdb;
        // get list of existing matiere
        $matieres = mvc_model('Matiere')->find(array(
            'joins' => array() //dummy condition to avoid join
        ));
        $matieres = assocToKeyVal($matieres, 'id');
        $select  = "SELECT fm.formation_id ,fm.matiere_id";
        $query = $select."
            FROM cri_formation f
            LEFT JOIN cri_formation_matiere fm ON f.id = fm.formation_id ";
        if (!empty($formation)) {
            $query .= "WHERE f.id = ".$formation;
        }
        $query .= ";";
        $results = $wpdb->get_results($query);
        $r = array();
        if (empty($formation)) {
            foreach ($results as $v) {
                if (!empty($matieres[$v->matiere_id])) {
                    $r[$v->formation_id][] = $matieres[$v->matiere_id];
                }
            }
        } else {
            foreach ($results as $v) {
                if (!empty($matieres[$v->matiere_id])) {
                    $r[] = $matieres[$v->matiere_id];
                }
            }
        }
        $objects = $r;
        return (!empty($objects)) ? $objects : null;
    }

    public function sendEmailPreinscription($session, $formationParticipants, $formationCommentaire) {
        $data = $this->_prepareNotificationsMails();

        // destinataire non vide
        if (count($data['dest']) > 0) {
            array_unique($data['dest']);
            $vars    = array(
                'type'                  => CONST_FORMATION_PREINSCRIPTION,
                'date'                  => strftime('%d %b %Y',strtotime($session->date)),
                'name'                  => $session->formation->post->post_title,
                'organisme'             => strtoupper($session->entite->office_name. ' ' . $session->entite->city),
                'participants'          => $formationParticipants,
                'commentaire'           => $formationCommentaire,
                'notaire'               => $data['notaire'],
            );
            $this->_sendNotificationMail($data['dest'], $vars, Config::$mailSubjectFormationPreinscription. ' : ' .$session->formation->post->post_title);
            $this->_sendAdminNotificationMail($data['adminDest'], $vars, Config::$mailSubjectAdminFormationPreinscription. ' : ' .$session->formation->post->post_title);
        }
    }

    public function sendEmailGenerique($formationThematique, $formationCommentaire) {
        $data = $this->_prepareNotificationsMails();

        // destinataire non vide
        if (count($data['dest']) > 0) {
            array_unique( $data['dest']);
            $vars    = array(
                'type'                  => CONST_FORMATION_GENERIQUE,
                'name'                  => $formationThematique,
                'commentaire'           => $formationCommentaire,
                'notaire'               => $data['notaire']
            );
            $this->_sendNotificationMail($data['dest'], $vars, Config::$mailSubjectFormationGenerique. ' : ' .$formationThematique);
            $this->_sendAdminNotificationMail($data['adminDest'], $vars, Config::$mailSubjectFormationGenerique. ' : ' .$formationThematique);

        }
    }

    public function sendEmailDemande ($formation, $formationParticipants, $formationCommentaire){
        $data = $this->_prepareNotificationsMails();

        // destinataire non vide
        if (count($data['dest']) > 0) {
            array_unique($data['dest']);
            $vars    = array(
                'type'                  => CONST_FORMATION_DEMANDE,
                'name'                  => $formation->post->post_title,
                'participants'          => $formationParticipants,
                'commentaire'           => $formationCommentaire,
                'notaire'               => $data['notaire'],
            );
            $this->_sendNotificationMail($data['dest'], $vars, Config::$mailSubjectFormationDemande. ' : ' .$formation->post->post_title);
            $this->_sendAdminNotificationMail($data['adminDest'], $vars, Config::$mailSubjectFormationDemande. ' : ' .$formation->post->post_title);
        }
    }

    protected function _prepareNotificationsMails() {
        $notaire = mvc_model('Notaire')->getUserConnectedData();

        // list des notaires à notifier par etude
        $notary = array($notaire);

        $adminDest = Config::$notificationAddressFormulaireFormation;
        $dest        = array();
        if (is_array($notary) && count($notary) > 0) {
            foreach ($notary as $item) {
                if (filter_var($item->email_adress, FILTER_VALIDATE_EMAIL)) {
                    $dest[] = $item->email_adress;
                }
            }
        }

        $env = getenv('ENV');
        if (empty($env) || ($env !== PROD)) {
            if ($env === 'PREPROD') {
                $dest = array(Config::$notificationAddressPreprod);
                $adminDest = array(Config::$notificationAddressPreprod);
            } else {
                $dest = array(Config::$notificationAddressDev);
                $adminDest = array(Config::$notificationAddressDev);
            }
        }

        return array(
            'dest' => $dest,
            'adminDest' => $adminDest,
            'notaire' => array(
                'crpcen'                => $notaire->crpcen,
                'fname'                 => $notaire->first_name,
                'lname'                 => $notaire->last_name,
                'mail'                  => !empty($notaire->email_adress) ? $notaire->email_adress : '',
            ),
        );
    }

    protected function _sendNotificationMail($dest, $vars, $subject) {
        $headers = array('Content-Type: text/html; charset=UTF-8');
        $message = CriRenderView('mail_notification_formation', $vars, 'custom', false);
        wp_mail($dest, $subject, $message, $headers);
    }

    protected function _sendAdminNotificationMail($dest, $vars, $subject) {
        $headers = array('Content-Type: text/html; charset=UTF-8');
        $messageAdmin = CriRenderView('mail_notification_admin_formation', $vars, 'custom', false);
        wp_mail($dest, $subject, $messageAdmin, $headers);
    }

    /**
     * On 1st of january, updates option so next year catalog isn't published
     *
     * @return bool
     */
    public function resetCatalogNextYear(){
        update_option('cridon_next_year_catalog_published',0);
        return true;
    }

    /**
     * Action for importing Formations data 
     *
     * @return mixed
     */
    public function importIntoFormation () {
        $this->adapter = null;

        // try to import and prevent exception to announce a false positive result
        try {
            switch (strtolower(CONST_IMPORT_OPTION)) {
                case self::IMPORT_ODBC_OPTION:
                    $this->adapter = CridonODBCAdapter::getInstance();
                case self::IMPORT_OCI_OPTION:
                    //if case above did not match, set OCI
                    $this->adapter = empty($this->adapter) ? CridonOCIAdapter::getInstance() : $this->adapter;
                default :
                    $this->importFormations();
                    break;
            }

            // status code
            return CONST_STATUS_CODE_OK;

        } catch (Exception $e) {
            // write into logfile
            writeLog($e, 'formation.log');

            // status code
            return CONST_STATUS_CODE_GONE;
        }
    }

    /**
     * @throws Exception
     *
     * @return void
     */
    protected function importFormations()
    {
        // get list of existing entite
        $existing = mvc_model('Formation')->find(array(
            'conditions' => "id_form <> ''",
            'joins' => array('Post') //dummy condition to avoid join
        ));
        $existing = assocToKeyVal($existing, 'id_form');

        // get list of existing matiere
        $matieres = mvc_model('Matiere')->find(array(
            'joins' => array() //dummy condition to avoid join
        ));
        $matieres = assocToKeyVal($matieres, 'code');

        // get list of existing matiere
        $juristes = mvc_model('UserCridon')->find(array(
            'joins' => array() //dummy condition to avoid join
        ));
        $juristes = assocToKeyVal($juristes, 'id_erp');

        $adapter = $this->adapter;
        $formationsInfos = array(
            $adapter::ZIDFORM,
            $adapter::ZTITRE,
            $adapter::ZMATIERE1,
            $adapter::ZMATIERE2,
            $adapter::ZMATIERE3,
            $adapter::ZMATIERE4,
            $adapter::ZANNEE,
            $adapter::ZJURISTE1,
            $adapter::ZJURISTE2,
            $adapter::ZJURISTE3,
            $adapter::ZJURISTE4,
            $adapter::ZNUMERO,
            $adapter::UPDATE,
            $adapter::ZOBJECTIF,
        );
        $formationsInfos = implode(', ', $formationsInfos);

        $sql = 'SELECT ' . $formationsInfos . ' FROM ' . CONST_DB_TABLE_FORMATION;

        $errors = array();
        // exec query
        $adapter->execute($sql);

        while ($data = $adapter->fetchData()) {
            if ( isset($data[$adapter::ZIDFORM]) ) {
                $data[$adapter::ZIDFORM] = trim($data[$adapter::ZIDFORM]);
                $data['updated'] = date_create_from_format('d-M-y', $data['UPDDAT_0']);

                if (!empty($data[$adapter::ZIDFORM])) {
                    $aData = array(
                        'id_form' => $data[$adapter::ZIDFORM],
                        'csn' => $data[$adapter::ZNUMERO],
                    );
                    $content = $data[$adapter::ZOBJECTIF]->read($adapter::CLOB_MAX_SIZE);
                    $content = wpautop(str_replace("\t", "    ", $content));
                    $pData = array(
                        'post_title' => mb_convert_encoding($data[$adapter::ZTITRE], 'UTF-8'),
                        'post_content' => $content,
                        'post_status' => 'publish',
                    );
                    if (isset($existing[$aData['id_form']])
                        && isset($existing[$aData['id_form']]->post)
                    ) { // Update
                        // just the date, no time
                        $post_updated = date_create_from_format('Y-m-d|+',$existing[$aData['id_form']]->post->post_modified);
                        $erp_updated = date_create_from_format('d-M-y',$data['UPDDAT_0']);
                        if ($post_updated->getTimestamp() < $erp_updated->getTimestamp()) {
                            $pData['ID'] = $existing[$data[$adapter::ZIDFORM]]->post_id;
                            $return = wp_update_post($pData, false);
                            if ($return !== 0) {
                                try {
                                    mvc_model('formation')->update($existing[$aData['id_form']]->id, $aData);
                                } catch (\Exception $e) {
                                    // write into logfile
                                    writeLog($e, 'formation.log');
                                    writeLog('Post id : ' . $return , 'formation.log');
                                    $errors[] = $e->getMessage();
                                }
                                $this->_update_matieres($data, $existing[$aData['id_form']]->id, $matieres);
                                $this->_update_juristes($data, $existing[$aData['id_form']]->id, $juristes);
                                $this->_update_millesime($data[$adapter::ZANNEE], $existing[$aData['id_form']]->id);
                            }
                        }

                    } else { // Insert
                        $return = wp_insert_post($pData, false);
                        if ($return !== 0) {
                            $aData['post_id'] = $return;
                            try {
                                $formation_id = mvc_model('formation')->insert($aData);
                            } catch (\Exception $e) {
                                // write into logfile
                                writeLog($e, 'formation.log');
                                writeLog('Post id : ' . $return , 'formation.log');
                                $errors[] = $e->getMessage();
                            }
                            if (!empty($formation_id)) {
                                $this->_update_matieres($data, $formation_id, $matieres);
                                $this->_update_juristes($data, $formation_id, $juristes);
                                $this->_update_millesime($data[$adapter::ZANNEE], $formation_id);
                            }
                        }
                    }
                }
            }

        }
        if (!empty($errors)) {
            // send email
           // reportError(CONST_EMAIL_ERROR_CATCH_EXCEPTION, implode('     \n', $errors), 'Cridon - Données étude - Erreur mise à jour');
        }
    }

    private function _update_matieres ($data, $id, $matieres) {
        $this->_update_many_to_many('matiere', $data,$id, $matieres);
    }

    private function _update_juristes ($data, $id, $juristes) {
        $this->_update_many_to_many('juriste', $data,$id, $juristes);
    }

    private function _update_many_to_many ($type, $data, $id, $existing) {
        global $wpdb;
        $type = strtolower($type);
        $columns = array(
            constant('DBConnect::Z'.strtoupper($type).'1'),
            constant('DBConnect::Z'.strtoupper($type).'2'),
            constant('DBConnect::Z'.strtoupper($type).'3'),
            constant('DBConnect::Z'.strtoupper($type).'4')
        );

        $wpdb->delete('cri_formation_'.$type , array('formation_id' => $id), array('%d'));
        $query = "INSERT INTO `cri_formation_".$type."` (formation_id, ".$type."_id) VALUES ";
        $insert = array();
        $toInsert = array();
        $place_holders = array();
        foreach ($columns as $column) { // get all columns value if !empty
            if (!empty(trim($data[$column])) && !empty($existing[trim($data[$column])])) {
                $toInsert[] = $existing[trim($data[$column])]->id;
            }
        }

        $toInsert = array_unique($toInsert); // remove duplicates
        foreach ($toInsert as $j) {
            array_push($insert ,$id, $j);
            $place_holders[] = "(%d, %d)";
        }

        $query .= implode(', ', $place_holders);
        try {
            if (!empty($insert)) {
                $prepared = $wpdb->prepare($query, $insert);
                $wpdb->query($prepared);
            }
        } catch (\Exception $e) {
            writeLog('Error while updating '.$type.'s link to formation '.$id , 'formation.log');
            writeLog($e, 'formation.log');
        }
    }

    private function _update_millesime($yearString, $id) {
        $millesime = mvc_model('Millesime');

        $options = array(
            'conditions' => array(
                'id_formation' => $id
            )
        );
        $millesime->delete_all( $options );
        $years = explode('/', trim($yearString));
        foreach ($years as $year) {
            $options = array(
                'id_formation' => $id,
                'year' => intval(trim($year))
            );
            $millesime->save( $options );
        }
    }
}
