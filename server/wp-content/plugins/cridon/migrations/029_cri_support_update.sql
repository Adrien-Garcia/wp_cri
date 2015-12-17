TRUNCATE TABLE cri_support;
INSERT INTO cri_support (`id`, `label`, `value` ,`displayed`, `label_front`, `description`, `order`) VALUES
(1, 'Lettre', 2, 1, 'Normal', '', 3),
(2, 'Téléphone', 2, 0, 'Téléphone', '', 4),
(3, 'Visite', 6, 0, 'Visite', '', 5),
(4, 'Messagerie Diane', 1, 1, 'Messagerie Diane', '', 6),
(5, 'Non facturé', 0, 1, 'Non facturé', '', 7),
(6, 'Urgent 48H', 20, 1, 'Urgent', '', 1),
(7, 'Urgent Semaine', 6, 1, 'Semaine', '', 2);