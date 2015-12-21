ALTER TABLE `cri_competence` CHANGE `id` `id` VARCHAR(50) NOT NULL; 
ALTER TABLE `cri_competence` ADD COLUMN `short_label` VARCHAR(255) NULL AFTER `label`; 