<?php

/**
 * Class AdminNotairesController
 * @author Etech
 * @contributor Joelio
 *
 */
class AdminNotairesController extends MvcAdminController
{
    var $default_search_joins = array('Etude');
    /**
     *
     * @var array
     */
    var $default_searchable_fields = array(
        'last_name', 
        'first_name',
        'client_number',
        'crpcen',
        'email_adress',
        'Etude.office_name',
        'tel_portable'
    );
    /**
     * @var array
     */
    public $default_columns = array(
        'last_name'  => array( 'label' => 'Prénom' ),
        'first_name' => array( 'label' => 'Nom' ),
        'client_number' => array( 'label' => 'Numéro client' ),
        'crpcen' => array( 'label' => 'CRPCEN' ),
        'email_adress' => array( 'label' => 'Email' ),
        'office_name' => array( 'label' => 'Nom de l\'office','value_method' => 'displayOfficeName' ),
        'tel_portable' => array( 'label' => 'Téléphone' ),
    );
    private function prepareData($aOptionList, $aData)
    {
        if (is_array($aData) && count($aData) > 0) {
            foreach ($aData as $oData) {
                foreach ($aOptionList as $sKey => $sVal) {
                    $oData->$sKey = $oData->$sVal;
                }
            }
        } elseif(is_object($aData)) {
            foreach ($aOptionList as $sKey => $sVal) {
                $aData->$sKey = $aData->$sVal;
            }
        }
    }
    public function displayOfficeName($object)
    {    
        return empty( $object->etude ) ? null : $object->etude->__name;
    }
}