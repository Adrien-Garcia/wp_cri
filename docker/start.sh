#!/usr/bin/env bash

#
# Path of the db backup on the server
# Pour le cridon : pas de dump sur le serveur aotools car les données ne sont pas hébergées par JETPULP
# L'export suivant est le lien de nom de ma db locale - '00_'
export DB_BACKUP_PATTERN=wp_cridon_20160329.sql.gz
#export DB_BACKUP_DIR=/vol/nfs_backup_sql/mutu134/mysql/mutu134
#export DB_BACKUP_SERVER=aotools.host.addonline.fr

start=$(dirname "$0")/../../jetpulper/docker/start.sh
if [ -f $start ]
then
    . $start
else
    echo "You need to clone git@git.jetpulp.hosting:dev/jetpulper.git in the same workspace"
fi


