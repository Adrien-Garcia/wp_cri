<?php

/**
 * Description of base_model.php
 *
 * @package wp_cridon
 * @author
 * @contributor
 */
class BaseModel extends MvcModel
{
    public function __construct()
    {
        // only in admin area
        if (is_admin()) {
            $this->per_page = CONST_ADMIN_NB_ITEM_PERPAGE;
        }
        parent::__construct();
    }
}