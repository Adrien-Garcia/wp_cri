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
        // en-tete email
        $headers = array('Content-Type: text/html; charset=UTF-8');
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

        // destinataire non vide
        if (count($dest) > 0) {
            array_unique($dest);
            $vars    = array(
                'type'                  => CONST_FORMATION_PREINSCRIPTION,
                'date'                  => strftime('%d %b %Y',strtotime($session->date)),
                'name'                  => $session->formation->post->post_title,
                'organisme'             => strtoupper($session->organisme->name. ' ' . $session->organisme->city),
                'participants'          => $formationParticipants,
                'commentaire'           => $formationCommentaire,
                'notaire'               => array(
                    'crpcen'                => $notaire->crpcen,
                    'fname'                 => $notaire->first_name,
                    'lname'                 => $notaire->last_name,
                    'mail'                  => !empty($notaire->email_adress) ? $notaire->email_adress : '',
                ),
            );
            $message = CriRenderView('mail_notification_formation', $vars, 'custom', false);
            $messageAdmin = CriRenderView('mail_notification_admin_formation', $vars, 'custom', false);

            $env = getenv('ENV');
            if (empty($env) || ($env !== PROD)) {
                if ($env === 'PREPROD') {
                    $dest = Config::$notificationAddressPreprod;
                    $adminDest = Config::$notificationAddressPreprod;
                } else {
                    $dest = Config::$notificationAddressDev;
                    $adminDest = Config::$notificationAddressDev;
                }
            }

            /**
             * wp_mail : envoie mail destinataire multiple
             *
             * @see wp-includes/pluggable.php : 228
             * @param string|array $to Array or comma-separated list of email addresses to send message.
             */
            wp_mail($dest, Config::$mailSubjectFormationPreinscription. ' : ' .$session->formation->post->post_title, $message, $headers);
            wp_mail($adminDest, Config::$mailSubjectAdminFormationPreinscription. ' : ' .$session->formation->post->post_title, $messageAdmin, $headers);

        }
    }

    public function sendEmailGenerique($formationThematique, $formationCommentaire) {
        // en-tete email
        $headers = array('Content-Type: text/html; charset=UTF-8');
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

        // destinataire non vide
        if (count($dest) > 0) {
            array_unique($dest);
            $vars    = array(
                'type'                  => CONST_FORMATION_GENERIQUE,
                'name'                  => $formationThematique,
                'commentaire'           => $formationCommentaire,
                'notaire'               => array(
                    'crpcen'                => $notaire->crpcen,
                    'fname'                 => $notaire->first_name,
                    'lname'                 => $notaire->last_name,
                    'mail'                  => !empty($notaire->email_adress) ? $notaire->email_adress : '',
                ),
            );
            $message = CriRenderView('mail_notification_formation', $vars, 'custom', false);
            $messageAdmin = CriRenderView('mail_notification_admin_formation', $vars, 'custom', false);

            $env = getenv('ENV');
            if (empty($env) || ($env !== PROD)) {
                if ($env === 'PREPROD') {
                    $dest = Config::$notificationAddressPreprod;
                    $adminDest = Config::$notificationAddressPreprod;
                } else {
                    $dest = Config::$notificationAddressDev;
                    $adminDest = Config::$notificationAddressDev;
                }
            }

            /**
             * wp_mail : envoie mail destinataire multiple
             *
             * @see wp-includes/pluggable.php : 228
             * @param string|array $to Array or comma-separated list of email addresses to send message.
             */
            wp_mail($dest, Config::$mailSubjectFormationGenerique. ' : ' .$formationThematique, $message, $headers);
            wp_mail($adminDest, Config::$mailSubjectFormationGenerique. ' : ' .$formationThematique, $messageAdmin, $headers);

        }
    }

    public function sendEmailDemande ($formation, $formationParticipants, $formationCommentaire){
        // en-tete email
        $headers = array('Content-Type: text/html; charset=UTF-8');
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

        // destinataire non vide
        if (count($dest) > 0) {
            array_unique($dest);
            $vars    = array(
                'type'                  => CONST_FORMATION_DEMANDE,
                'name'                  => $formation->post->post_title,
                'participants'          => $formationParticipants,
                'commentaire'           => $formationCommentaire,
                'notaire'               => array(
                    'crpcen'                => $notaire->crpcen,
                    'fname'                 => $notaire->first_name,
                    'lname'                 => $notaire->last_name,
                    'mail'                  => !empty($notaire->email_adress) ? $notaire->email_adress : '',
                ),
            );
            $message = CriRenderView('mail_notification_formation', $vars, 'custom', false);
            $messageAdmin = CriRenderView('mail_notification_admin_formation', $vars, 'custom', false);

            $env = getenv('ENV');
            if (empty($env) || ($env !== PROD)) {
                if ($env === 'PREPROD') {
                    $dest = Config::$notificationAddressPreprod;
                    $adminDest = Config::$notificationAddressPreprod;
                } else {
                    $dest = Config::$notificationAddressDev;
                    $adminDest = Config::$notificationAddressDev;
                }
            }

            /**
             * wp_mail : envoie mail destinataire multiple
             *
             * @see wp-includes/pluggable.php : 228
             * @param string|array $to Array or comma-separated list of email addresses to send message.
             */
            wp_mail($dest, Config::$mailSubjectFormationDemande. ' : ' .$formation->post->post_title, $message, $headers);
            wp_mail($adminDest, Config::$mailSubjectFormationDemande. ' : ' .$formation->post->post_title, $messageAdmin, $headers);

        }
    }
}
