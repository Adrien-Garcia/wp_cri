ALTER TABLE `cri_expertise` ADD COLUMN IF NOT EXISTS `document` VARCHAR(256) DEFAULT NULL;

TRUNCATE TABLE `cri_expertise`;

INSERT INTO cri_expertise (`id`, `label` ,`displayed`, `label_front`, `description`, `order`, `document`) VALUES
(1, 'Initial', 1, 'Initial', '', 1, 'documentsCridon/Expertises.pdf'),
(2, 'Medium', 1, 'Medium', '', 2, 'documentsCridon/Expertises.pdf'),
(3, 'Expert', 1, 'Expert', '', 3, 'documentsCridon/Expertises.pdf');