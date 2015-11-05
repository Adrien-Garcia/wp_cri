CREATE TABLE `cri_user_cridon` (
  `id` INT(10) NOT NULL AUTO_INCREMENT,
  `id_erp` VARCHAR(5) NOT NULL,
  `profil` VARCHAR(255) NOT NULL DEFAULT 0,
  `last_connection` DATETIME NOT NULL,
  `id_wp_user` BIGINT(20) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_cri_user_cridon_cri_users1_idx` (`id_wp_user` ASC)
);