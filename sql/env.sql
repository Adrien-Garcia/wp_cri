# Environnement : devl vers prod
update wp_options set option_value = "URLDEPROD" where option_name in("siteurl", "home");

# changer url relative des posts dans la table wp_posts
UPDATE wp_posts SET guid = replace(guid, 'http://www.ancien-site.com','http://www.nouveau-site.com');

# Copier / Remplacer contenu des postes
UPDATE wp_posts SET post_content = replace(post_content, 'http://www.ancien-site.com', 'http://www.nouveau-site.com');

UPDATE wp_postmeta SET meta_value = replace(meta_value, 'http://www.ancien-site.com', 'http://www.nouveau-site.com');