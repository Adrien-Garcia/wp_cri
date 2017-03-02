TRUNCATE `cri_organisme_etude`;

ALTER TABLE `cri_organisme_etude`
CHANGE COLUMN `crpcen` `crpcen` VARCHAR(10) NOT NULL ,
ADD PRIMARY KEY (`id_organisme`, `crpcen`);
