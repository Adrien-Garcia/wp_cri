<?php

class AdminLieuHelper extends MvcHelper
{
    /*
     * @override
     */
    public function admin_actions_cell($controller, $object)
    {
        $links = array();
        $object_name = empty($object->__name) ? 'Item #'.$object->__id : $object->__name;
        $encoded_object_name = $this->esc_attr($object_name);
        $links[] = '<a href="'.str_replace('lieus','lieux',MvcRouter::admin_url(array('object' => $object, 'action' => 'edit'))).'" title="'.Config::$actionsWpmvcTranslation['edit'].' '.$encoded_object_name.'">'.Config::$actionsWpmvcTranslation['edit'].'</a>';
        $links[] = '<a href="'.str_replace('lieus','lieux',MvcRouter::admin_url(array('object' => $object, 'action' => 'delete'))).'" title="'.Config::$actionsWpmvcTranslation['delete'].' '.$encoded_object_name.'" onclick="return confirm(&#039;'.Config::$msgConfirmDelete.' '.$encoded_object_name.'?&#039;);">'.Config::$actionsWpmvcTranslation['delete'].'</a>';
        $html = implode(' | ', $links);
        return '<td>'.$html.'</td>';
    }
}
