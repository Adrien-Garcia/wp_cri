<?php $address = (is_object($oModel) && property_exists($oModel, 'address') && $oModel->address) ? $oModel->address : 'La Joliette<br />
20A Boulevard du Plomb<br />
13581 Marseille Cedex 20<br />
France' ?>
<textarea style="width: 100%; height: 85px;" name="address" id="address" value="<?php echo $address ?>" ><?php echo $address ?></textarea>