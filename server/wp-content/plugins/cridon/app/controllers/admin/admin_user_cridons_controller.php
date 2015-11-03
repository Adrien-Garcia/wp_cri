<?php

class AdminUserCridonsController extends MvcAdminController {
    
    var $default_columns = array('id_erp', 'profil',
        'user' => array( 'label'=>'Utilisateur','value_method' => 'show_user'),
        'last_connection' => array( 'value_method' => 'show_last_connection'),
    );
    public function show_user($object)
    {
        return empty($object->user) ? null : $object->user->__name;
    }
    public function show_last_connection($object)
    {
        $date = '';
        if( !empty( $object->last_connection ) ){
            $dt = new DateTime($object->last_connection);    
            $date = $dt->format('d-m-Y H:i');//To FR
        }
        return $date;
    }
}

?>