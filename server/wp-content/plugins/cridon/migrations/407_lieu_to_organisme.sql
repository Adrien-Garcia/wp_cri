ALTER TABLE `cri_lieu`
RENAME TO  `cri_organisme` ;

ALTER TABLE `cri_lieu_etude`
RENAME TO  `cri_organisme_etude` ;

ALTER TABLE `cri_session`
CHANGE COLUMN `id_lieu` `id_organisme` INT(11) NOT NULL ;
