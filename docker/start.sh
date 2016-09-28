#!/usr/bin/env bash
# source variables
. $(dirname "$0")/env.sh

#init data containers
docker run --name wp_cridon-dbdata -v /var/lib/mysql tianon/true /true &> /dev/null

echo ""
echo "Setup database when restoration is completed with this commands : "
echo "docker-setup-db"
echo ""

if [ ! -f "../server/.htaccess"]
then
    echo ""
    echo "Copying htaccess"
    cp ../conf/local/.htaccess ../server/
fi

if [ ! -f "../server/wp-config.php"]
then
    cp ../conf/local/wp-config.php ../server/
fi

start=$(dirname "$0")/../../jetpulper/docker/start.sh
if [ -f $start ]
then
    . $start
else
    echo "You need to clone git@git.jetpulp.hosting:dev/jetpulper.git in the same workspace"
fi