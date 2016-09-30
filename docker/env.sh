#!/usr/bin/env bash
#
# environnement variables for running the project on docker
#
# db backup name pattern
export DB_BACKUP_PATTERN="wp_cridon_*.sql.gz"
# Path of the db backup on the server
export DB_BACKUP_DIR=/vol/nfs_backup_sql/cridon
# db backup server
export DB_BACKUP_SERVER=aotools.host.addonline.fr
# local server name
export SERVER_NAME=wp-cridon.$JETPULP_USERNAME.jetpulp.dev
# database name
export MYSQL_DATABASE=wp_cridon