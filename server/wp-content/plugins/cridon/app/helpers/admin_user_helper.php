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
        $encoded_object_name = $this->esc_attr($object_name);
        $links[]             = '<a href="' . admin_url('user-edit.php?user_id='.$object->user->ID) . '" title="Edit ' . $encoded_object_name . '">Edit</a>';
        $links[]             = '<a href="' . wp_nonce_url(admin_url('users.php?action=delete&user='.$object->user->ID),'bulk-users') . '" title="Delete ' . $encoded_object_name . '" onclick="return confirm(&#039;Are you sure you want to delete ' . $encoded_object_name . '?&#039;);">Delete</a>';
        $html                = implode(' | ', $links);

        return '<td>' . $html . '</td>';
    }
}

