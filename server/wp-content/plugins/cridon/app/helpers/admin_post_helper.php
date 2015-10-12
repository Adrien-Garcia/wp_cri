<?php

/**
 * Class AdminPostHelper
 *
 * @author eTech
 */
class AdminPostHelper extends MvcHelper
{
    /*
     * @override
     */
    public function admin_table_cells($controller, $objects)
    {
        $html = '';
        foreach ($objects as $object) {
            $html .= '<tr>';
            foreach ($controller->default_columns as $key => $column) {
                $html .= $this->admin_table_cell($controller, $object, $column);
            }
            $html .= $this->admin_actions_cell($controller, $object);
            $html .= '</tr>';
        }

        return $html;
    }

    /*
     * @override 
     */
    public function admin_actions_cell($controller, $object)
    {
        $links               = array();
        $object_name         = empty( $object->__name ) ? 'Item #' . $object->__id : $object->__name;
        $encoded_object_name = $this->esc_attr($object_name);
        $links[]             = '<a href="' . admin_url('post.php?post=' . $object->post_id . '&action=edit&cridon_type=' . $this->trim($controller->name)) . '" title="Edit ' . $encoded_object_name . '">Edit</a>';
        $links[]             = '<a href="' . MvcRouter::public_url(array('object' => $object)) . '" title="View ' . $encoded_object_name . '">View</a>';
        $links[]             = '<a href="' . MvcRouter::admin_url(array(
                'object' => $object,
                'action' => 'delete'
            )) . '" title="Delete ' . $encoded_object_name . '" onclick="return confirm(&#039;Are you sure you want to delete ' . $encoded_object_name . '?&#039;);">Delete</a>';
        $html                = implode(' | ', $links);

        return '<td>' . $html . '</td>';
    }

    private function trim($str)
    {
        return str_replace('admin_', '', $str);
    }
}

