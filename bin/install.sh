#!/usr/bin/env bash

cat $PWD/bin/splash.txt
target_docker_name="api"

bash $PWD/bin/setup-docker.sh $target_docker_name
if [ $? != 0 ]
then
    >&2 echo "[api-installer] no $target_docker_name docker-machine running"
    exit 1
fi

echo "[api-installer] Building docker containers"
eval $(docker-machine env api)

DOCKER_IP=`docker-machine ip $target_docker_name`

if [ -z "$DOCKER_IP" ]
then
    >&2 echo "Looks like we did not the correct docker-machine set up"
    exit 1
fi

echo "[api-installer] Copying development configs"

cp config/development.config.php.dist config/development.config.php
cp config/autload/local.php.dist config/autoload/local.php

echo "[api-installer] Installing git hooks"
bash $PWD/bin/install-git-hooks.sh


docker-compose build
if [ "composer.json" -nt "vendor/" ]; then
    if [ ! -e "vendor//" ];
    then
        echo "[api-installer] New composer.json installing new packages"
        docker-compose run php composer install
    else
        echo "[api-installer] New composer.json updating packages"
        docker-compose run php composer update
    fi
else
    echo "[api-installer] Composer appears to be up to date"
fi

docker-compose start
echo "[api-installer] Allowing mysql to start"
sleep 3
docker-compose run php phinx migrate -c config/phinx.php -e dev
docker-compose run php phinx seed:run -c config/phinx.php -e dev


cat <<EOF

!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
!!                                                    !!
!!  You're all set and ready to go.  In order to hit  !!
!!  api-local.changemyworldnow.com, you need to add   !!
!!  the following to your /etc/hosts file:            !!
!!                                                    !!
!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

$DOCKER_IP    api-local.changemyworldnow.com

You then run:
docker-compose start
and you will be able to access the site locally in the browser

Happy Coding!

EOF
