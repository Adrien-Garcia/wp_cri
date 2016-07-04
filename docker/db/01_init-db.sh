#!/usr/bin/env bash
set -e

echo "Start init-db.sh";

echo "Update database to local context ${SERVER_NAME}";
mysql  -proot -u root $MYSQL_DATABASE <<- EOM
UPDATE cri_posts SET guid = replace(guid, 'http://www.cridon-lyon.fr','http://${SERVER_NAME}');

UPDATE cri_posts SET post_content = replace(post_content, 'http://www.cridon-lyon.fr', 'http://${SERVER_NAME}');
UPDATE cri_postmeta SET meta_value = replace(meta_value, 'http://www.cridon-lyon.fr', 'http://${SERVER_NAME}');

UPDATE cri_options set option_value = "http://${SERVER_NAME}/" where option_name = "siteurl";
UPDATE cri_options set option_value = "http://${SERVER_NAME}/" where option_name = "home";
UPDATE cri_options set option_value = "http://${SERVER_NAME}/wp-content/uploads" where option_name = "upload_url_path";
UPDATE cri_posts SET post_content = REPLACE(post_content,'http://images.cridon-lyon.fr/wp-content/uploads','http://${SERVER_NAME}/wp-content/uploads');
UPDATE cri_posts SET guid = REPLACE(guid,'http://images.cridon-lyon.fr/wp-content/uploads','http://${SERVER_NAME}/wp-content/uploads');
EOM

echo "End init-db.sh";
