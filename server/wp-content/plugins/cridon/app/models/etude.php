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

    public function getSubscriptionPrice($etude,$isNext = true){


        $level = ($isNext && !empty($etude->next_subscription_level)) ? $etude->next_subscription_level : $etude->subscription_level;

        $options = array('conditions' => array('crpcen' => $etude->crpcen));
        $nbCollaboratorEtude = count(mvc_model('QueryBuilder')->findAll('notaire', $options));
        $prices = Config::$pricesLevelsVeilles[0][$level];
        krsort($prices);
        // Tri du tableau de prix par clé descendante
        foreach($prices as $nbCollaborator => $price) {
            if ($nbCollaboratorEtude >= $nbCollaborator) {
                return $price;
            }
        }
    }



}

?>