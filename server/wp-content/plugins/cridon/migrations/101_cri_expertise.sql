CREATE TABLE cri_expertise (
  `id` INT NOT NULL AUTO_INCREMENT,
  `label` VARCHAR(50) NOT NULL,
  `displayed` TINYINT(1) NOT NULL DEFAULT 0,
  `label_front` VARCHAR(100) NULL,
  `description` TEXT NULL,
  `order` INT UNSIGNED NULL,
  PRIMARY KEY (`id`)
);

INSERT INTO cri_expertise (`id`, `label` ,`displayed`, `label_front`, `description`, `order`) VALUES
(1, 'Initial', 1, 'Initial', '', 1),
(2, 'Medium', 1, 'Medium', '', 2),
(3, 'Expert', 1, 'Expert', '', 3)