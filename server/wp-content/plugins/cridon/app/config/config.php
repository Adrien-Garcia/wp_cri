<?php

/**
 * Description of config
 *
 * @author ETECH
 * 
 */
class Config {

    // Les modèles associés à cri_posts
    // avec les noms des tables sans les préfixes
    public static $data = array(
        'veilles' => array(                    // Indice correspondant aux noms de fichier de controlleur
            'value'             => 'veilles',  // Nécessaire à la correspondance
            'name'              => 'veille',   // Nom de la table
            'model'             => 'Veille',   // Nom du MvcModel
            'controller'        => 'veilles',  // Contrôleur pour la redirection après ajout de post
            'action'            => 'index'     // Action associée au contrôleur
        ),
        'flashes' => array(
            'value'             => 'flashes',
            'name'              => 'flash',
            'model'             => 'Flash',
            'controller'        => 'flashes',
            'action'            => 'index'
        ),
        'vie_cridons' => array(
            'value'             => 'vie_cridons',
            'name'              => 'vie_cridon',
            'model'             => 'VieCridon',
            'controller'        => 'vie_cridons',
            'action'            => 'index'
        ),
        'formations' => array(
            'value'             => 'formations',
            'name'              => 'formation',
            'model'             => 'Formation',
            'controller'        => 'formations',
            'action'            => 'index'
        ),
        'cahier_cridons' => array(
            'value'             => 'cahier_cridons',
            'name'              => 'cahier_cridon',
            'model'             => 'CahierCridon',
            'controller'        => 'cahier_cridons',
            'action'            => 'index'
        )
    );

    // list of cridon_type using default post form
    public static $mvcWithPostForm = array('vie_cridons','cahier_cridons','flashes','formations','veilles');

    public static $titleMetabox = 'Matière';// Titre du metabox de catégorie veille en admin

    // Supported file in model Matiere ( picto )
    public static $supported_types = array('image/jpeg','image/bmp','image/x-windows-bmp','image/x-icon','image/jpeg','image/pjpeg','image/png');

    // Maximum width and height of image in model Matiere ( picto )
    // width x height
    public static $maxWidthHeight  = array(
        'width'  => 1400,
        'height' => 2000
    );

    //Default Matiere of model Veille
    public static $defaultMatiere = array(
        'id'     => 14,
        'name'   => 'Expertise transversale'
    );

    //Content qualified by a "Matière"
    public static $contentWithMatiere = array(
        'veilles',
        'flashes',
        'cahier_cridons',
        'formations'
    );

    //Content qualified by a "Matière"
    public static $contentWithParent = array(
        'cahier_cridons',
    );

    public static $titleParentMetabox = 'Cahier principal';

    // list of category not to be imported
    public static $notImportedList = array(CONST_CLIENTDIVERS_ROLE);

    //List of role Notaire
    public static $rolesNotaire = array( 'notaire',CONST_OFFICES_ROLE,CONST_ORGANISMES_ROLE,CONST_CLIENTDIVERS_ROLE );

    //Duration of token for webservice ( day unit )
    public static $tokenDuration = 1;

    // All model construct with WP_MVC with capabilities
    public static $capabitilies = array(
        //list
        'liste-vie_cridon-cridon',
        'liste-cahier_cridon-cridon',
        'liste-competence-cridon',
        'liste-document-cridon',
        'liste-flash-cridon',
        'liste-formation-cridon',
        'liste-matiere-cridon',
        'liste-notaire-cridon',
        'liste-question-cridon',
        'liste-support-cridon',
        'liste-veille-cridon'
    );

    // list of cridon_user_type using default user form
    public static $mvcWithUserForm = array('user_cridons');

    // list of persons who will receive e-mail error notification
    public static $emailNotificationError = array(
        'to' => array(
            'info@cridon-lyon.fr', //Client e-mail, only use it in production mode
        ),
        'cc' => array(
            'victor.albert@jetpulp.fr',
        ),
    );

    // list of persons who will receive e-mail notification for empty document
    public static $emailNotificationEmptyDocument = array(
        'to' => 'info@cridon-lyon.fr',//Client e-mail, only use it in production mode
        'administrators' => array(
            'victor.albert@jetpulp.fr'
        ),
        'secretaries'=> array(
            'victor.albert@jetpulp.fr'
        ),
        'subject' => 'Questions sans PDF',
        'message' => 'Les questions suivantes n\'ont pas encore de document associé : "%s"'
    );

    // list of notaire functions cannot access finances
    public static $canAccessFinance = array(
        CONST_NOTAIRE_FONCTION,
        CONST_NOTAIRE_ASSOCIE,
        CONST_NOTAIRE_ASSOCIEE,
        CONST_NOTAIRE_GERANT,
        CONST_NOTAIRE_GERANTE,
        CONST_NOTAIRE_SUPLEANT,
        CONST_NOTAIRE_SUPLEANTE,
        CONST_NOTAIRE_ADMIN,
    );

    // list of notaire functions used to calculate cridonline prices
    public static $functionsPricesCridonline = array(
        CONST_NOTAIRE_FONCTION,
        CONST_NOTAIRE_ASSOCIE,
        CONST_NOTAIRE_ASSOCIEE,
        CONST_NOTAIRE_SALARIE,
        CONST_NOTAIRE_SALARIEE,
        CONST_NOTAIRE_GERANT,
        CONST_NOTAIRE_GERANTE,
        CONST_NOTAIRE_SUPLEANT,
        CONST_NOTAIRE_SUPLEANTE,
        CONST_NOTAIRE_ADMIN,
    );

    public static $titleMetaboxDocument = 'Associer des documents';// Titre du metabox pour l'ajout de document

    // list of accepted question supports
    public static $acceptedSupports = array(
        CONST_SUPPORT_COURRIER_ID,
        CONST_SUPPORT_URG48H_ID,
        CONST_SUPPORT_URGWEEK_ID,
        CONST_SUPPORT_NON_FACTURE,
        CONST_SUPPORT_MES_DIANE
    );

    //Notification for published post
    public static $notificationForAllNotaries = array( 'flash','viecridon' );
    public static $notificationForSubscribersNotaries = array( 'veille' );
    public static $mailBodyNotification  = array(
        'subject'   => 'Publication: %s',
        'title'     => '%s',
        'date'      => '%s',
        'excerpt'   => '%s',
        'content'   => '%s',
        'matiere'   => '%s',
        'permalink' => '<a href="%s">%s</a>',
        'documents' => 'Les documents associés: ',
        'tags'      => ''
    );
    public static $mailBodyQuestionStatusChange = array(
        'numero_question'  => '%s',
        'resume'           => '%s',
        'content'          => '%s',
        'support'          => '%s',
        'juriste'          => '%s',
        'matiere'          => '%s',
        'competence'       => '%s',
        'creation_date'    => '%s',
        'affectation_date' => '%s',
        'wish_date'        => '%s',
        'date'             => '%s',
    );

    // Notification for posted question
    public static $mailSubjectQuestionStatusChange = array(
        1 => 'Question CRIDON LYON transmise',
        2 => 'Question CRIDON LYON numéro %s prise en compte',
        3 => 'Requalification de la question CRIDON LYON numéro %s',
        4 => 'Question CRIDON LYON numéro %s en cours de traitement',
        5 => 'Question CRIDON LYON numéro %s en attente de renseignements complémentaires',
        6 => 'Réponse à votre question CRIDON LYON numéro %s',
    );

    // Notification for posted question
    public static $mailContentQuestionStatusChange = array(
        1 => 'Votre question du %s en délai %s a bien été transmise.',
        2 => 'Nous avons bien reçu votre question numéro %s du %s en délai %s.',
        3 => 'Compte tenu de l’affluence des demandes, il ne nous sera pas possible de respecter le délai demandé de votre question numéro %s du %s. Nous enregistrons votre question en délai %s et faisons le nécessaire pour vous donner satisfaction.',
        4 => 'Votre question numéro %s en délai %s a été attribuée le %s à %s. Une réponse vous sera apportée au plus tard le %s.',
        5 => 'Merci de nous adresser les renseignements complémentaires demandés qui nous sont indispensables pour répondre à votre question numéro %s en délai %s du %s.',
        6 => 'La réponse à votre question numéro %s en délai %s du %s est disponible depuis votre espace privé.',

    );

    // Notification for posted question
    public static $mailSubjectCridonline = 'Confirmation de votre souscription à crid\'online';

    public static $notificationAddressPreprod = "victor.albert@jetpulp.fr";

    public static $notificationAddressCridon = "s.raby@cridon-lyon.fr";

    //GED Administration
    public static $GEDtxtIndexes = array(
        /* index Nom du fichier PDF  */
        'INDEX_NOMFICHIER'      => 0,
        /* index Valeur CAB */
        'INDEX_VALCAB'          => 1,
        /* index Nombre de pages correspondant à la question  */
        'INDEX_NBPAGE'          => 2,
        /* index Nombre de pages du document PDF */
        'INDEX_NBPAGEDOC'       => 3,
        /* index N° Question */
        'INDEX_NUMQUESTION'     => 4,
        /* index N° CRPCEN de l'étude */
        'INDEX_CRPCEN'          => 5,
        /* index Nom du notaire */
        'INDEX_NOMNOTAIRE'      => 6,
        /* index Prénom du notaire  */
        'INDEX_PRENOMNOTAIRE'   => 7,
        /* index Matière de la question */
        'INDEX_MATIERE'         => 8,
        /* index Support de la question */
        'INDEX_SUPPORT'         => 9,
        /* index Date d'affectation de la question */
        'INDEX_DATEAFFECTION'   => 10,
        /* index Date de réponse */
        'INDEX_DATEREPONSE'     => 11,
        /* index Nom du juriste principal */
        'INDEX_NOMJURISTE'      => 12,
        /* index Objet de la question */
        'INDEX_OBJET'           => 13,
        /* nombre de colonne présent dans le csv */
        'NB_COLONNES'        => 14,
    );


    //Begin Translation
    // Admin menu translation
    public static $sidebarAdminMenuActions = array(
        'add' => array(
            'label' => 'Ajouter'
        ),
        'delete' => array(
            'label' => 'Suppression'
        ),
        'edit' => array(
            'label' => 'Edition'
        )
    );
    public static $listOfControllersWpMvcOnSidebar = array(
        'cahier_cridons','competences','documents','flashes','formations','matieres','notaires',
        'questions','soldes','supports','user_cridons','veilles','vie_cridons'
    );
    public static $listOfControllersWithNoActionAdd = array(
        'notaires',
        'questions'
    );
    
    //Admin wp_mvc action translation
    public static $actionsWpmvcTranslation = array(
        'view'   => 'Voir',
        'edit'   => 'Editer',
        'delete' => 'Supprimer',
        'download' => 'Télécharger'
    );
    public static $msgConfirmDelete = 'Êtes-vous sur de vouloir supprimer';
    public static $btnTextAdmin = array(
        'add'    => 'Ajouter',
        'update' => 'Mettre à jour'
    );
    //Titre sur les formulaires d'édition et d'ajout
    public static $titleAdminForm  = array(
        'competence' => array(
            'add'    => 'Ajout de compétence',
            'edit'   => 'Edition de compétence'
        ),
        'document' => array(
            'add'    => 'Ajout d\'un document sécurisé du Cridon',
            'edit'   => 'Modification d\'un document sécurisé du Cridon'
        ),
        'matiere' => array(
            'add'    => 'Ajout d\'une matière',
            'edit'   => 'Modification d\'une matière'
        ),
        'support' => array(
            'add'    => 'Ajout d\'un support',
            'edit'   => 'Modification d\'un support'
        ),
        'solde' => array(
            'add'    => 'Ajout d\'un solde',
            'edit'   => 'Modification d\'un solde'
        ),
        'question' => array(
            'add'    => 'Ajout d\'une question',
            'edit'   => 'Modification d\'une question'
        )
    );
    public static $titleFieldAdminForm = array(
        'label'       => 'Libellé',
        'code'        => 'Code',
        'short_label' => 'Libellé court',
        'displayed'   => 'Affiché sur le site ?',
        'label_front' => 'Libellé en front',
        'value'       => 'Valeur',
        'description' => 'Description',
        'client_number' => 'Numéro client',
        'quota'         => 'Quota',
        'type_support'  => 'Type du support',
        'date_arret'    => 'Date d\'arrêt',
        'question'      => 'Sur les questions ?'
    );
    //End translation

    //Public download URL
    public static $confPublicDownloadURL = array(
        'pattern' => '/documents\/public\/([0-9]+)/',//Pattern à utilisé pour un test preg_match
        'url'     => 'documents/public/'//Sera ajouté à l'encodage, l'id sera ajouté dynamiquement (ex:documents/public/1)
    );
    //End Public download URL
    
    //Access documents
    
    //Liste des actus dont les téléchargements de document est à restreindre au notaire connecté
    public static $accessDowloadDocument = array(
        'flash','veille'//correspond au champ type de la table cri_document
    );
    //End access

    // Content qualified by a "Custom Date"
    public static $contentWithCustomDate = array(
        'formations',
    );
    public static $dateTitleMetabox = 'Date de formation';// Titre du metabox date de formation

    public static $contentWithAddress = array(
        'formations',
    );
    // Titre des metabox - adresse de formation
    public static $addressTitleMetabox = array(
        'address' => 'Adresse de la formation',
        'postal_code' => 'Code postal de la formation',
        'town' => 'Ville de la formation'
    ) ;

    //Label des affectations sur les questions
    public static $labelAffection = array(
        1 => 'Question transmise',
        2 => 'En cours de traitement',
        3 => 'En attente de renseignements complémentaires',
        4 => 'Question répondue'
    );
    //End label

    // admins Cridon custom capabilities
    public static $authorizedCapsForAdminsCridon = array(
        'read_private_posts',
        'read_private_pages',
    );

    // breadcrumb wpmvc model title
    // key must be match with controller params name
    public static $breadcrumbModelParams = array(
        'veilles'           => 'Veilles',
        'flashes'           => 'Flash infos',
        'cahier_cridons'    => 'Les cahiers du CRIDON',
        'formations'        => 'Formation',
        'vie_cridons'       => 'Vie du CRIDON',
        'matieres'          => 'Matiere',
    );

    //RSS
    public static $rss = array(
        'title'         => 'CRIDON LYON - Veilles juridiques',//all
        'title_mat'     => 'CRIDON LYON - Veille %s',//filtered
        'description'   => ''
    );
    //End RSS

    //Listing veille
    public static $listingVeille = array(
        'h1'                => 'Veille juridique',
        'meta_title'        => 'CRIDON Lyon: la veille juridique et l\'actualité des notaires',
        'meta_description'  => 'CRIDON Lyon vous accompagne au coeur de l’actualité juridique : droit international, fiscalité, droit social, droit de la famille...'
    );
    //End listing

    // manually set list of authorized capabilities for notary (no roles were associated to notary by default)
    public static $authorizedCapsForNotary = array(
        'read_private_posts',
        'read_private_pages',
    );

    // question pending status
    public static $questionPendingStatus = array(1,2,3);

    // Notification for password changed
    public static $mailPasswordChange = array(
        'subject' => 'Changement du mot de passe de %s',
    );

    // Content qualified by a "Niveau"
    public static $contentWithLevel = array(
        'veilles',
    );
    // level meta_box title
    public static $titleLevelMetabox = 'Niveau de %s';
    // list of level
    public static $listOfLevel = array(
        'Niveau 1' => 1,
        'Niveau 2' => 2,
        'Niveau 3' => 3,
    );

    /**
     * @var array list of type to be restricted by level
     */
    public static $restrictedDownloadByTypeLevel = array(
        'veille'
    );

    public static $modelWithIdDocImplemented = array(
        'Veille'
    );

    public static $pricesLevelsVeilles = array(
        '1' => array(
            '5' => 0,
            '2' => 0,
            '1' => 0,
        ),
        '2' => array(
            '5' => 7900,
            '2' => 4800,
            '1' => 2500,
        ),
        '3' => array (
            '5' => 9900,
            '2' => 5900,
            '1' => 3500,
        )
    );

    public static $motiveImmediateUpdate = array(2,3,4);

    // Notification for posted question
    public static $mailBodyQuestionConfirmation  = array(
        'subject'   => 'Prise en compte de votre question sur le site'
    );

    /**
     * @var array list of notary "function" allowed to edit profil, show office members
     */
    public static $allowedNotaryFunction = array(1, 2, 3, 6, 7, 8, 9, 10);

    /**
     * @var array list of notary collaborator comptable "id_function_collaborateur"
     * 1 : Comptable
     * 2 : Comptable taxateur
     */
    public static $notaryFunctionCollaboratorComptableId = array(1, 2);

    /**
     * @var array list of excepted actions for redirect 301
     */
    public static $exceptedActionForRedirect301 = array(
        'deletecollaborateur',
    );

    /**
     * @var array list of protected pages allowed only for notaries with a fonction inside $canAccessFinance
     */
    public static $protected_pages = array(
        'facturation',
        'cridonline',
        'collaborateur'
    );

    public static $authCridonOnline = array(
        1 => 'tvwYZMJ3rqrxmIAFKrwMy0x7AX',
        2 => 'tbxZABKYrprvmIZMJ7wKykwTZH',
        3 => 'ykwYZWJYrirnmIAGKTwqywxYZK',
    );
    /**
     * @var array list of notary roles : the keys must be match of the list defined in const.inc.php
     */
    public static $notaryRoles = array(
        CONST_FINANCE_ROLE                 => 'Accès aux pages "compta" (finances, factures, relevée de consommation)',
        CONST_CONNAISANCE_ROLE             => 'Accès aux bases de connaissance', // par tout le monde
        CONST_QUESTIONECRITES_ROLE         => 'Poser des questions écrites',
        CONST_QUESTIONTELEPHONIQUES_ROLE   => 'Poser des questions téléphoniques',
    );


    /**
     * @var array list of notary roles by function : the keys must be match of the list defined in const.inc.php
     */
    public static $notaryRolesByFunction = array(
        'notaries' => array(
            CONST_NOTAIRE_FONCTION => array(
                CONST_FINANCE_ROLE,
                CONST_CONNAISANCE_ROLE,
                CONST_QUESTIONECRITES_ROLE,
                CONST_QUESTIONTELEPHONIQUES_ROLE,
            ),
            CONST_NOTAIRE_ASSOCIE => array(
                CONST_FINANCE_ROLE,
                CONST_CONNAISANCE_ROLE,
                CONST_QUESTIONECRITES_ROLE,
                CONST_QUESTIONTELEPHONIQUES_ROLE,
            ),
            CONST_NOTAIRE_SALARIE => array(
                CONST_CONNAISANCE_ROLE,
                CONST_QUESTIONECRITES_ROLE,
            ),
            CONST_NOTAIRE_SALARIEE => array(
                CONST_CONNAISANCE_ROLE,
                CONST_QUESTIONECRITES_ROLE,
            ),
            CONST_NOTAIRE_ASSOCIEE => array(
                CONST_FINANCE_ROLE,
                CONST_CONNAISANCE_ROLE,
                CONST_QUESTIONECRITES_ROLE,
                CONST_QUESTIONTELEPHONIQUES_ROLE,
            ),
            CONST_NOTAIRE_ASSOCIEE => array(
                CONST_FINANCE_ROLE,
                CONST_CONNAISANCE_ROLE,
                CONST_QUESTIONECRITES_ROLE,
                CONST_QUESTIONTELEPHONIQUES_ROLE,
            ),
            CONST_NOTAIRE_GERANT => array(
                CONST_FINANCE_ROLE,
                CONST_CONNAISANCE_ROLE,
                CONST_QUESTIONECRITES_ROLE,
                CONST_QUESTIONTELEPHONIQUES_ROLE,
            ),
            CONST_NOTAIRE_GERANTE => array(
                CONST_FINANCE_ROLE,
                CONST_CONNAISANCE_ROLE,
                CONST_QUESTIONECRITES_ROLE,
                CONST_QUESTIONTELEPHONIQUES_ROLE,
            ),
            CONST_NOTAIRE_SUPLEANT => array(
                CONST_FINANCE_ROLE,
                CONST_CONNAISANCE_ROLE,
                CONST_QUESTIONECRITES_ROLE,
                CONST_QUESTIONTELEPHONIQUES_ROLE,
            ),
            CONST_NOTAIRE_SUPLEANTE => array(
                CONST_FINANCE_ROLE,
                CONST_CONNAISANCE_ROLE,
                CONST_QUESTIONECRITES_ROLE,
                CONST_QUESTIONTELEPHONIQUES_ROLE,
            ),
            CONST_NOTAIRE_ADMIN => array(
                CONST_FINANCE_ROLE,
                CONST_CONNAISANCE_ROLE,
                CONST_QUESTIONECRITES_ROLE,
                CONST_QUESTIONTELEPHONIQUES_ROLE,
            ),
            CONST_NOTAIRE_PRESIDENT_CHAMBRE => array(
                CONST_FINANCE_ROLE,
                CONST_CONNAISANCE_ROLE,
                CONST_QUESTIONECRITES_ROLE,
                CONST_QUESTIONTELEPHONIQUES_ROLE,
            ),
            CONST_NOTAIRE_PRESIDENT_CONSEIL => array(
                CONST_FINANCE_ROLE,
                CONST_CONNAISANCE_ROLE,
                CONST_QUESTIONECRITES_ROLE,
                CONST_QUESTIONTELEPHONIQUES_ROLE,
            ),
            CONST_NOTAIRE_DELEGUE_CRIDON => array(
                CONST_FINANCE_ROLE,
                CONST_CONNAISANCE_ROLE,
                CONST_QUESTIONECRITES_ROLE,
                CONST_QUESTIONTELEPHONIQUES_ROLE,
            ),
            CONST_NOTAIRE_DIRECTEUR => array(
                CONST_FINANCE_ROLE,
                CONST_CONNAISANCE_ROLE,
                CONST_QUESTIONECRITES_ROLE,
                CONST_QUESTIONTELEPHONIQUES_ROLE,
            ),
            CONST_NOTAIRE_DIRECTEUR_GENERAL => array(
                CONST_FINANCE_ROLE,
                CONST_CONNAISANCE_ROLE,
                CONST_QUESTIONECRITES_ROLE,
                CONST_QUESTIONTELEPHONIQUES_ROLE,
            ),
            CONST_NOTAIRE_DIRECTEUR_DEPARTEMET => array(
                CONST_FINANCE_ROLE,
                CONST_CONNAISANCE_ROLE,
                CONST_QUESTIONECRITES_ROLE,
                CONST_QUESTIONTELEPHONIQUES_ROLE,
            ),
            CONST_NOTAIRE_CONSEIL_JUR => array(
                CONST_FINANCE_ROLE,
                CONST_CONNAISANCE_ROLE,
                CONST_QUESTIONECRITES_ROLE,
                CONST_QUESTIONTELEPHONIQUES_ROLE,
            ),
            CONST_NOTAIRE_ASSISTANT => array(
                CONST_FINANCE_ROLE,
                CONST_CONNAISANCE_ROLE,
                CONST_QUESTIONECRITES_ROLE,
                CONST_QUESTIONTELEPHONIQUES_ROLE,
            ),
            CONST_NOTAIRE_ASSISTANTE => array(
                CONST_FINANCE_ROLE,
                CONST_CONNAISANCE_ROLE,
                CONST_QUESTIONECRITES_ROLE,
                CONST_QUESTIONTELEPHONIQUES_ROLE,
            ),
            CONST_NOTAIRE_HONORAIRE => array(
                CONST_FINANCE_ROLE,
                CONST_CONNAISANCE_ROLE,
                CONST_QUESTIONECRITES_ROLE,
                CONST_QUESTIONTELEPHONIQUES_ROLE,
            ),
            CONST_NOTAIRE_SG => array(
                CONST_FINANCE_ROLE,
                CONST_CONNAISANCE_ROLE,
                CONST_QUESTIONECRITES_ROLE,
                CONST_QUESTIONTELEPHONIQUES_ROLE,
            ),
            CONST_NOTAIRE_SECRETAIRE => array(
                CONST_FINANCE_ROLE,
                CONST_CONNAISANCE_ROLE,
                CONST_QUESTIONECRITES_ROLE,
                CONST_QUESTIONTELEPHONIQUES_ROLE,
            ),
            CONST_NOTAIRE_SECOND_RAPORTEUR => array(
                CONST_FINANCE_ROLE,
                CONST_CONNAISANCE_ROLE,
                CONST_QUESTIONECRITES_ROLE,
                CONST_QUESTIONTELEPHONIQUES_ROLE,
            ),
            CONST_NOTAIRE_PROF_DROIT => array(
                CONST_FINANCE_ROLE,
                CONST_CONNAISANCE_ROLE,
                CONST_QUESTIONECRITES_ROLE,
                CONST_QUESTIONTELEPHONIQUES_ROLE,
            ),
            CONST_NOTAIRE_TRESORIER => array(
                CONST_FINANCE_ROLE,
                CONST_CONNAISANCE_ROLE,
                CONST_QUESTIONECRITES_ROLE,
                CONST_QUESTIONTELEPHONIQUES_ROLE,
            ),
            CONST_NOTAIRE_CHARGE_DVP => array(
                CONST_FINANCE_ROLE,
                CONST_CONNAISANCE_ROLE,
                CONST_QUESTIONECRITES_ROLE,
                CONST_QUESTIONTELEPHONIQUES_ROLE,
            ),
            CONST_NOTAIRE_GEOMETRE => array(
                CONST_FINANCE_ROLE,
                CONST_CONNAISANCE_ROLE,
                CONST_QUESTIONECRITES_ROLE,
                CONST_QUESTIONTELEPHONIQUES_ROLE,
            ),
        ),
        'collaborators' => array(
            CONST_COLLAB_COMPTABLE => array(
                CONST_FINANCE_ROLE,
                CONST_CONNAISANCE_ROLE,
            ),
            CONST_COLLAB_COMPTABLE_TAXATEUR => array(
                CONST_FINANCE_ROLE,
                CONST_CONNAISANCE_ROLE,
            ),
            CONST_COLLAB_CLERC => array(
                CONST_CONNAISANCE_ROLE,
            ),
            CONST_COLLAB_NEGOCIATEUR => array(
                CONST_CONNAISANCE_ROLE,
            ),
            CONST_COLLAB_ASSISTANT => array(
                CONST_CONNAISANCE_ROLE,
                CONST_QUESTIONECRITES_ROLE,
            ),
            CONST_COLLAB_STAGIAIRE => array(
                CONST_CONNAISANCE_ROLE,
            ),
            CONST_COLLAB_EMPLOYE_ACCUEIL => array(
                CONST_CONNAISANCE_ROLE,
            ),
            CONST_COLLAB_SECRETAIRE => array(
                CONST_CONNAISANCE_ROLE,
            ),
            CONST_COLLAB_CADRE => array(
                CONST_CONNAISANCE_ROLE,
            ),
            CONST_COLLAB_CADRE => array(
                CONST_CONNAISANCE_ROLE,
            ),
            CONST_COLLAB_SECRETAIRE_ASSIST => array(
                CONST_CONNAISANCE_ROLE,
            ),
            CONST_COLLAB_TECHNICIEN => array(
                CONST_CONNAISANCE_ROLE,
            ),
        ),
    );

    public static $addableFunctions = array(
        CONST_NOTAIRE_SALARIE,
        CONST_NOTAIRE_SALARIEE,
        CONST_NOTAIRE_COLLABORATEUR
    );

    /**
     * @var array list of actions specific to collaborateur tab
     */
    public static $collaborateurActions = array(
        CONST_CREATE_USER,
        CONST_MODIFY_USER
    );

    /**
     * @var array : liste des autres categories de notaires sans etude officielle
     */
    public static $notaryNoDefaultOffice = array(
        CONST_ORGANISMES_CATEG,
        CONST_CLIENTDIVERS_CATEG,
    );

    /**
     * Selon la regle de nommage des fichiers factures à importer
     * <CRPCEN_NUMFACTURE_TYPEFACTURE_AAAAMMJJ>.pdf
     * @var string
     */
    public static $importFacturePattern = '/([0-9]+)_([a-zA-Z0-9]+)_([a-zA-Z0-9]+)_([0-9]+)\.pdf/i';
    public static $importFactureParserPattern = '/^.*([0-9]+)_([a-zA-Z0-9]+)_([a-zA-Z0-9]+)_([0-9]+)\.pdf$/i';

    // Notification pour une nouvelle facture
    public static $mailSubjectNotifFacture = array(
        'Notification de nouvelle facture'
    );

    /**
     * @var array : liste de type de document non liés aux models de WPMVC
     * @TODO : à completer avec type relevé de consommation
     */
    public static $exceptedDocTypeForModel = array(
        CONST_DOC_TYPE_FACTURE
    );

    /**
     * Selon la regle de nommage des fichiers factures à importer
     * <CRPCEN_releveconso_AAAAMMJJ>.pdf
     * @var string
     */
    public static $importRelevePattern = '/([0-9]+)_([a-zA-Z0-9]+)_([0-9]+)\.pdf/';
    public static $importReleveParserPattern = '/^.*([0-9]+)_([a-zA-Z0-9]+)_([0-9]+)\.pdf$/i';

    /**
     * Get role label by role
     *
     * @param string $role
     * @return mixed
     */
    public static function getRoleLabel($role) {
        return Config::$notaryRoles[$role];
    }
}
