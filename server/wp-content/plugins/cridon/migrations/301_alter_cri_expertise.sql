ALTER TABLE `cri_expertise` ADD COLUMN IF NOT EXISTS `document` VARCHAR(256) DEFAULT NULL;

TRUNCATE TABLE `cri_expertise`;

INSERT INTO cri_expertise (`id`, `label` ,`displayed`, `label_front`, `description`, `order`, `document`) VALUES
(1, "Initial", 1, "Initial", "Une consultation courte, simple et rapide pour éclairer un point de droit, vérifier une interprétation.", 1, "documentsCridon/Expertises.pdf"),
(2, "Medium", 1, "Medium", "Une consultation sur un domaine du droit,qui demande une recherche approfondie de la part d'un ou plusieurs juristes consultants.", 2, "documentsCridon/Expertises.pdf"),
(3, "Expert", 1, "Expert", "Une consultation complexe qui demande l'analyse d'un ou plusieurs juristes consultants.", 3, "documentsCridon/Expertises.pdf");