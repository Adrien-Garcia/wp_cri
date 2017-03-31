<?php

class AdminSessionHelper extends MvcHelper
{
    /*
     * @override 
     */
    public function admin_actions_cell($controller, $object)
    {
        $links = array();
        $links[] = '<a href="'.MvcRouter::admin_url(array('object' => $object, 'action' => 'delete')).'" title="'.Config::$actionsWpmvcTranslation['delete'].' '.$encoded_object_name.'" onclick="return confirm(&#039;'.Config::$msgConfirmDelete.' '.$encoded_object_name.'?&#039;);">'.Config::$actionsWpmvcTranslation['delete'].'</a>';
        if (!$object->is_full) {
            $links[] = '<a  href="'.MvcRouter::admin_url(array('object' => $object, 'action' => 'edit', 'id' => $object->id)).'"
                            title="'.Config::$actionsWpmvcTranslation['complete'].'"
                            onclick="return confirm(&#039;'.Config::$msgConfirmComplete.'&#039;);">
                            '.Config::$actionsWpmvcTranslation['complete'].'
                        </a>';
        } else {
            $links[] = '<span>
                            '.Config::$actionsWpmvcTranslation['full'].'
                        </span>';
        }
        $html = implode(' | ', $links);
        return '<td>'.$html.'</td>';
    }

    public function dateToDbFormat($sDate, $format = 'd-m-Y') {
        $timestamp = date_create_from_format($format, $sDate)->getTimestamp();
        return strftime('%G-%m-%d',$timestamp);
    }
}

