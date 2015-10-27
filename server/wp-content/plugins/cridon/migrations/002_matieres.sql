ALTER TABLE `cri_matiere` ADD `picto` VARCHAR(255) NULL;
ALTER TABLE `cri_veille` ADD `id_matiere` INT NULL;
ALTER TABLE cri_veille ADD KEY `fk_cri_veille_cri_matiere1_idx` (`id_matiere` ASC);
