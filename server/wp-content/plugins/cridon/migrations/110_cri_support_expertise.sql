CREATE TABLE `cri_expertise_support` (
   `id_expertise` INT NULL,
   `id_support` INT NULL,
   INDEX `fk_cri_support_expertise_cri_expertise1_idx` (`id_expertise` ASC),
   INDEX `fk_cri_support_expertise_cri_support1_idx` (`id_support` ASC)
);

INSERT INTO cri_expertise_support (`id_expertise`, `id_support`) VALUES
(1, 8),
(1, 9),
(2, 10),
(2, 11),
(3, 12),
(3, 13);