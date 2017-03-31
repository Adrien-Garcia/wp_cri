ALTER TABLE `cri_session`
CHANGE COLUMN `timetable` `timetable` VARCHAR(255) NULL DEFAULT NULL AFTER `is_full`,
ADD COLUMN `id_erp` VARCHAR(20) NOT NULL AFTER `id`,
ADD COLUMN `place` VARCHAR(255) NULL AFTER `timetable`,
ADD COLUMN `time_unit` VARCHAR(255) NULL AFTER `place`,
ADD COLUMN `time_unit_nb` INT NULL AFTER `time_unit`,
ADD COLUMN `price` VARCHAR(255) NULL AFTER `time_unit_nb`,
ADD INDEX `ERP` (`id_erp` ASC);

TRUNCATE `cri_session`;

CREATE TABLE IF NOT EXISTS `cri_session_matiere` (
    session_id INT NOT NULL REFERENCES `cri_session` (id)
    ON DELETE CASCADE ON UPDATE CASCADE,
    matiere_id INT UNSIGNED NOT NULL REFERENCES `cri_matiere` (id)
    ON DELETE CASCADE ON UPDATE CASCADE,
    PRIMARY KEY (session_id , matiere_id)
);

CREATE TABLE IF NOT EXISTS `cri_session_juriste` (
    session_id INT NOT NULL REFERENCES `cri_session` (id)
    ON DELETE CASCADE ON UPDATE CASCADE,
    juriste_id INT UNSIGNED NOT NULL REFERENCES `cri_notaire` (id)
    ON DELETE CASCADE ON UPDATE CASCADE,
    PRIMARY KEY (session_id , juriste_id)
);
