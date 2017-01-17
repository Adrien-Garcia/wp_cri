#!/usr/bin/env bash

#
# Set environnement variables for running the project on docker
#
function init {

    export COMPOSE_PROJECT_NAME=wpcridon
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
    # every vitural_host separated by , (used for nginx proxy)
    export VIRTUAL_HOST=$SERVER_NAME,images-$SERVER_NAME,static-$SERVER_NAME
    # timeout for DB restoring waiting (default 3m0s)
    export DB_RESTORE_TIMEOUT=5m0s

}

#
# OVERRIDE functions for specific cases :
# init-data-containers
# delete-data-containers

