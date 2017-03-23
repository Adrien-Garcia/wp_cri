ALTER TABLE `cri_session` ADD COLUMN IF NOT EXISTS `is_full` TINYINT DEFAULT 0 AFTER `id_organisme`;
