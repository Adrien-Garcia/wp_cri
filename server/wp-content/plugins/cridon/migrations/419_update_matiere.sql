ALTER TABLE `cri_matiere`
CHANGE COLUMN `displayed` `displayed` TINYINT(1) NOT NULL DEFAULT '0' AFTER `meta_description`,
ADD COLUMN `formation` TINYINT(1) NOT NULL DEFAULT '0' AFTER `question`;

UPDATE cri_matiere SET formation = 1 WHERE code != 'X';

INSERT INTO `cri_matiere` (`id`, `code`, `label`, `short_label`, `picto`, `virtual_name`, `displayed`, `question`, `color`, `formation`) VALUES ('16', 'M', 'Management et informatique', 'Info', '/wp-content/uploads/matieres/picto/CRIDON-PICTOS-BD-Management-informatique.png', 'management-informatique', '0', '0', '#EE7F01', '1');
