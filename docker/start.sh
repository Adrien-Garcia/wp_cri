#!/usr/bin/env bash

#
# Path of the db backup on the server
# Attenion pour le cridon n'est pas hébergé chez jetpulp, mais on stock un dump sur aotools , il n'est pas forcément très "à jour"
export DB_BACKUP_PATTERN=wp_cridon_20*.sql.gz
export DB_BACKUP_DIR=/vol/nfs_backup_sql/cridon
export DB_BACKUP_SERVER=aotools.host.addonline.fr

start=$(dirname "$0")/../../jetpulper/docker/start.sh
if [ -f $start ]
then
    . $start
else
    echo "You need to clone git@git.jetpulp.hosting:dev/jetpulper.git in the same workspace"
fi


