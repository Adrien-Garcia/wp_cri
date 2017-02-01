DROP TABLE IF EXISTS `cri_lieu_etude`;
CREATE TABLE `cri_lieu_etude` (
   `crpcen` INT NULL,
   `id_lieu` INT NULL,
   INDEX `fk_cri_lieu_etude_cri_lieu1_idx` (`id_lieu` ASC),
   INDEX `fk_cri_lieu_etude_cri_etude1_idx` (`crpcen` ASC)
);