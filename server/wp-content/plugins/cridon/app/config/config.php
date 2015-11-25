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

    // option list of document type    
    public static $optionDocumentType = array(
        'question'      => 'Question',
        'reponse'       => 'Réponse',
        'veille'        => 'Veille',
        'formation'     => 'Formation',
        'cahier_cridon' => 'Cahier cridon',
        'actu_cridon'   => 'Actu cridon',
        'flash'         => 'Flash'
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
        'id'     => 12,
        'name'   => 'Expertise transversale'
    );

    //Content qualified by a "Matière"
    public static $contentWithMatiere = array(
        'veilles',
        'flashes',
        'cahier_cridons',
    );

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
        'liste-affectation-cridon',
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

    // list of notaire functions cannot access finances
    public static $cannotAccessFinance = array(
        CONST_NOTAIRE_ASSOCIE,
        CONST_NOTAIRE_ASSOCIEE,
        CONST_NOTAIRE_SALARIE,
        CONST_NOTAIRE_SALARIEE
    );

    public static $titleMetaboxDocument = 'Associer des documents';// Titre du metabox pour l'ajout de document

    // list of accepted question supports
    public static $acceptedSupports = array(
        CONST_SUPPORT_COURRIER_ID,
        CONST_SUPPORT_URG48H_ID,
        CONST_SUPPORT_URGWEEK_ID
    );
}
