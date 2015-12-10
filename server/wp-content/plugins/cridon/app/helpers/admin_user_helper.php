<?php

/**
 * Class AdminUserHelper
 *
 * @author eTech
 */
class AdminUserHelper extends MvcHelper
{
    /*
     * @override 
     */
    public function admin_actions_cell($controller, $object)
    {
        $links               = array();
        $object_name         = empty( $object->__name ) ? 'Item #' . $object->__id : $object->__name;
        if( isset( $object->user ) && !empty( $object->user ) ){
            $encoded_object_name = $object->user->user_login;
        }else{
            $encoded_object_name = $this->esc_attr($object_name);          
        }        
        $links[]             = '<a href="' . admin_url('user-edit.php?user_id='.$object->user->ID) . '" title="'.Config::$actionsWpmvcTranslation['edit'].' ' . $encoded_object_name . '">'.Config::$actionsWpmvcTranslation['edit'].'</a>';
        $links[]             = '<a href="' . wp_nonce_url(admin_url('users.php?action=delete&user='.$object->user->ID),'bulk-users') . '" title="'.Config::$actionsWpmvcTranslation['delete'].' ' . $encoded_object_name . '" onclick="return confirm(&#039;'.Config::$msgConfirmDelete.' ' . $encoded_object_name . '?&#039;);">'.Config::$actionsWpmvcTranslation['delete'].'</a>';
        $html                = implode(' | ', $links);

        return '<td>' . $html . '</td>';
    }
}

