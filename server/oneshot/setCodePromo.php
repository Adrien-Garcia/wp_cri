<?php
/**
 * Ce script permet de setter les deux champs code promo pour chaque entité à une valeur aléatoire de 6 caractères
 * De plus, on met à jour un flag pour 1 notaire par entité pour pouvoir exporter ces codes à l'erp
 *
 * Created by PhpStorm.
 * User: amsellem
 * Date: 10/02/2016
 * Time: 11:49
 */


// set utf-8 encoding
header('Content-type: text/html; charset=utf-8');
if (!defined( 'WP_ADMIN' )) {
    define('WP_ADMIN', true);
}
// load WP Core
require_once '../wp-load.php';

/**
 * @var Document $model
 */
$model = mvc_model('Entite');

$entites = $model->find(
    array(
        'joins' => array() //dummy join to avoid loading of all relations
    ));
foreach ($entites as $entite){
    $data = array(
        'code_promo_offre_choc' => $model->getRandomPromoCode(),
        'code_promo_offre_privilege' => $model->getRandomPromoCode()
    );
    $model->update($entite->crpcen,$data);

    //Set cron_update_erp to send code promos to the erp
    $notaire = mvc_model('Notaire')->find_one(array(
        'conditions' => array(
            'crpcen' => $entite->crpcen,
        ),
        'joins' => array(),
        'selects' => array('id')
    ));
    mvc_model('Notaire')->updateFlagERP($notaire->id);
}

echo 'Promo code inserted for all entites';
