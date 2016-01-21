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
    public static $notificationAddressPreprod = "clement.horgues@jetpulp.fr";

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
        'notaires'
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

    //Label des affectations sur les questions
    public static $labelAffection = array(
        1 => 'Question transmise',
        2 => 'En cours de traitement',
        3 => 'En attente de renseignements complémentaires',
        4 => 'Question répondue'
    );
    //End label

    // breadcrumb wpmvc model title
    // key must be match with controller params name
    public static $breadcrumbModelParams = array(
        'veilles'           => 'Veille juridique',
        'flashes'           => 'Flash infos',
        'cahier_cridons'    => 'Les cahiers du CRIDON',
        'formations'        => 'Formation',
        'vie_cridons'       => 'Vie du CRIDON',
    );

    //RSS
    public static $rss = array(
        'title'         => 'Flux RSS des veilles',//all
        'title_mat'     => '%s',//filtered
        'description'   => ''
    );
    //End RSS
}
