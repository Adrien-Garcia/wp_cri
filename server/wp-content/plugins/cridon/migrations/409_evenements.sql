DROP TABLE IF EXISTS `cri_evenement`;

CREATE TABLE `cri_evenement` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `date` DATE NOT NULL UNIQUE,
  PRIMARY KEY (`id`));