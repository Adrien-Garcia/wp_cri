ALTER TABLE `cri_etude` ADD `next_subscription_level` INT NULL AFTER `subscription_level`;
ALTER TABLE `cri_etude` ADD `start_subscription_date` DATE NULL AFTER `next_subscription_level`;
ALTER TABLE `cri_etude` ADD `end_subscription_date` DATE NULL AFTER `start_subscription_date`;
ALTER TABLE `cri_etude` ADD `echeance_subscription_date` DATE NULL AFTER `end_subscription_date`;
ALTER TABLE `cri_etude` ADD `subscription_price` INT NULL AFTER `echeance_subscription_date`;
ALTER TABLE `cri_etude` ADD `next_subscription_price` INT NULL AFTER `subscription_price`;