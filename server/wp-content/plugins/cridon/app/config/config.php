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
        'veilles' => array(                    //Indice correspondant aux noms de fichier de controlleur 
            'value'             => 'veilles', // Nécessaire à la correspondance
            'name_metabox'      => 'Veille', // Titre au niveau du metabox
            'name'              => 'veille', // nom de la table
            'controller'        => 'veilles', // contrôleur pour la redirection après ajout de post
            'action'            => 'index' // action associée au contrôleur
        ),
        'flashes' => array(
            'value'             => 'flashes',
            'name_metabox'      => 'Flash',
            'name'              => 'flash',
            'controller'        => 'flashes', // contrôleur pour la redirection après ajout de post
            'action'            => 'index' // action associée au contrôleur
        ),
        'actu_cridons' => array(
            'value'             => 'actu_cridons',
            'name_metabox'      => 'Actus Cridon',
            'name'              => 'actu_cridon',
            'controller'        => 'actu_cridons', // contrôleur pour la redirection après ajout de post
            'action'            => 'index' // action associée au contrôleur
        ),
        'formations' => array(
            'value'             => 'formations',
            'name_metabox'      => 'Formation',
            'name'              => 'formation',
            'controller'        => 'formations', // contrôleur pour la redirection après ajout de post
            'action'            => 'index' // action associée au contrôleur
        ),
        'cahier_cridons' => array(
            'value'             => 'cahier_cridons',
            'name_metabox'      => 'Cahier Cridon',
            'name'              => 'cahier_cridon',
            'controller'        => 'cahier_cridons', // contrôleur pour la redirection après ajout de post
            'action'            => 'index' // action associée au contrôleur
        )
    );
    public static $titleMetabox = 'Cridon contenu';

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
    public static $mvcWithPostForm = array('actu_cridons','cahier_cridons','flashes','formations','veilles');
}
