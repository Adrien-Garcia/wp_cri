<input type="text" name="custom_post_date" id="custom_post_date" value="<?php echo ((is_object($oModel) && property_exists($oModel, 'custom_post_date') && $oModel->custom_post_date && $oModel->custom_post_date != '0000-00-00') ? $oModel->custom_post_date : '') ?>" >