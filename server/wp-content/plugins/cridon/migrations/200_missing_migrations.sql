ALTER TABLE `cri_question` ADD COLUMN `mobile_push_token` VARCHAR(255) NULL AFTER `crpcen`, ADD COLUMN `mobile_notification_status` TINYINT(1) DEFAULT 0 NULL AFTER `mobile_push_token`, ADD COLUMN `mobile_device_type` VARCHAR(50) NULL AFTER `mobile_notification_status`;
ALTER TABLE `cri_question` CHANGE `content` `content` TEXT CHARSET utf8 COLLATE utf8_unicode_ci NULL;
