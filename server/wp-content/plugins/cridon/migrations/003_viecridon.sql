ALTER TABLE `cri_actu_cridon` RENAME `cri_vie_cridon`;
ALTER TABLE `cri_vie_cridon` DROP KEY `fk_cri_actus_cridon_wp_posts1_idx`;
ALTER TABLE `cri_vie_cridon` ADD KEY `fk_cri_vie_cridon_wp_posts1_idx` (`post_id` ASC);