CREATE TABLE `cri_matiere_notaire` (
   `id_matiere` INT NULL,
   `id_notaire` INT NULL,
   INDEX `fk_cri_matiere_notaire_cri_matiere1_idx` (`id_matiere` ASC),
   INDEX `fk_cri_matiere_notaire_cri_notaire1_idx` (`id_notaire` ASC)
);