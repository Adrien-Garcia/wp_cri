CREATE TABLE `cri_expertise_support` (
   `id_expertise` INT NULL,
   `id_support` INT NULL,
   INDEX `fk_cri_support_expertise_cri_expertise1_idx` (`id_expertise` ASC),
   INDEX `fk_cri_support_expertise_cri_support1_idx` (`id_support` ASC)
);

INSERT INTO cri_expertise_support (`id_expertise`, `id_support`) VALUES
(1, 6),
(1, 8),
(2, 7),
(2, 9),
(3, 8),
(3, 10);