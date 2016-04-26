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

    public function getRelatedPrices($etude) {

        // get number of members of the office
        $options = array('conditions' => array('crpcen' => $etude->crpcen, 'id_fonction' => Config::$functionsPricesCridonline));
        $nbCollaboratorEtude = mvc_model('QueryBuilder')->countItems('notaire', $options);

        $subscriptionInfos = array();
        foreach (Config::$pricesLevelsVeilles as $level => $grille) {
            $prices = $grille[$level];
            // Tri du tableau de prix par clé descendante
            krsort($prices);
            foreach ($prices as $nbCollaborator => $price) {
                if ($nbCollaboratorEtude >= $nbCollaborator) {
                    $subscriptionInfos[$level] = $price;
                    break;
                }
            }
        }

        return $subscriptionInfos;
    }

    public function getSubscriptionPrice($etude, $isNextLevel = false){
        $level = ($isNextLevel && !empty($etude->next_subscription_level)) ? $etude->next_subscription_level : $etude->subscription_level;
        $prices = $this->getRelatedPrices($etude);
        return $prices[$level];
    }



}

?>
