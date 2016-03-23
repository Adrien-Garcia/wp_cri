ALTER TABLE `cri_etude` ADD `start_subscription_date_veille` DATE NULL AFTER `subscription_level`;
ALTER TABLE `cri_etude` ADD `end_subscription_date_veille` DATE NULL AFTER `start_subscription_date_veille`;
ALTER TABLE `cri_etude` ADD `subscription_price` INT NULL AFTER `end_subscription_date_veille`;