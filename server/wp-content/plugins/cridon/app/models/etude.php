<?php

class Etude extends \App\Override\Model\CridonMvcModel {
    public $primary_key = 'crpcen';
    public $display_field = 'office_name';
    public $table = '{prefix}etude';
    var $has_many       = array(
        'Notaire' => array(
            'foreign_key' => 'crpcen'
        )
    );
    var $includes       = array('Sigle');
    var $belongs_to     = array(
        'Sigle' => array('foreign_key' => 'id_sigle')
    );

    public function getSubscriptionPrice($etude,$isNext = true,$allPrices = false){

        if (!$allPrices) {
            $levels[] = ($isNext && !empty($etude->next_subscription_level)) ? $etude->next_subscription_level : $etude->subscription_level;
        } else {
            $levels = array(1,2,3);
        }
        // get number of members of the office
        $options = array('conditions' => array('crpcen' => $etude->crpcen, 'id_fonction' => Config::$functionsPricesCridonline));
        $nbCollaboratorEtude = mvc_model('QueryBuilder')->countItems('notaire', $options);
        $subscriptionInfos = [];
        foreach ($levels as $level) {
            $prices = Config::$pricesLevelsVeilles[$level];
            // Tri du tableau de prix par clé descendante
            krsort($prices);
            foreach ($prices as $nbCollaborator => $price) {
                if ($nbCollaboratorEtude >= $nbCollaborator) {
                    $subscriptionInfos[] = array('level' => $level, 'price' => $price);
                    break;
                }
            }
        }
        if (count($subscriptionInfos) == 1){
            return $subscriptionInfos[0]['price'];
        } else {
            return $subscriptionInfos;
        }
    }



}

?>