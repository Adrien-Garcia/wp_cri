ALTER TABLE `cri_formation` ADD COLUMN IF NOT EXISTS `id_form` VARCHAR(256) NOT NULL COMMENT 'Formation ID in ERP';

ALTER TABLE `cri_formation` ADD COLUMN IF NOT EXISTS `csn` VARCHAR(256) NULL COMMENT 'Numero CSN';

CREATE TABLE IF NOT EXISTS `cri_formation_matiere` (
    formation_id INT NOT NULL REFERENCES `cri_formation` (id)
    ON DELETE CASCADE ON UPDATE CASCADE,
    matiere_id INT UNSIGNED NOT NULL REFERENCES `cri_matiere` (id)
    ON DELETE CASCADE ON UPDATE CASCADE,
    PRIMARY KEY (formation_id , matiere_id)
);

CREATE TABLE IF NOT EXISTS `cri_formation_juriste` (
    formation_id INT NOT NULL REFERENCES `cri_formation` (id)
    ON DELETE CASCADE ON UPDATE CASCADE,
    juriste_id INT UNSIGNED NOT NULL REFERENCES `cri_notaire` (id)
    ON DELETE CASCADE ON UPDATE CASCADE,
    PRIMARY KEY (formation_id , juriste_id)
);

