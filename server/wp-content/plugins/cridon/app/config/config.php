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

    public static $modelTable = array(
        'veilles' => array(
            'name'              => 'veille',   // Nom de la table
            'model'             => 'Veille',   // Nom du MvcModel
        ),
        'flashes' => array(
            'name'              => 'flash',
            'model'             => 'Flash',
        ),
        'vie_cridons' => array(
            'name'              => 'vie_cridon',
            'model'             => 'VieCridon',
        ),
        'formations' => array(
            'name'              => 'formation',
            'model'             => 'Formation',
        ),
        'sessions' => array(
            'name'              => 'session',
            'model'             => 'Session',
        ),
        'cahier_cridons' => array(
            'name'              => 'cahier_cridon',
            'model'             => 'CahierCridon',
        )
    );

    // list of cridon_type using default post form
    public static $mvcWithPostForm = array('vie_cridons','cahier_cridons','flashes','formations','veilles');

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

    //Content qualified by a "Millésime"
    public static $contentWithMillesime = array(
        'formations',
    );

    //Content with parent
    public static $contentWithParent = array(
        'cahier_cridons',
    );

    //Content with specific email
    public static $contentWithSpecificEmail = array(
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

    public static $emailNotificationQuestionEmptyContent = array(
        'to' => 'info@cridon-lyon.fr',//Client e-mail, only use it in production mode
        'subject' => 'Question %s envoyée à l\'ERP sans son contenu',
        'message' => "La question n° %s posée par le client : %s n'a pas pu être envoyée avec son contenu. \nLe voici dans un email séparé :
        \nZTXTQUEST_0 : %s"
    );

    public static $emailNotificationQuestionNotSent = array(
        'to' => 'info@cridon-lyon.fr',//Client e-mail, only use it in production mode
        'subject' => 'Question %s non envoyée à l\'ERP',
        'message' => "La question n° %s posée par le client : %s n'a pas pu être envoyée. Merci de trouver ci-après toutes les informations envoyés habituellement à l'ERP:
        \nZQUEST_ZIDQUEST_0 : %s
        \nZQUEST_ZTRAITEE_0 : %s
        \nZQUEST_SREBPC_0 : %s
        \nZQUEST_SRECCN_0 : %s
        \nZQUEST_YCODESUP_0 : %s
        \nZQUEST_YMATIERE_0 : %s
        \nZQUEST_YMAT_0 : %s
        \nZQUEST_YMAT_1 : %s
        \nZQUEST_YMAT_2 : %s
        \nZQUEST_YMAT_3 : %s
        \nZQUEST_YMAT_4 : %s
        \nZQUEST_ZCOMPETENC_0 : %s
        \nZQUEST_ZCOMP_0 : %s
        \nZQUEST_ZCOMP_1 : %s
        \nZQUEST_ZCOMP_2 : %s
        \nZQUEST_ZCOMP_3 : %s
        \nZQUEST_ZCOMP_4 : %s
        \nZQUEST_YRESUME_0 : %s
        \nZQUEST_YSREASS_0 : %s
        \nZQUEST_CREDAT_0 : %s
        \nZQUEST_ZLIENS_0 : %s
        \nZQUEST_ZLIENS_1 : %s
        \nZQUEST_ZLIENS_2 : %s
        \nZQUEST_ZLIENS_3 : %s
        \nZQUEST_ZLIENS_4 : %s
        \nZTXTQUEST_0 : %s
        \nZQUEST_SRENUM1_0 : %s
        \nZQUEST_ZMESSERR_0 : %s
        \nZQUEST_ZERR_0 : %s
        "
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
        CONST_NOTAIRE_GERANT,
        CONST_NOTAIRE_GERANTE,
        CONST_NOTAIRE_SUPLEANT,
        CONST_NOTAIRE_SUPLEANTE,
        CONST_NOTAIRE_ADMIN,
    );

    public static $titleMetaboxMatiere = 'Matière';// Titre du metabox de catégorie matière en admin
    public static $titleMetaboxDocument = 'Associer des documents';// Titre du metabox pour l'ajout de document
    public static $titleMetaboxMillesime = 'Millésimes';// Titre du metabox pour l'ajout de millésime

    // list of accepted question supports
    public static $acceptedSupports = array(
        CONST_SUPPORT_COURRIER_ID,
        CONST_SUPPORT_URG48H_ID,
        CONST_SUPPORT_URGWEEK_ID,
        CONST_SUPPORT_NON_FACTURE,
        CONST_SUPPORT_MES_DIANE,
        CONST_SUPPORT_3_TO_4_WEEKS_INITIALE_ID,
        CONST_SUPPORT_2_DAYS_INITIALE_ID,
        CONST_SUPPORT_5_DAYS_MEDIUM_ID,
        CONST_SUPPORT_RDV_TEL_MEDIUM_ID,
        CONST_SUPPORT_3_TO_4_WEEKS_EXPERT_ID,
        CONST_SUPPORT_DOSSIER_EXPERT_ID,
        CONST_SUPPORT_LETTRE_HORS_DELAI_ID,
        CONST_SUPPORT_2_DAYS_INITIALE_HORS_DELAI_ID,
        CONST_SUPPORT_5_DAYS_MEDIUM_HORS_DELAI_ID
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
        1 => 'Votre question du %s de niveau d\'expertise %s en délai %s a bien été transmise.',
        2 => 'Nous avons bien reçu votre question numéro %s du %s de niveau d\'expertise %s en délai %s.',
        3 => array('Compte tenu de l’affluence des demandes, il ne nous sera pas possible de respecter le délai demandé de votre question numéro %s du %s.',
                   'Nous enregistrons votre question en délai %s et faisons le nécessaire pour vous donner satisfaction.'),
        4 => array('Votre question numéro %s de niveau d\'expertise %s en délai %s a été attribuée le %s à %s.',
                   'Une réponse devrait être apportée au plus tard le %s.'),
        5 => 'Merci de nous adresser les renseignements complémentaires demandés qui nous sont indispensables pour répondre à votre question numéro %s de niveau d\'expertise %s en délai %s du %s.',
        6 => 'La réponse à votre question numéro %s de niveau d\'expertise %s en délai %s du %s est disponible depuis votre espace privé.',

    );

    public static $mailSubjectCridonline = 'Confirmation de votre souscription à crid\'online';

    public static $mailSubjectCahierCridon = 'Nouveau cahier cridon lyon';

    public static $notificationAddressDev = "clement.horgues@jetpulp.fr";

    public static $notificationAddressPreprod = "s.raby@cridon-lyon.fr";

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
        'questions','soldes','supports','user_cridons','veilles','vie_cridons', 'sessions',
        'evenements','demarches','cridonline_prices','entites'
    );
    public static $listOfControllersWithNoActionAdd = array(
        'entites',
        'notaires',
        'questions',
        'demarches',
        'cridonline_prices',
        'sessions',
    );
    
    //Admin wp_mvc action translation
    public static $actionsWpmvcTranslation = array(
        'view'   => 'Voir',
        'edit'   => 'Editer',
        'delete' => 'Supprimer',
        'download' => 'Télécharger',
        'complete' => 'Indiquer comme complet',
        'full' => 'Complet'
    );
    public static $msgConfirmDelete = 'Êtes-vous sur de vouloir supprimer';
    public static $msgConfirmComplete = 'Voulez vous indiquer cette session comme étant complète ?';
    public static $btnTextAdmin = array(
        'add'    => 'Ajouter',
        'update' => 'Mettre à jour',
        'export' => 'Exporter'
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
        ),
        'session' => array(
            'add'    => 'Ajout d\'une nouvelle session de formation',
            'edit'   => 'Modifier les informations d\'une session de formation'
        ),
        'evenement' => array(
            'add'    => 'Ajout d\'un nouvel évènement',
            'edit'   => 'Modifier les informations d\'un évènement'
        ),
        'demarche' => array(
            'add'    => 'Demarche',
            'edit'   => 'Gérer une démarche',
            'export' => 'Exporter les démarches en CSV'
        )
    );
    public static $titleFieldAdminForm = array(
        'address'       => 'Adresse',
        'client_number' => 'Numéro client',
        'city'          => 'Ville',
        'code'          => 'Code',
        'date_arret'    => 'Date d\'arrêt',
        'description'   => 'Description',
        'displayed'     => 'Affiché sur le site ?',
        'email'         => 'Email',
        'evenement'     => 'Nom de l\'évènement',
        'is_cridon'     => 'Est le cridon ?',
        'label'         => 'Libellé',
        'label_front'   => 'Libellé en front',
        'name'          => 'Nom',
        'phone_number'  => 'Numéro de téléphone',
        'postal_code'   => 'Code postal',
        'question'      => 'Sur les questions ?',
        'quota'         => 'Quota',
        'short_label'   => 'Libellé court',
        'type_support'  => 'Type du support',
        'value'         => 'Valeur',
        'date'          => 'Date',
        'timetable'     => 'Horaire',
        'color'         => 'Couleur',
        'export_start_date' => 'Export du',
        'export_end_date'   => 'au',
        'export_complet'   => 'Export intégral',
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
        'sessions','evenements',
    );
    public static $dateTitleMetabox = 'Date de session';// Titre du metabox date de formation

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

    public static $customBreadcrumbActions = array(
        'calendar','demande','demandegenerique','preinscription'
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
    public static $mailPassword = array(
        'changePasswordSubject' => 'Changement du mot de passe de %s',
        'firstTimeTelPasswordSubject' => 'Identifiants d\'accès CRIDON LYON de %s',
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

    // Motifs de changement de niveau :
    // 1 : Résiliation à échéance
    // 2 : Sans suite
    // 3 : Résiliation immédiate
    // 4 : Upgrade sans suite
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
     * <CRPCEN_TYPEPIECE_NUMFACTURE_TYPEFACTURE_AAAAMMJJ>.pdf
     * @var string
     */
    public static $importFacturePattern = '/([a-zA-Z0-9]+)_([a-zA-Z0-9]+)_([a-zA-Z0-9]+)_([a-zA-Z0-9]+)_([0-9]+)\.pdf/i';
    public static $importFactureParserPattern = '/^.*([a-zA-Z0-9]+)_([a-zA-Z0-9]+)_([a-zA-Z0-9]+)_([a-zA-Z0-9]+)_([0-9]+)\.pdf$/i';

    // Notification pour une nouvelle facture
    public static $mailSubjectNotifFacture = 'Notification de nouvelle facture';

    /**
     * @var array : liste de type de document non liés aux models de WPMVC
     */
    public static $exceptedDocTypeForModel = array(
        CONST_DOC_TYPE_FACTURE,
        CONST_DOC_TYPE_RELEVE_CONSO
    );

    /**
     * Selon la regle de nommage des fichiers factures à importer
     * <CRPCEN_releveconso_AAAAMMJJ>.pdf
     * @var string
     */
    public static $importRelevePattern = '/([a-zA-Z0-9]+)_([a-zA-Z0-9]+)_([0-9]+)\.pdf/i';
    public static $importReleveParserPattern = '/^.*([a-zA-Z0-9]+)_([a-zA-Z0-9]+)_([0-9]+)\.pdf$/i';

    public static $cridonlineLevels = array (
        CONST_CRIDONLINE_LEVEL_2,
        CONST_CRIDONLINE_LEVEL_3
    );

    public static $promo_available_for_level = array(
        CONST_PROMO_CHOC => array(
            CONST_CRIDONLINE_LEVEL_2,
            CONST_CRIDONLINE_LEVEL_3
        ),
        CONST_PROMO_PRIVILEGE => array(
            CONST_CRIDONLINE_LEVEL_3
        )
    );

    public static $cridonlineMessages = array (
        'no_promo' => 'Vous avez choisi l\'offre CRID\'ONLINE %s pour <strong>%.2f € HT</strong> par an', // 1er %s : premium / excellence ; 2ème %s : price
        'promo_choc' => '<u>Vous avez choisi l\'offre CRID\'ONLINE %s à <strong> %.2f € HT</strong> par an (fin de l\'année %s offerte)</u>', // 1er %s : label premium / excellence ; 2ème %s : price ; 3ème %s : date
        'promo_privilege' => '<u>Vous avez choisi l\'offre CRID\'ONLINE Excellence pendant deux ans à <strong>%.2f € HT</strong> la première année</u>', // 1er %s : price
    );

    /**
     * Tableau des déclassements : à lire -> Du support `key` au support array(`values`)
     *
     * @var array
     */
    public static $declassement = array(
        CONST_SUPPORT_3_TO_4_WEEKS_INITIALE_ID =>
            array(
                CONST_SUPPORT_5_DAYS_MEDIUM_ID,
                CONST_SUPPORT_RDV_TEL_MEDIUM_ID,
                CONST_SUPPORT_3_TO_4_WEEKS_EXPERT_ID,
                CONST_SUPPORT_DOSSIER_EXPERT_ID
            ),
        CONST_SUPPORT_2_DAYS_INITIALE_ID       =>
            array(
                CONST_SUPPORT_5_DAYS_MEDIUM_ID,
                CONST_SUPPORT_RDV_TEL_MEDIUM_ID,
                CONST_SUPPORT_3_TO_4_WEEKS_EXPERT_ID,
                CONST_SUPPORT_DOSSIER_EXPERT_ID
            ),
        CONST_SUPPORT_5_DAYS_MEDIUM_ID         =>
            array(
                CONST_SUPPORT_RDV_TEL_MEDIUM_ID,
                CONST_SUPPORT_3_TO_4_WEEKS_EXPERT_ID,
                CONST_SUPPORT_DOSSIER_EXPERT_ID
            ),
        CONST_SUPPORT_RDV_TEL_MEDIUM_ID        =>
            array(
                CONST_SUPPORT_5_DAYS_MEDIUM_ID,
                CONST_SUPPORT_3_TO_4_WEEKS_EXPERT_ID,
                CONST_SUPPORT_DOSSIER_EXPERT_ID
            ),
        CONST_SUPPORT_3_TO_4_WEEKS_EXPERT_ID   =>
            array(
                CONST_SUPPORT_RDV_TEL_MEDIUM_ID,
                CONST_SUPPORT_DOSSIER_EXPERT_ID
            ),
        CONST_SUPPORT_DOSSIER_EXPERT_ID        =>
            array(
                CONST_SUPPORT_3_TO_4_WEEKS_EXPERT_ID
            )
    );
    /**
     * Tableau des codes supports correspondant à une question téléphonique
     *
     * @var array
     */
    public static $code_support_questions_tel = array(
        CONST_SUPPORT_APPEL_ID,
        CONST_SUPPORT_RDV_TEL_MEDIUM_ID
    );
    /**
     * Tableau des codes supports correspondant à une question écrite
     *
     * @var array
     */
    public static $code_support_questions_ecrites = array(
        CONST_SUPPORT_COURRIER_ID,
        CONST_SUPPORT_NON_FACTURE,
        CONST_SUPPORT_URG48H_ID,
        CONST_SUPPORT_URGWEEK_ID,
        CONST_SUPPORT_3_TO_4_WEEKS_INITIALE_ID,
        CONST_SUPPORT_2_DAYS_INITIALE_ID,
        CONST_SUPPORT_5_DAYS_MEDIUM_ID,
        CONST_SUPPORT_3_TO_4_WEEKS_EXPERT_ID,
        CONST_SUPPORT_DOSSIER_EXPERT_ID,
        CONST_SUPPORT_LETTRE_HORS_DELAI_ID,
        CONST_SUPPORT_2_DAYS_INITIALE_HORS_DELAI_ID,
        CONST_SUPPORT_5_DAYS_MEDIUM_HORS_DELAI_ID
    );

    public static $libellesFactures = array(
        CONST_TYPEFACTURE_CG            => 'Cotisation générale',
        CONST_TYPEFACTURE_CS            => 'Cotisation supplémentaire',
        CONST_TYPEFACTURE_CRIDONLINE    => 'Crid\'online',
        CONST_TYPEFACTURE_CONSULTATION  => 'Consultation',
        CONST_TYPEFACTURE_DOSSIER       => 'Dossier',
        CONST_TYPEFACTURE_SAF           => 'Service d\'assistance fiscale',
        CONST_TYPEFACTURE_SEMINAIRE     => 'Formation',
        CONST_TYPEFACTURE_TRADUC        => 'Traduction',
        CONST_TYPEFACTURE_OUVRAGE       => 'Ouvrage',
        CONST_TYPEFACTURE_DIVERS        => 'Divers'
    );

    public static $mailSubjectFormationPreinscription = 'Demande de pré-inscription à la formation';
    public static $mailSubjectAdminFormationPreinscription = 'Nouvelle demande de pré-inscription à la formation';
    public static $mailSubjectFormationGenerique = 'Demande de formation';
    public static $mailSubjectFormationDemande = 'Demande de formation';

    public static $notificationAddressFormulaireFormation = 'n.prunaret@cridon-lyon.fr';
    
    public static $allowedMailTags = array(
        'a' => array(
            'href' => true,
            'title' => true,
        ),
        'abbr' => array(
            'title' => true,
        ),
        'acronym' => array(
            'title' => true,
        ),
        'b' => array(),
        'br' => array(),
        'blockquote' => array(
            'cite' => true,
        ),
        'cite' => array(),
        'code' => array(),
        'del' => array(
            'datetime' => true,
        ),
        'em' => array(),
        'i' => array(),
        'q' => array(
            'cite' => true,
        ),
        's' => array(),
        'strike' => array(),
        'strong' => array(),
    );

    public static $labelWorflowFormation = array(
        CONST_FORMATION_PREINSCRIPTION => 'Pré-inscription',
        CONST_FORMATION_DEMANDE => 'Nouvelle session',
        CONST_FORMATION_GENERIQUE => 'Nouvelle formation',
    );
}
