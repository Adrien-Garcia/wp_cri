CREATE TABLE cri_matiere (
    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `code` VARCHAR(5) NOT NULL,
    `label` VARCHAR(255) NOT NULL,
    `short_label` VARCHAR(20) NOT NULL,
    `displayed` TINYINT(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    UNIQUE KEY `UX_CODE` (`code`)
);

CREATE TABLE cri_competence (
    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `label` VARCHAR(255) NOT NULL,
    `displayed` TINYINT(1) NOT NULL DEFAULT 1,
    `code_matiere` VARCHAR(5) NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `X_FK_COMPETENCE_MATIERE` (`code_matiere` ASC)
);

CREATE TABLE cri_affectation (
  `id` INT NOT NULL AUTO_INCREMENT,
  `label` VARCHAR(20) NOT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE cri_support (
  `id` INT NOT NULL AUTO_INCREMENT,
  `label` VARCHAR(50) NOT NULL,
  `value` VARCHAR(20) NOT NULL,
  `displayed` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
);

CREATE TABLE `cri_civilite` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `label` VARCHAR(20) NOT NULL,
  `displayed` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
);

CREATE TABLE `cri_fonction` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `label` VARCHAR(20) NOT NULL,
  `displayed` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
);

CREATE TABLE `cri_sigle` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `label` VARCHAR(20) NOT NULL,
  `displayed` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
);

CREATE TABLE `cri_etude` (
  `crpcen` VARCHAR(10) NOT NULL,
  `id_sigle` INT(10) UNSIGNED NOT NULL,
  `office_name` VARCHAR(150) NOT NULL,
  `adress_1` VARCHAR(40) NOT NULL,
  `adress_2` VARCHAR(40) NOT NULL,
  `adress_3` VARCHAR(40) NOT NULL,
  `cp` VARCHAR(10) NOT NULL,
  `city` VARCHAR(40) NOT NULL,
  `office_email_adress_1` VARCHAR(80) NOT NULL,
  `office_email_adress_2` VARCHAR(80) NOT NULL,
  `office_email_adress_3` VARCHAR(80) NOT NULL,
  PRIMARY KEY (`crpcen`),
  INDEX `X_FK_CRI_ETUDE_CRI_SIGLE_ID` (`id_sigle` ASC)
);

CREATE TABLE `cri_notaire` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `category` VARCHAR(50) NOT NULL,
  `client_number` VARCHAR(100) NOT NULL,
  `first_name` VARCHAR(250) NOT NULL,
  `last_name` VARCHAR(250) NOT NULL,
  `crpcen` VARCHAR(100) NOT NULL,
  `web_password` VARCHAR(8) NOT NULL,
  `tel_password` VARCHAR(50) NOT NULL,
  `code_interlocuteur` VARCHAR(15) NOT NULL,
  `id_civilite` INT(10) UNSIGNED NOT NULL,
  `email_adress` VARCHAR(80) NOT NULL,
  `id_fonction` INT(10) UNSIGNED NOT NULL,
  `tel` VARCHAR(20) DEFAULT '' NOT NULL,
  `fax` VARCHAR(20) DEFAULT '' NOT NULL,
  `tel_portable` VARCHAR(20) DEFAULT '' NOT NULL,
  `date_modified` DATE NOT NULL,
  `id_wp_user` BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `X_FK_CRI_NOTAIRE_CRI_CIVILITY_ID` (`id_civilite` ASC),
  INDEX `X_CRI_NOTAIRE_CRI_FONCTION_ID` (`id_fonction` ASC),
  INDEX `X_FK_CRI_NOTAIRE_CRI_ETUDE_ID` (`crpcen` ASC),
  INDEX `X_FK_CRI_NOTAIRE_WP_USERS_ID` (`id_wp_user` ASC),
  INDEX `X_CLISNT_NUMBER` (`client_number`)
);

CREATE TABLE cri_document (
  `id` INT(10) NOT NULL AUTO_INCREMENT,
  `file_path` VARCHAR(250) NOT NULL,
  `download_url` VARCHAR(250) NOT NULL,
  `date_modified` DATETIME NOT NULL ,
  `type` VARCHAR(50) NOT NULL,
  `id_externe` INT(10) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_cri_document_cri_doc_type_idx` (`id_externe` ASC),
  INDEX `ix_cri_document_cri_doc_type_idx` (`type` ASC)
);

CREATE TABLE cri_question (
  `srenum` VARCHAR(15) NULL,
  `id` VARCHAR(100) NOT NULL,
  `client_number` VARCHAR(45) NOT NULL,
  `sreccn` VARCHAR(15) NOT NULL,
  `id_support` INT(10) NOT NULL,
  `id_competence_1` INT(10) NOT NULL,
  `resume` VARCHAR(80) NOT NULL,
  `content` VARCHAR(5000) NOT NULL,
  `id_affectation` INT(10) NOT NULL DEFAULT 1,
  `juriste` VARCHAR(15) NULL,
  `affectation_date` DATE NULL,
  `wish_date` DATE NULL,
  `real_date` DATE NULL,
  `yuser` VARCHAR(5) NULL,
  `treated` TINYINT(1) NOT NULL DEFAULT 0,
  `error` TINYINT(1) NOT NULL,
  `error_message` VARCHAR(250) NULL,
  `creation_date` DATE NULL,
  `date_modif` DATE NULL,
  `hour_modif` TIME NULL,
  `transmis_erp` TINYINT(1) NOT NULL DEFAULT 0,
  `confidential` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  INDEX `fk_cri_question_cri_affectation1_idx` (`id_affectation` ASC),
  INDEX `fk_cri_question_cri_support1_idx` (`id_support` ASC),
  INDEX `fk_cri_question_cri_competence1_idx` (`id_competence_1` ASC),
  UNIQUE INDEX `srenum_UNIQUE` (`srenum` ASC),
  INDEX `fk_cri_question_cri_notaire1_idx` (`client_number` ASC)
);

CREATE TABLE cri_veille (
  `id` INT NOT NULL AUTO_INCREMENT,
  `post_id` BIGINT(20) NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_cri_veille_wp_posts1_idx` (`post_id` ASC)
);

CREATE TABLE cri_flash (
  `id` INT NOT NULL AUTO_INCREMENT,
  `post_id` BIGINT(20) NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_cri_flash_wp_posts1_idx` (`post_id` ASC)
);

CREATE TABLE cri_actu_cridon (
  `id` INT NOT NULL AUTO_INCREMENT,
  `post_id` BIGINT(20) NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_cri_actus_cridon_wp_posts1_idx` (`post_id` ASC)
);

CREATE TABLE cri_formation (
  `id` INT NOT NULL AUTO_INCREMENT,
  `post_id` BIGINT(20) NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_cri_formation_wp_posts1_idx` (`post_id` ASC)
);

CREATE TABLE cri_cahier_cridon (
  `id` INT NOT NULL AUTO_INCREMENT,
  `post_id` BIGINT(20) NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_cri_cahier_cridon_wp_posts1_idx` (`post_id` ASC)
);

CREATE TABLE `cri_question_competence` (
  `id_competence` INT(10) NOT NULL,
  `id_question` INT(10) NOT NULL,
  INDEX `fk_cri_question_competence_cri_competence1_idx` (`id_competence` ASC),
  INDEX `fk_cri_question_competence_cri_question1_idx` (`id_question` ASC)
);

