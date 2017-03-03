<?php
/*
 * This file is part of the JETPULP wp_cridon project.
 *
 * Copyright (C) JETPULP
 */

class RoleManager
{
    protected static $defaultUserRoleValues = array(
        CONST_OFFICES_CATEG => array(
            CONST_NOTAIRE_DEFAULT => array(
                CONST_VEILLES_ROLE => true,
                CONST_FLASH_ROLE => true,
                CONST_CAHIERS_ROLE => true,
                CONST_QUESTIONTELEPHONIQUES_ROLE => false,
                CONST_QUESTIONECRITES_ROLE => false,
                CONST_FACTURES_ROLE => false,
                CONST_REGLES_ROLE => false,
                CONST_CONSO_ROLE => false,
                CONST_COLLABORATEUR_TAB_ROLE => false,
                CONST_DROITS_COLLABORATEUR_ROLE => false,
                CONST_MODIFYOFFICE_ROLE => false,
                CONST_CRIDONLINESUBSCRIPTION_ROLE => false,
                CONST_SINEQUA_ROLE => true,
                CONST_CRIDONLINE_ROLE => true,
                CONST_DASHBOARD_ROLE => true,
                CONST_PRIVATEPAGES_ROLE => true,
            ),
            CONST_NOTAIRE_FONCTION => array(
                CONST_VEILLES_ROLE => true,
                CONST_FLASH_ROLE => true,
                CONST_CAHIERS_ROLE => true,
                CONST_QUESTIONTELEPHONIQUES_ROLE => true,
                CONST_QUESTIONECRITES_ROLE => true,
                CONST_FACTURES_ROLE => true,
                CONST_REGLES_ROLE => true,
                CONST_CONSO_ROLE => true,
                CONST_COLLABORATEUR_TAB_ROLE => true,
                CONST_DROITS_COLLABORATEUR_ROLE => true,
                CONST_MODIFYOFFICE_ROLE => true,
                CONST_CRIDONLINESUBSCRIPTION_ROLE => true,
                CONST_SINEQUA_ROLE => true,
                CONST_CRIDONLINE_ROLE => true,
                CONST_DASHBOARD_ROLE => true,
                CONST_PRIVATEPAGES_ROLE => true,
            ),
            CONST_NOTAIRE_ASSOCIE => array(
                CONST_VEILLES_ROLE => true,
                CONST_FLASH_ROLE => true,
                CONST_CAHIERS_ROLE => true,
                CONST_QUESTIONTELEPHONIQUES_ROLE => true,
                CONST_QUESTIONECRITES_ROLE => true,
                CONST_FACTURES_ROLE => true,
                CONST_REGLES_ROLE => true,
                CONST_CONSO_ROLE => true,
                CONST_COLLABORATEUR_TAB_ROLE => true,
                CONST_DROITS_COLLABORATEUR_ROLE => true,
                CONST_MODIFYOFFICE_ROLE => true,
                CONST_CRIDONLINESUBSCRIPTION_ROLE => true,
                CONST_SINEQUA_ROLE => true,
                CONST_CRIDONLINE_ROLE => true,
                CONST_DASHBOARD_ROLE => true,
                CONST_PRIVATEPAGES_ROLE => true,
            ),
            CONST_NOTAIRE_ASSOCIEE => array(
                CONST_VEILLES_ROLE => true,
                CONST_FLASH_ROLE => true,
                CONST_CAHIERS_ROLE => true,
                CONST_QUESTIONTELEPHONIQUES_ROLE => true,
                CONST_QUESTIONECRITES_ROLE => true,
                CONST_FACTURES_ROLE => true,
                CONST_REGLES_ROLE => true,
                CONST_CONSO_ROLE => true,
                CONST_COLLABORATEUR_TAB_ROLE => true,
                CONST_DROITS_COLLABORATEUR_ROLE => true,
                CONST_MODIFYOFFICE_ROLE => true,
                CONST_CRIDONLINESUBSCRIPTION_ROLE => true,
                CONST_SINEQUA_ROLE => true,
                CONST_CRIDONLINE_ROLE => true,
                CONST_DASHBOARD_ROLE => true,
                CONST_PRIVATEPAGES_ROLE => true,
            ),
            CONST_NOTAIRE_SALARIE => array(
                CONST_VEILLES_ROLE => true,
                CONST_FLASH_ROLE => true,
                CONST_CAHIERS_ROLE => true,
                CONST_QUESTIONTELEPHONIQUES_ROLE => false,
                CONST_QUESTIONECRITES_ROLE => true,
                CONST_FACTURES_ROLE => false,
                CONST_REGLES_ROLE => false,
                CONST_CONSO_ROLE => false,
                CONST_COLLABORATEUR_TAB_ROLE => false,
                CONST_DROITS_COLLABORATEUR_ROLE => false,
                CONST_MODIFYOFFICE_ROLE => false,
                CONST_CRIDONLINESUBSCRIPTION_ROLE => false,
                CONST_SINEQUA_ROLE => true,
                CONST_CRIDONLINE_ROLE => true,
                CONST_DASHBOARD_ROLE => true,
                CONST_PRIVATEPAGES_ROLE => true,
            ),
            CONST_NOTAIRE_SALARIEE => array(
                CONST_VEILLES_ROLE => true,
                CONST_FLASH_ROLE => true,
                CONST_CAHIERS_ROLE => true,
                CONST_QUESTIONTELEPHONIQUES_ROLE => false,
                CONST_QUESTIONECRITES_ROLE => true,
                CONST_FACTURES_ROLE => false,
                CONST_REGLES_ROLE => false,
                CONST_CONSO_ROLE => false,
                CONST_COLLABORATEUR_TAB_ROLE => false,
                CONST_DROITS_COLLABORATEUR_ROLE => false,
                CONST_MODIFYOFFICE_ROLE => false,
                CONST_CRIDONLINESUBSCRIPTION_ROLE => false,
                CONST_SINEQUA_ROLE => true,
                CONST_CRIDONLINE_ROLE => true,
                CONST_DASHBOARD_ROLE => true,
                CONST_PRIVATEPAGES_ROLE => true,
            ),
            CONST_NOTAIRE_GERANT => array(
                CONST_VEILLES_ROLE => true,
                CONST_FLASH_ROLE => true,
                CONST_CAHIERS_ROLE => true,
                CONST_QUESTIONTELEPHONIQUES_ROLE => true,
                CONST_QUESTIONECRITES_ROLE => true,
                CONST_FACTURES_ROLE => true,
                CONST_REGLES_ROLE => true,
                CONST_CONSO_ROLE => true,
                CONST_COLLABORATEUR_TAB_ROLE => true,
                CONST_DROITS_COLLABORATEUR_ROLE => true,
                CONST_MODIFYOFFICE_ROLE => true,
                CONST_CRIDONLINESUBSCRIPTION_ROLE => true,
                CONST_SINEQUA_ROLE => true,
                CONST_CRIDONLINE_ROLE => true,
                CONST_DASHBOARD_ROLE => true,
                CONST_PRIVATEPAGES_ROLE => true,
            ),
            CONST_NOTAIRE_GERANTE => array(
                CONST_VEILLES_ROLE => true,
                CONST_FLASH_ROLE => true,
                CONST_CAHIERS_ROLE => true,
                CONST_QUESTIONTELEPHONIQUES_ROLE => true,
                CONST_QUESTIONECRITES_ROLE => true,
                CONST_FACTURES_ROLE => true,
                CONST_REGLES_ROLE => true,
                CONST_CONSO_ROLE => true,
                CONST_COLLABORATEUR_TAB_ROLE => true,
                CONST_DROITS_COLLABORATEUR_ROLE => true,
                CONST_MODIFYOFFICE_ROLE => true,
                CONST_CRIDONLINESUBSCRIPTION_ROLE => true,
                CONST_SINEQUA_ROLE => true,
                CONST_CRIDONLINE_ROLE => true,
                CONST_DASHBOARD_ROLE => true,
                CONST_PRIVATEPAGES_ROLE => true,
            ),
            CONST_NOTAIRE_SUPLEANT => array(
                CONST_VEILLES_ROLE => true,
                CONST_FLASH_ROLE => true,
                CONST_CAHIERS_ROLE => true,
                CONST_QUESTIONTELEPHONIQUES_ROLE => true,
                CONST_QUESTIONECRITES_ROLE => true,
                CONST_FACTURES_ROLE => true,
                CONST_REGLES_ROLE => true,
                CONST_CONSO_ROLE => true,
                CONST_COLLABORATEUR_TAB_ROLE => true,
                CONST_DROITS_COLLABORATEUR_ROLE => true,
                CONST_MODIFYOFFICE_ROLE => true,
                CONST_CRIDONLINESUBSCRIPTION_ROLE => true,
                CONST_SINEQUA_ROLE => true,
                CONST_CRIDONLINE_ROLE => true,
                CONST_DASHBOARD_ROLE => true,
                CONST_PRIVATEPAGES_ROLE => true,
            ),
            CONST_NOTAIRE_SUPLEANTE => array(
                CONST_VEILLES_ROLE => true,
                CONST_FLASH_ROLE => true,
                CONST_CAHIERS_ROLE => true,
                CONST_QUESTIONTELEPHONIQUES_ROLE => true,
                CONST_QUESTIONECRITES_ROLE => true,
                CONST_FACTURES_ROLE => true,
                CONST_REGLES_ROLE => true,
                CONST_CONSO_ROLE => true,
                CONST_COLLABORATEUR_TAB_ROLE => true,
                CONST_DROITS_COLLABORATEUR_ROLE => true,
                CONST_MODIFYOFFICE_ROLE => true,
                CONST_CRIDONLINESUBSCRIPTION_ROLE => true,
                CONST_SINEQUA_ROLE => true,
                CONST_CRIDONLINE_ROLE => true,
                CONST_DASHBOARD_ROLE => true,
                CONST_PRIVATEPAGES_ROLE => true,
            ),
            CONST_NOTAIRE_ADMIN => array(
                CONST_VEILLES_ROLE => true,
                CONST_FLASH_ROLE => true,
                CONST_CAHIERS_ROLE => true,
                CONST_QUESTIONTELEPHONIQUES_ROLE => true,
                CONST_QUESTIONECRITES_ROLE => true,
                CONST_FACTURES_ROLE => true,
                CONST_REGLES_ROLE => true,
                CONST_CONSO_ROLE => true,
                CONST_COLLABORATEUR_TAB_ROLE => true,
                CONST_DROITS_COLLABORATEUR_ROLE => true,
                CONST_MODIFYOFFICE_ROLE => true,
                CONST_CRIDONLINESUBSCRIPTION_ROLE => true,
                CONST_SINEQUA_ROLE => true,
                CONST_CRIDONLINE_ROLE => true,
                CONST_DASHBOARD_ROLE => true,
                CONST_PRIVATEPAGES_ROLE => true,
            ),
            CONST_NOTAIRE_COLLABORATEUR => array(
                CONST_VEILLES_ROLE => true,
                CONST_FLASH_ROLE => true,
                CONST_CAHIERS_ROLE => true,
                CONST_QUESTIONTELEPHONIQUES_ROLE => false,
                CONST_QUESTIONECRITES_ROLE => false,
                CONST_FACTURES_ROLE => false,
                CONST_REGLES_ROLE => false,
                CONST_CONSO_ROLE => false,
                CONST_COLLABORATEUR_TAB_ROLE => false,
                CONST_DROITS_COLLABORATEUR_ROLE => false,
                CONST_MODIFYOFFICE_ROLE => false,
                CONST_CRIDONLINESUBSCRIPTION_ROLE => false,
                CONST_SINEQUA_ROLE => true,
                CONST_CRIDONLINE_ROLE => true,
                CONST_DASHBOARD_ROLE => true,
                CONST_PRIVATEPAGES_ROLE => true,
            ),
        ),
        CONST_ORGANISMES_CATEG => array(
            CONST_NOTAIRE_DEFAULT => array(
                CONST_VEILLES_ROLE => true,
                CONST_FLASH_ROLE => true,
                CONST_CAHIERS_ROLE => true,
                CONST_QUESTIONTELEPHONIQUES_ROLE => false,
                CONST_QUESTIONECRITES_ROLE => true,
                CONST_FACTURES_ROLE => true,
                CONST_REGLES_ROLE => false,
                CONST_CONSO_ROLE => false,
                CONST_COLLABORATEUR_TAB_ROLE => true,
                CONST_DROITS_COLLABORATEUR_ROLE => false,
                CONST_MODIFYOFFICE_ROLE => true,
                CONST_CRIDONLINESUBSCRIPTION_ROLE => false,
                CONST_SINEQUA_ROLE => true,
                CONST_CRIDONLINE_ROLE => true,
                CONST_DASHBOARD_ROLE => false,
                CONST_PRIVATEPAGES_ROLE => true,
            ),
            CONST_NOTAIRE_COLLABORATEUR => array(
                CONST_VEILLES_ROLE => true,
                CONST_FLASH_ROLE => true,
                CONST_CAHIERS_ROLE => true,
                CONST_QUESTIONTELEPHONIQUES_ROLE => false,
                CONST_QUESTIONECRITES_ROLE => true,
                CONST_FACTURES_ROLE => true,
                CONST_REGLES_ROLE => false,
                CONST_CONSO_ROLE => false,
                CONST_COLLABORATEUR_TAB_ROLE => true,
                CONST_DROITS_COLLABORATEUR_ROLE => false,
                CONST_MODIFYOFFICE_ROLE => true,
                CONST_CRIDONLINESUBSCRIPTION_ROLE => false,
                CONST_SINEQUA_ROLE => true,
                CONST_CRIDONLINE_ROLE => true,
                CONST_DASHBOARD_ROLE => false,
                CONST_PRIVATEPAGES_ROLE => true,
            ),
        ),
        CONST_CLIENTDIVERS_CATEG => array(
            CONST_NOTAIRE_DEFAULT => array(
                CONST_VEILLES_ROLE => true,
                CONST_FLASH_ROLE => true,
                CONST_CAHIERS_ROLE => true,
                CONST_QUESTIONTELEPHONIQUES_ROLE => false,
                CONST_QUESTIONECRITES_ROLE => true,
                CONST_FACTURES_ROLE => false,
                CONST_REGLES_ROLE => false,
                CONST_CONSO_ROLE => false,
                CONST_COLLABORATEUR_TAB_ROLE => false,
                CONST_DROITS_COLLABORATEUR_ROLE => false,
                CONST_MODIFYOFFICE_ROLE => true,
                CONST_CRIDONLINESUBSCRIPTION_ROLE => false,
                CONST_SINEQUA_ROLE => false,
                CONST_CRIDONLINE_ROLE => false,
                CONST_DASHBOARD_ROLE => false,
                CONST_PRIVATEPAGES_ROLE => true,
            ),
            CONST_NOTAIRE_HONORAIRE => array(
                CONST_VEILLES_ROLE => true,
                CONST_FLASH_ROLE => true,
                CONST_CAHIERS_ROLE => true,
                CONST_QUESTIONTELEPHONIQUES_ROLE => false,
                CONST_QUESTIONECRITES_ROLE => true,
                CONST_FACTURES_ROLE => false,
                CONST_REGLES_ROLE => false,
                CONST_CONSO_ROLE => false,
                CONST_COLLABORATEUR_TAB_ROLE => false,
                CONST_DROITS_COLLABORATEUR_ROLE => false,
                CONST_MODIFYOFFICE_ROLE => true,
                CONST_CRIDONLINESUBSCRIPTION_ROLE => false,
                CONST_SINEQUA_ROLE => false,
                CONST_CRIDONLINE_ROLE => true,
                CONST_DASHBOARD_ROLE => false,
                CONST_PRIVATEPAGES_ROLE => true,
            ),
            CONST_NOTAIRE_UNIVERSITAIRE => array(
                CONST_VEILLES_ROLE => true,
                CONST_FLASH_ROLE => true,
                CONST_CAHIERS_ROLE => true,
                CONST_QUESTIONTELEPHONIQUES_ROLE => false,
                CONST_QUESTIONECRITES_ROLE => false,
                CONST_FACTURES_ROLE => true,
                CONST_REGLES_ROLE => false,
                CONST_CONSO_ROLE => false,
                CONST_COLLABORATEUR_TAB_ROLE => false,
                CONST_DROITS_COLLABORATEUR_ROLE => false,
                CONST_MODIFYOFFICE_ROLE => true,
                CONST_CRIDONLINESUBSCRIPTION_ROLE => false,
                CONST_SINEQUA_ROLE => false,
                CONST_CRIDONLINE_ROLE => true,
                CONST_DASHBOARD_ROLE => false,
                CONST_PRIVATEPAGES_ROLE => true,
            ),
            CONST_NOTAIRE_COLLABORATEUR => array(
                CONST_VEILLES_ROLE => true,
                CONST_FLASH_ROLE => true,
                CONST_CAHIERS_ROLE => true,
                CONST_QUESTIONTELEPHONIQUES_ROLE => false,
                CONST_QUESTIONECRITES_ROLE => false,
                CONST_FACTURES_ROLE => true,
                CONST_REGLES_ROLE => false,
                CONST_CONSO_ROLE => false,
                CONST_COLLABORATEUR_TAB_ROLE => false,
                CONST_DROITS_COLLABORATEUR_ROLE => false,
                CONST_MODIFYOFFICE_ROLE => true,
                CONST_CRIDONLINESUBSCRIPTION_ROLE => false,
                CONST_SINEQUA_ROLE => false,
                CONST_CRIDONLINE_ROLE => true,
                CONST_DASHBOARD_ROLE => false,
                CONST_PRIVATEPAGES_ROLE => true,
            ),
        ),
    );

    protected static $defaultCollaborateurRoleValues = array(
        CONST_OFFICES_CATEG => array(
            CONST_COLLAB_COMPTABLE => array(
                CONST_VEILLES_ROLE => true,
                CONST_FLASH_ROLE => true,
                CONST_CAHIERS_ROLE => true,
                CONST_QUESTIONTELEPHONIQUES_ROLE => false,
                CONST_QUESTIONECRITES_ROLE => false,
                CONST_FACTURES_ROLE => true,
                CONST_REGLES_ROLE => true,
                CONST_CONSO_ROLE => true,
                CONST_COLLABORATEUR_TAB_ROLE => false,
                CONST_DROITS_COLLABORATEUR_ROLE => false,
                CONST_MODIFYOFFICE_ROLE => false,
                CONST_CRIDONLINESUBSCRIPTION_ROLE => false,
                CONST_SINEQUA_ROLE => true,
                CONST_CRIDONLINE_ROLE => true,
                CONST_DASHBOARD_ROLE => true,
                CONST_PRIVATEPAGES_ROLE => true,
            ),
            CONST_COLLAB_COMPTABLE_TAXATEUR => array(
                CONST_VEILLES_ROLE => true,
                CONST_FLASH_ROLE => true,
                CONST_CAHIERS_ROLE => true,
                CONST_QUESTIONTELEPHONIQUES_ROLE => false,
                CONST_QUESTIONECRITES_ROLE => false,
                CONST_FACTURES_ROLE => true,
                CONST_REGLES_ROLE => true,
                CONST_CONSO_ROLE => true,
                CONST_COLLABORATEUR_TAB_ROLE => false,
                CONST_DROITS_COLLABORATEUR_ROLE => false,
                CONST_MODIFYOFFICE_ROLE => false,
                CONST_CRIDONLINESUBSCRIPTION_ROLE => false,
                CONST_SINEQUA_ROLE => true,
                CONST_CRIDONLINE_ROLE => true,
                CONST_DASHBOARD_ROLE => true,
                CONST_PRIVATEPAGES_ROLE => true,
            ),
        ),
        CONST_ORGANISMES_CATEG => array(),
        CONST_CLIENTDIVERS_CATEG => array(),
    );

    protected static $frontManagableRoles = array(
        CONST_DASHBOARD_ROLE => 'Accès au tableau de bord',
        CONST_PRIVATEPAGES_ROLE => 'Accès aux pages sécurisées',
        CONST_FACTURES_ROLE => 'Accès au détail des factures',
        CONST_REGLES_ROLE => 'Accès aux règles de facturation',
        CONST_CONSO_ROLE => 'Accès aux relevés de consommation',
        CONST_QUESTIONECRITES_ROLE => 'Poser des questions écrites',
        CONST_QUESTIONTELEPHONIQUES_ROLE => 'Poser des questions téléphoniques',
        CONST_VEILLES_ROLE => 'Accès au détail des veilles juridiques',
        CONST_FLASH_ROLE => 'Accès au détail des flash info',
        CONST_CAHIERS_ROLE => 'Accès au détail des Cahiers du Cridon',
        CONST_SINEQUA_ROLE => 'Accéder aux bases de connaissances du Cridon Lyon',
        CONST_CRIDONLINE_ROLE => 'Accéder aux bases de connaissances Crid\'Online',
        CONST_MODIFYOFFICE_ROLE => "Modifier les informations de l'étude",
        CONST_CRIDONLINESUBSCRIPTION_ROLE => "Souscription à l'offre CRID'ONLINE",
    );


    /**
     * Get role label by role, or all labels if no role is provided
     *
     * @param string $role
     * @return array|string
     */
    public static function getRoleLabel($role = null) {
        if (null === $role) {
            return static::$frontManagableRoles;
        }
        return static::$frontManagableRoles[$role];
    }

    public static function getRoles($type = CONST_OFFICES_CATEG, $fonction = CONST_NOTAIRE_DEFAULT, $fonction_collaborateur = null)
    {
        if (CONST_NOTAIRE_COLLABORATEUR == $fonction && !empty($fonction_collaborateur)) {
            return isset(static::$defaultCollaborateurRoleValues[$type][$fonction_collaborateur]) ? static::$defaultCollaborateurRoleValues[$type][$fonction_collaborateur] : static::$defaultUserRoleValues[$type][$fonction];
        } else {
            return isset(static::$defaultUserRoleValues[$type][$fonction]) ? static::$defaultUserRoleValues[$type][$fonction] : static::$defaultUserRoleValues[$type][CONST_NOTAIRE_DEFAULT];
        }
    }

    public static function getAllRoles($collaborateurs = false)
    {
        return $collaborateurs ? static::$defaultCollaborateurRoleValues : static::$defaultUserRoleValues;
    }

    /**
     * update roles for a user
     *
     * @param mixed $notaire
     * @return void
     */
    public static function majNotaireRole($notaire)
    {
        if (!isset($notaire->id_wp_user)) {
            // get user by notary_id
            $user = static::getAssociatedUserByNotaryId($notaire->id);
            $notaire->id_wp_user = $user->ID;
        } else {
            // get user by id
            $user = new WP_User($notaire->id_wp_user);
        }

        $type = isset($notaire->category) ? $notaire->category : CONST_OFFICES_CATEG;

        // user must be an instance of WP_User vs WP_Error
        if ($user instanceof WP_User) {
            $roles = array();
            if (Notaire::isCollaborateur($notaire)) {
                if(isset(static::$defaultCollaborateurRoleValues[$type][$notaire->id_fonction_collaborateur])) {
                    $roles = static::$defaultCollaborateurRoleValues[$type][$notaire->id_fonction_collaborateur];
                } else {
                    $roles = static::$defaultUserRoleValues[$type][CONST_NOTAIRE_COLLABORATEUR]; //Default
                }
            } else {
                if (isset(static::$defaultUserRoleValues[$type][$notaire->id_fonction])) {
                    $roles = static::$defaultUserRoleValues[$type][$notaire->id_fonction];
                } else {
                    $roles = static::$defaultUserRoleValues[$type][CONST_NOTAIRE_DEFAULT];
                }
            }
            foreach ($roles as $role => $enable) {
                if ($enable) {
                    $user->add_role($role);
                }
            }
        }
        static::disableAdminBar($notaire);
    }

    /**
     * Get associated user by notary id
     *
     * @param int $id
     * @return void|WP_User
     * @throws Exception
     */
    public static function getAssociatedUserByNotaryId($id)
    {
        // get notary data
        $notary = mvc_model('QueryBuilder')->findOne('notaire',
            array(
                'fields' => 'id, id_wp_user, crpcen',
                'conditions' => 'id = ' . $id,
            )
        );
        // get notary associated user
        if (is_object($notary) && $notary->id_wp_user) {
            $user = new WP_User($notary->id_wp_user);

            // check if user is a WP_user vs WP_error
            if ($user instanceof WP_User && is_array($user->roles)) {
                return $user;
            }
        }
        return;
    }

    /**
     * Disable admin bar for notaries
     *
     * @param mixed $notaries
     * @return void
     */
    public static function disableUserAdminBar($notaries)
    {
        if (is_array($notaries) && count($notaries) > 0) {
            foreach ($notaries as $notary) {
                static::disableAdminBar($notary);
            }
        } elseif(is_object($notaries)) {
            static::disableAdminBar($notaries);
        }
    }

    /**
     * @param object $notary
     * @throws Exception
     * @return void
     */
    protected static function disableAdminBar($notary)
    {
        // peut être que $notary->id_wp_user est encore null (cas de nouvelle insertion via bulk insert)
        // cette valeur sera mise à jour après execution bulk update via updateCriNotaireWpUserId
        if (!$notary->id_wp_user) {
            $notary = mvc_model('QueryBuilder')->findOne('notaire',
                array(
                    'fields'     => 'id_wp_user',
                    'conditions' => 'id = ' . $notary->id,
                )
            );
        }
        // insert or update user_meta
        update_user_meta($notary->id_wp_user, 'show_admin_bar_front', 'false');
    }

    /**
     * @param $mvcUser mixed representation of a Notaire object
     * @return array roles
     */
    public static function getUserRoles($mvcUser)
    {
        if (is_object($mvcUser) && $mvcUser->id_wp_user) {
            $user = new WP_User($mvcUser->id_wp_user);

            // check if user is a WP_user vs WP_error
            if ($user instanceof WP_User && is_array($user->roles)) {
                return $user->roles;
            }
        }
        return array();
    }

    /**
     * Rest all manageable user roles
     *
     * @param mixed $user
     * @return void
     */
    public static function resetUserRoles($user)
    {
        foreach (static::getRoleLabel() as $role => $label) {
            $user->remove_role($role);
        }
    }
}
