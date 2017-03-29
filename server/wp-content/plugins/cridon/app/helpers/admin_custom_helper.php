<?php

class AdminCustomHelper extends MvcHelper
{
    /*
     * @override 
     */
    public function admin_actions_cell($controller, $object, $hasView = true)
    {
        $links = array();
        $object_name = empty($object->__name) ? 'Item #'.$object->__id : $object->__name;
        $encoded_object_name = $this->esc_attr($object_name);
        $links[] = '<a href="'.MvcRouter::admin_url(array('object' => $object, 'action' => 'edit')).'" title="'.Config::$actionsWpmvcTranslation['edit'].' '.$encoded_object_name.'">'.Config::$actionsWpmvcTranslation['edit'].'</a>';
        if ($hasView) {
            $links[] = '<a href="'.MvcRouter::public_url(array('object' => $object)).'" title="'.Config::$actionsWpmvcTranslation['view'].' '.$encoded_object_name.'">'.Config::$actionsWpmvcTranslation['view'].'</a>';
        }
        $links[] = '<a href="'.MvcRouter::admin_url(array('object' => $object, 'action' => 'delete')).'" title="'.Config::$actionsWpmvcTranslation['delete'].' '.$encoded_object_name.'" onclick="return confirm(&#039;'.Config::$msgConfirmDelete.' '.$encoded_object_name.'?&#039;);">'.Config::$actionsWpmvcTranslation['delete'].'</a>';
        $html = implode(' | ', $links);
        return '<td>'.$html.'</td>';
    }

    public function dateToDbFormat($sDate, $format = 'd-m-Y') {
        $timestamp = date_create_from_format($format, $sDate)->getTimestamp();
        return strftime('%G-%m-%d',$timestamp);
    }
}

