DROP TABLE IF EXISTS `cri_millesime`;

CREATE TABLE `cri_millesime` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `id_formation` INT NOT NULL,
  `year` year NOT NULL,
  PRIMARY KEY (`id`));