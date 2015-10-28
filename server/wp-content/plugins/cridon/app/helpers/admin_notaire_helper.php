<?php

class AdminNotaireHelper extends MvcHelper
{
    /*
     * @override 
     */
    public function admin_actions_cell($controller, $object)
    {
        $links               = array();
        $object_name         = empty( $object->__name ) ? 'Item #' . $object->__id : $object->__name;
        $encoded_object_name = $this->esc_attr($object_name);
        $links[] = '<a href="'.MvcRouter::admin_url(array('object' => $object, 'action' => 'edit')).'" title="View '.$encoded_object_name.'">View</a>';
        $html = implode(' | ', $links);
        return '<td>' . $html . '</td>';
    }
}

