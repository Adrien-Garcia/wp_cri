<?php
// base admin ctrl
require_once 'base_admin_controller.php';

class AdminUserCridonsController extends BaseAdminController {
    
    var $default_search_joins = array('User');
    /**
     *
     * @var array
     */
    var $default_searchable_fields = array(
        'id_erp', 
        'profil',
        'User.user_nicename',
        'last_connection'
    );
    var $default_columns = array('id_erp', 'profil',
        'user' => array( 'label'=>'Utilisateur','value_method' => 'show_user'),
        'last_connection' => array( 'label' => 'Dernière connexion','value_method' => 'show_last_connection'),
    );
    public function index() {
        parent::index();
        if( isset( $this->params['flash'] ) ){
            if( ( $this->params['flash'] == 'success' ) && isset( $this->params['action_referer'] ) ){
                if ($this->params['action_referer'] === 'edit') {
                    $this->flash('notice', 'L\'utilisateur a été mise à jour !');
                }else{
                    $this->flash('notice', 'L\'utilisateur a été supprimé !');
                }
            }else{
                $this->flash('notice', 'L\'utilisateur a été bien ajouté !'); 
            }
        }
        //Load custom helper
        $this->load_helper('AdminUser');
    }
    public function show_user($object)
    {
        return empty($object->user) ? null : $object->user->__name;
    }
    public function show_last_connection($object)
    {
        $date = '0000-00-00 00:00:00';
        if( !empty( $object->last_connection ) && ( $object->last_connection !== '0000-00-00 00:00:00' )){
            $date = $object->last_connection;
        }
        return $date;
    }
}

?>