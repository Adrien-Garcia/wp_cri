ALTER TABLE `cri_cahier_cridon`
ADD COLUMN `id_parent` INT NULL DEFAULT NULL AFTER `post_id`,
ADD COLUMN `id_matiere` INT DEFAULT NULL AFTER `id_parent`,
ADD INDEX (`id_parent`),
ADD INDEX (`id_matiere`);

