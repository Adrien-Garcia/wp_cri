ALTER TABLE `cri_support` ADD COLUMN `icon` INT DEFAULT 1 NOT NULL;

TRUNCATE TABLE `cri_support`;

INSERT INTO cri_support (`id`, `label`, `value` ,`displayed`, `label_front`, `description`, `order`, `icon`) VALUES
(1, 'Lettre', 2, 0, 'Normal', '', 3, 1),
(2, 'Téléphone', 3, 0, 'Téléphone', '', 4, 1),
(3, 'Visite', 6, 0, 'Visite', '', 5, 1),
(4, 'Messagerie Diane', 1, 0, 'Messagerie Diane', '', 6, 1),
(5, 'Non facturé', 0, 0, 'Non facturé', '', 7, 1),
(6, 'Urgent 48H', 20, 0, 'Urgent', '', 1, 3),
(7, 'Urgent Semaine', 6, 1, 'Semaine', '', 2, 2),
(8, '3 à 4 semaines', 2, 1, '3 à 4 semaines', 'Cette prestation permet d’obtenir sous 3 à 4 semaines, une consultation écrite portant sur une problématique juridique simple.', 9, 1),
(9, '2 jours', 4, 1, '2 jours', 'Cette prestation permet d’obtenir sous 2 jours ouvrés, une consultation écrite portant sur une problématique juridique simple.', 8, 3),
(10, '5 jours', 6, 1, '5 jours', 'Cette prestation permet d’obtenir sous 5 jours ouvrés, une consultation écrite portant sur une problématique juridique complexe.', 10, 2),
(11, 'RDV Téléphonique', 3, 1, 'RDV Téléphonique', 'Cette prestation permet d’obtenir sous 5 jours ouvrés, une consultation orale portant sur une problématique juridique complexe.', 11, 2),
(12, '3 à 4 semaines', 6, 1, '3 à 4 semaines', 'Cette prestation permet d’obtenir sous 3 à 4 semaines, une consultation écrite portant sur une problématique juridique très complexe.', 12, 1),
(13, 'Dossier', 0, 1, 'Dossier', 'Cette prestation permet d’obtenir sur devis, une consultation écrite portant sur un dossier juridique très complexe.', 13, 1)
