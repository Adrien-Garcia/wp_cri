UPDATE `cri_question` SET `date_modif` = NULL WHERE `date_modif` = '0000-00-00' ;#
UPDATE `cri_question` SET `hour_modif` = NULL WHERE  `hour_modif` = '00:00:00';#
UPDATE `cri_question` SET `affectation_date` = NULL WHERE  `affectation_date` = '00:00:00';#
UPDATE `cri_question` SET `wish_date` = NULL WHERE  `wish_date` = '00:00:00';#
UPDATE `cri_question` SET `real_date` = NULL WHERE  `real_date` = '00:00:00';