Change My World Now API 
=======================

[![wercker status](https://app.wercker.com/status/971cb383ce3b1f71f539b9e090ccb362/m "wercker status")](https://app.wercker.com/project/bykey/971cb383ce3b1f71f539b9e090ccb362)


Requirements
------------

Please see the [composer.json](composer.json) file.

Installation
------------

### Vagrant

### Config Files

1st you need to take all the *.php.dist files and copy them to *.php in config/autoload
ex: db.local.php.dist -> db.local.php 

2nd copy config/development.config.php.dist to config/development.config.php

## Database Migration

Phinx is the primary module to perform DB migrations.  Currently there is a ZF2 Module that will
proxy calls to migrate the DB but not seed the database. 

#### Migration

To create all the tables follow these steps:

```
$ vagrant ssh
$ cd /var/www
$ php vendor/bin/phinx migrate -c config/phinx.php -e dev
```

#### DB Seeding

Currently only games and user name candidates will be seeded.  To run the seed:

```
$ vagrant ssh
$ cd /var/www
$ php vendor/bin/phinx seed:run -c config/phinx.php
```

