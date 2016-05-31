ALTER TABLE `cri_fonction` CHANGE `label` `label` VARCHAR(50) CHARSET utf8 COLLATE utf8_unicode_ci NULL;

TRUNCATE TABLE `cri_fonction`;

INSERT INTO cri_fonction (`id`, `label`, `displayed`) VALUES
(1, "Notaire", 1),
(2, "Notaire associé", 1),
(3, "Notaire associée", 1),
(4, "Notaire salarié", 1),
(5, "Notaire salariée", 1),
(6, "Notaire gérant", 1),
(7, "Notaire gérante", 1),
(8, "Notaire suppléant", 1),
(9, "Notaire suppléante", 1),
(10, "Notaire administrateur", 1),
(11, "Président de Chambre", 1),
(12, "Président de Conseil Régional", 1),
(13, "Délégué CRIDON", 1),
(14, "Directeur", 1),
(15, "Directeur général", 1),
(16, "Directeur Département Juridique", 1),
(17, "Conseiller juridique", 1),
(18, "Assistant", 1),
(19, "Assistante", 1),
(20, "Notaire honoraire", 1),
(21, "Secrétaire Général", 1),
(22, "Secrétaire", 1),
(23, "Second rapporteur", 1),
(24, "Professeur de droit", 1),
(25, "Trésorier", 1),
(26, "Chargé de développement", 1),
(27, "Collaborateur/Collaboratrice", 1),
(28, "Géomètre", 1);