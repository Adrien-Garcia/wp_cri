#!/usr/bin/env bash
set -e

echo "Start init-db.sh";

echo "Update database to local context ${SERVER_NAME}";
mysql  -proot -u root $MYSQL_DATABASE <<- EOM
EOM

echo "End init-db.sh";
