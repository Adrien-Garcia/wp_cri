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
        if( isset( $object->post ) && !empty( $object->post ) ){
            $encoded_object_name = $object->post->post_title;
        }else{
            $encoded_object_name = $this->esc_attr($object_name);            
        }
        $deleteLink = MvcRouter::admin_url(array(
            'object' => $object,
            'action' => 'delete'
        ));
        if (isset($_GET['option'])) {
            $deleteLink .= '&option=' . $_GET['option'];
        }
        $option = array(
            'controller' => MvcInflector::tableize($object->__model_name),
            'action'     => 'show',
            'id'         => $object->post->post_name
        );
        $url =  MvcRouter::public_url( $option );
        $links[]             = '<a href="' . admin_url('post.php?post=' . $object->post_id . '&action=edit&cridon_type=' . $this->trim($controller->name)) . '" title="'.Config::$actionsWpmvcTranslation['edit'].' ' . $encoded_object_name . '">'.Config::$actionsWpmvcTranslation['edit'].'</a>';
        $links[]             = '<a href="' . $url . '" title="'.Config::$actionsWpmvcTranslation['view'].' ' . $encoded_object_name . '">'.Config::$actionsWpmvcTranslation['view'].'</a>';
        $links[]             = '<a href="' . $deleteLink . '" title="'.Config::$actionsWpmvcTranslation['delete'].' ' . $encoded_object_name . '" onclick="return confirm(&#039;'.Config::$msgConfirmDelete.' ' . $encoded_object_name . '?&#039;);">'.Config::$actionsWpmvcTranslation['delete'].'</a>';
        $html                = implode(' | ', $links);

        return '<td>' . $html . '</td>';
    }

    private function trim($str)
    {
        return str_replace('admin_', '', $str);
    }
}

