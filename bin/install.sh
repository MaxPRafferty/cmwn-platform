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
        docker-compose run --rm php composer install
    else
        echo "[api-installer] New composer.json updating packages"
        docker-compose run --rm php composer update
    fi
else
    echo "[api-installer] Composer appears to be up to date"
fi

docker-compose start
echo "[api-installer] Allowing mysql to start"
sleep 3

echo "[api-installer] Migrating Database"
docker-compose run --rm php phinx migrate -c config/phinx.php -e dev

echo "[api-installer] Seeding Database"
docker-compose run --rm php phinx seed:run -c config/phinx.php -e dev

echo "[api-installer] Creating test database"
docker-compose run --rm php mysql --host="cmwn_api_mysql" -u root --password="cmwn_pass123" -e "CREATE DATABASE IF NOT EXISTS cmwn_test; GRANT ALL PRIVILEGES ON cmwn_test.* TO cmwn_user@'%' IDENTIFIED BY 'cmwn_pass'"

echo "[api-installer] Migrating test database"
docker-compose run --rm php phinx migrate -c config/phinx.php -e test

echo "[api-installer] Seeding test database"
docker-compose run --rm php phinx seed:run -c config/phinx.php -e test

echo "[api-installer] Creating development ssl keys"
docker-compose run --rm php openssl req -x509 -newkey rsa:2048 \
  -subj "/C=XX/ST=XXXX/L=XXXX/O=XXXX/CN=api-local.changemyworldnow.com" \
  -keyout "/var/www/data/ssl/key.pem" \
  -out "/var/www/data/ssl/cert.crt" \
  -days 3650 -nodes -sha256

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
