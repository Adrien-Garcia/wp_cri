#!/usr/bin/env bash
set -e

echo "Start init-db.sh";

echo "Update database to local context ${SERVER_NAME}";
mysql  -proot -u root $MYSQL_DATABASE <<- EOM
UPDATE cri_posts SET guid = replace(guid, 'www.cridon-lyon.fr','${SERVER_NAME}');

UPDATE cri_posts SET post_content = replace(post_content, 'www.cridon-lyon.fr', '${SERVER_NAME}');
UPDATE cri_postmeta SET meta_value = replace(meta_value, 'www.cridon-lyon.fr', '${SERVER_NAME}');

UPDATE cri_options set option_value = "https://${SERVER_NAME}/" where option_name = "siteurl";
UPDATE cri_options set option_value = "https://${SERVER_NAME}/" where option_name = "home";
UPDATE cri_options set option_value = "https://${SERVER_NAME}/wp-content/uploads" where option_name = "upload_url_path";
UPDATE cri_posts SET post_content = REPLACE(post_content,'images.cridon-lyon.fr/wp-content/uploads','${SERVER_NAME}/wp-content/uploads');
UPDATE cri_posts SET guid = REPLACE(guid,'images.cridon-lyon.fr/wp-content/uploads','${SERVER_NAME}/wp-content/uploads');
EOM

echo "End init-db.sh";
