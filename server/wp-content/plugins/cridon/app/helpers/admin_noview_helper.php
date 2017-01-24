<?php

require_once 'admin_custom_helper.php';

class AdminNoviewHelper extends AdminCustomHelper
{
    /*
     * @override
     */
    public function admin_actions_cell($controller, $object)
    {
        return parent::admin_actions_cell($controller, $object, false);
    }
}
