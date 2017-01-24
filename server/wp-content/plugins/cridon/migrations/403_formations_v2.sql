CREATE TABLE `cri_session` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `date` DATE NOT NULL,
  `timetable` VARCHAR(255) NULL DEFAULT NULL,
  `id_formation` INT NOT NULL,
  `id_lieu` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `LIEU` (`id_lieu` ASC),
  INDEX `FORMATION` (`id_formation` ASC));

DROP TABLE IF EXISTS `cri_lieu`;

CREATE TABLE `cri_lieu` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `is_cridon` TINYINT(1) NOT NULL DEFAULT '0',
  `address` VARCHAR(255) NULL DEFAULT NULL,
  `postal_code` VARCHAR(8) NOT NULL,
  `city` VARCHAR(50) NOT NULL,
  `phone_number` VARCHAR(16) NOT NULL,
  `email` VARCHAR(255) NULL DEFAULT NULL,
  PRIMARY KEY (`id`));

ALTER TABLE `cri_formation` 
DROP COLUMN `town`,
DROP COLUMN `postal_code`,
DROP COLUMN `address`,
DROP COLUMN `custom_post_date`,
ADD COLUMN `short_name` VARCHAR(24) NOT NULL AFTER `id_matiere`;
