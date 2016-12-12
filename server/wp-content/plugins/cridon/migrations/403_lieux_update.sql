DROP TABLE `cri_lieu`;

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
