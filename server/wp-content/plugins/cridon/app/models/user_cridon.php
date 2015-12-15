<?php

require_once 'base_model.php';

class UserCridon extends BaseModel {
    var $table        = '{prefix}user_cridon';
    var $display_field = 'id_erp';
    var $includes  = array('User');

    public $belongs_to = array(
        'User' => array(
            'foreign_key' => 'id_wp_user'
        )
    );
    public function delete($id) {
        $user = $this->find_by_id($id);
        if( !empty( $user->user ) ){
            $adminUrl  = wp_nonce_url(admin_url('users.php?action=delete&user='.$user->id_wp_user),'bulk-users');
            //Redirect to user deleting
            wp_redirect( $adminUrl, 301 );
            exit;
        }
        parent::delete($id);
    }
}

?>