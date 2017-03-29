CREATE TABLE IF NOT EXISTS `cri_demarche` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `type` INT NOT NULL,
  `date` DATE NOT NULL,
  `notaire_id` INT NOT NULL,
  `session_id` INT NULL DEFAULT 0,
  `formation_id` INT NULL DEFAULT 0,
  `details` TEXT NULL,
  `commentaire_client` TEXT NULL,
  `commentaire_cridon` TEXT NULL,
  PRIMARY KEY (`id`));
