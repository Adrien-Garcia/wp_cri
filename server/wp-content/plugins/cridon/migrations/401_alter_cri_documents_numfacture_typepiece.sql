ALTER TABLE `cri_document` ADD COLUMN IF NOT EXISTS `numero_facture`  VARCHAR(255) NULL AFTER `label`;
ALTER TABLE `cri_document` ADD COLUMN IF NOT EXISTS `type_piece` VARCHAR(255) NULL AFTER `numero_facture`;
ALTER TABLE `cri_document` ADD COLUMN IF NOT EXISTS `type_facture` VARCHAR(255) NULL AFTER `type_piece`;