<?php

class Formation extends \App\Override\Model\CridonMvcModel
{
    use DocumentsHolderTrait;

    var $table = "{prefix}formation";
    var $includes = array('Post','Matiere', 'Session');
    var $belongs_to = array(
        'Post' => array('foreign_key' => 'post_id'),
        'Matiere' => array('foreign_key' => 'id_matiere')
    );
    var $has_many = array(
        'Session' => array(
            'foreign_key' => 'id'
        )
    );
    var $display_field = 'name';

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
}
