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
            'controller'        => 'veilles',  // Contrôleur pour la redirection après ajout de post
            'action'            => 'index'     // Action associée au contrôleur
        ),
        'flashes' => array(
            'value'             => 'flashes',
            'name'              => 'flash',
            'controller'        => 'flashes', 
            'action'            => 'index' 
        ),
        'vie_cridons' => array(
            'value'             => 'vie_cridons',
            'name'              => 'vie_cridon',
            'controller'        => 'vie_cridons',
            'action'            => 'index' 
        ),
        'formations' => array(
            'value'             => 'formations',
            'name'              => 'formation',
            'controller'        => 'formations', 
            'action'            => 'index' 
        ),
        'cahier_cridons' => array(
            'value'             => 'cahier_cridons',
            'name'              => 'cahier_cridon',
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

    // list of category not to be imported
    public static $notImportedList = array(CONST_CLIENTDIVERS_ROLE);
    
    //List of role Notaire
    public static $rolesNotaire = array( 'notaire',CONST_OFFICES_ROLE,CONST_ORGANISMES_ROLE,CONST_CLIENTDIVERS_ROLE );
}
