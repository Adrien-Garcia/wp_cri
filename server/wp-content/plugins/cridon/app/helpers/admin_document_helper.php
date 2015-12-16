<?php

class AdminDocumentHelper extends MvcHelper
{
    /*
     * @override 
     */
    public function admin_actions_cell($controller, $object)
    {
        $model = $controller->model;
        $links = array();
        $object_name = empty($object->__name) ? 'Item #'.$object->__id : $object->__name;
        $encoded_object_name = $this->esc_attr($object_name);
        $links[] = '<a href="'.MvcRouter::admin_url(array('object' => $object, 'action' => 'edit')).'" title="'.Config::$actionsWpmvcTranslation['edit'].' '.$encoded_object_name.'">'.Config::$actionsWpmvcTranslation['edit'].'</a>';
        $links[] = '<a href="'.$model->generatePublicUrl($object->id).'" title="'.Config::$actionsWpmvcTranslation['download'].' '.$encoded_object_name.'" target="_blank">'.Config::$actionsWpmvcTranslation['download'].'</a>';
        $links[] = '<a href="'.MvcRouter::admin_url(array('object' => $object, 'action' => 'delete')).'" title="'.Config::$actionsWpmvcTranslation['delete'].' '.$encoded_object_name.'" onclick="return confirm(&#039;'.Config::$msgConfirmDelete.' '.$encoded_object_name.'?&#039;);">'.Config::$actionsWpmvcTranslation['delete'].'</a>';
        $html = implode(' | ', $links);
        return '<td>'.$html.'</td>';
    }
}

