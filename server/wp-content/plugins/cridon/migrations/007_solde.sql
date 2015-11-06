CREATE TABLE `cri_solde` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_number` varchar(100) NOT NULL,
  `quota` varchar(100) NOT NULL DEFAULT '0',
  `type_support` int(11) NOT NULL DEFAULT '0',
  `nombre` varchar(100) NOT NULL DEFAULT '0',
  `points` varchar(100) NOT NULL DEFAULT '0',
  `date_arret` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;