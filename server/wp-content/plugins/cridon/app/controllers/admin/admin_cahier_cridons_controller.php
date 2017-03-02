<?php

/**
 *
 * This file is part of project 
 *
 * File name : admin_cahier_cridons_controller.php
 * Project   : wp_cridon
 *
 * @author Etech
 * @contributor Fabrice MILA
 *
 */
// base admin ctrl
require_once 'base_admin_controller.php';

class AdminCahierCridonsController extends BaseAdminController {
    
    var $default_search_joins = array('Matiere','Post');
    /**
     *
     * @var array
     */
    var $default_searchable_fields = array(
        'id', 
        'Post.post_title',
        'Matiere.label'
    );
    var $default_columns = array(
        'id',
        'post' => array(
            'label'=> 'Titre' ,
            'value_method' => 'post_edit_link'
        ),
        'matiere' => array(
            'label'=>'Matière',
            'value_method' => 'matiere_edit_link'
        ),
        'Sommaire' => array(
            'label'=>'Sommaire',
            'value_method' => 'sommaire_edit_link'
        )
    );

    public function index() {
        $this->init_default_columns();
        $this->process_params_for_search();
        $collection = $this->model->paginate($this->params);
        $this->set('objects', $collection['objects']);
        $this->set_pagination($collection);
        //Load custom helper
        $this->load_helper('AdminPost');
    }
    public function add()
    {
        //Default
        $this->redirect('post-new.php?cridon_type='.$this->trim( $this->name ),301);
    }
    public function edit()
    {
        //Default
        $object = MvcObjectRegistry::get_object($this->model->name);
        $this->redirect( $this->samplePostEditUrl($object, $this),301);
    }

    public function post_edit_link($object)
    {      
        $aOptionList = array(
            '__name'    => 'post_title'
        );
        $this->prepareData($aOptionList, $object->post);
        return empty($object->post) ? null : '<a href="'.$this->postEditUrl($object, $this).'" title="Edit">'.$object->post->__name.'</a>';
    }
    private function trim( $str ){
        return str_replace('admin_', '', $str);
    }
    private function postEditUrl( $object,$controller ){
        return admin_url( 'post.php?post='.$object->post_id.'&action=edit&cridon_type='.$this->trim($controller->name) );
    }
    private function samplePostEditUrl( $object,$controller ){
        return 'post.php?post='.$object->post_id.'&action=edit&cridon_type='.$this->trim($controller->name);
    }
    private function prepareData($aOptionList, $aData)
    {
        if (is_array($aData) && count($aData) > 0) {
            foreach ($aData as $oData) {
                foreach ($aOptionList as $sKey => $sVal) {
                    $oData->$sKey = $oData->$sVal;
                }
            }
        } elseif(is_object($aData)) {
            foreach ($aOptionList as $sKey => $sVal) {
                $aData->$sKey = $aData->$sVal;
            }
        }
    }
    public function matiere_edit_link($object)
    {
        $aOptionList = array(
            '__name'    => 'label'
        );
        $this->prepareData($aOptionList, $object->matiere);
        return empty($object->matiere) ? Config::$defaultMatiere['name'] : HtmlHelper::admin_object_link($object->matiere, array('action' => 'edit'));
    }
    public function sommaire_edit_link($object)
    {
        if ($object->id_parent) {
            $aQueryOptions = array(
                'selects' => array('Post.post_title', 'CahierCridon.*'),
                'conditions' => array(
                    'CahierCridon.id' => $object->id_parent
                ),
                'joins' => array('Post')
            );
            $aParent = $this->model->find( $aQueryOptions );

            if (isset($aParent[0])) {
                return '<a href="'.$this->postEditUrl($aParent[0], $this).'" title="Edit">'.$aParent[0]->post->post_title.'</a>';
            }
        }
    }

    function email_sender()
    {
        if (!empty($_POST['cahier_cridon_parent_id']) &&
            ((!empty($_POST['test_email']) && $_POST['send_to'] === "email_test")
                || $_POST['send_to'] === "notaires"
            )
        ) {
            $id_parent = esc_sql($_POST['cahier_cridon_parent_id']);
            if (!empty($id_parent) && ctype_digit($id_parent)) {
                $cahiers = $this->model->get_parent_and_childs($id_parent);
                if (!empty ($cahiers['parent'])  && !empty($cahiers['childs'])) {
                    $vars = array(
                        'cahier_parent' => $cahiers['parent'][0],
                        'cahier_childs' => $cahiers['childs']
                    );

                    $message = CriRenderView('mail_cahiers_cridon', $vars, 'custom', false);
                    $headers = array('Content-Type: text/html; charset=UTF-8');

                    if ($_POST['send_to'] === "email_test" && !empty($_POST['test_email'])) {
                        $email = wp_mail($_POST['test_email'], Config::$mailSubjectCahierCridon, $message, $headers);
                        writeLog("Email de test : " . $_POST['test_email'] . "\n"
                            . "retour wp_mail : " . $email . "\n"
                            . "message" . "\n" . $message, "cahiercridonmailog.txt");
                        if ($email) {
                            $this->flash('notice', 'L\'email a été correctement envoyé à ' . $_POST['test_email'] . ' !');
                        }
                    } elseif ($_POST['send_to'] === "notaires") {
                        $env = getenv('ENV');
                        if (empty($env) || ($env !== 'PROD')){
                            if ($env === 'PREPROD') {
                                $dest = Config::$notificationAddressPreprod;
                            } else {
                                $dest = Config::$notificationAddressDev;
                            }
                            $email = wp_mail($dest, Config::$mailSubjectCahierCridon, $message, $headers);
                            writeLog("Email de test : " . $_POST['test_email'] . "\n"
                                . "retour wp_mail : " . $email . "\n"
                                . "message" . "\n" . $message, "cahiercridonmailog.txt");
                            if ($email) {
                                $this->flash('notice', 'L\'email a été correctement envoyé à ' . $dest . ' !');
                            }
                        } else {
                            $options = array(
                                'synonym' => 'n',
                                'join' => array(
                                    array(
                                        'table' => 'users u',
                                        'column' => ' n.id_wp_user = u.id'
                                    ),
                                    array(
                                        'table' => 'entite e',
                                        'column' => ' n.crpcen = e.crpcen'
                                    ),
                                ),
                                'conditions' => array(
                                    'u.user_status' => CONST_STATUS_ENABLED
                                )
                            );
                            $notaires = mvc_model('QueryBuilder')->findAll('notaire', $options, 'n.id');
                            $emails = array();
                            foreach ($notaires as $notaire) {
                                $user = new WP_User($notaire->id_wp_user);
                                $emailAddress = trim($notaire->email_adress);
                                if (mvc_model('Notaire')->userHasRole($user, CONST_CONNAISANCE_ROLE)) {
                                    if (!empty($emailAddress)) {
                                        $emails[] = $emailAddress;
                                    }
                                    $office_email = trim($notaire->office_email_adress_1);
                                    if (!empty($office_email)){
                                        $emails[] = $office_email;
                                    }
                                }
                            }
                            $emails_addresses = array_unique($emails);
                            writeLog("Emails : " . $emails_addresses . "\n"
                                . "message" . "\n" . $message, "cahiercridonmailog.txt");
                            foreach ($emails_addresses as $email_address) {
                                $email = wp_mail($email_address, Config::$mailSubjectCahierCridon, $message, $headers);
                                writeLog("retour wp_mail : " . $email . "\n", "cahiercridonmailog.txt");
                            }
                            $this->flash('notice', 'Les emails ont été envoyés aux notaires !');
                        }
                    }
                } else {
                    $this->flash('error', 'Le numéro de cahier cridon lyon n\'est pas correct !');
                }
            } else {
                $this->flash('error', 'Le numéro de cahier cridon lyon n\'est pas correct !');
            }
        }
    }
}
