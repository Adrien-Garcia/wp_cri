ALTER TABLE `cri_matiere` ADD COLUMN `meta_title` TEXT NULL AFTER `question`;
ALTER TABLE `cri_matiere` ADD COLUMN `meta_description` LONGTEXT NULL AFTER `meta_title`; 