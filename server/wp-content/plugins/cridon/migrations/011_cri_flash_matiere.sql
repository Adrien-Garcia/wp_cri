ALTER TABLE `cri_flash` ADD `id_matiere` INT NULL;
ALTER TABLE `cri_flash` ADD KEY `fk_cri_veille_cri_matiere1_idx` (`id_matiere` ASC);