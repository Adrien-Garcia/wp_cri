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
        'actu_cridons' => array(
            'value'             => 'actu_cridons',
            'name'              => 'actu_cridon',
            'controller'        => 'actu_cridons', 
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
    public static $mvcWithPostForm = array('actu_cridons','cahier_cridons','flashes','formations','veilles');
}
