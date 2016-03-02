Change My World Now API 
=======================

Requirements
------------

Please see the [composer.json](composer.json) file.

Installation
------------

### Vagrant

If you prefer to develop with Vagrant, there is a basic vagrant recipe included with this project.

This recipe assumes that you already have Vagrant installed. The virtual machine will try to use localhost:8080 by
default, so if you already have a server on this port of your host machine, you need to shut down the conflicting
server first, or if you know how, you can reconfigure the ports in Vagrantfile.

Assuming you have Vagrant installed and assuming you have no port conflicts, you can bring up the Vagrant machine
with the standard `up` command:

```
vagrant up
```

When the machine comes up, you can ssh to it with the standard ssh forward agent:

```
vagrant ssh
```

The web root is inside the shared directory, which is at `/vagrant`. Once you've ssh'd into the box, you need to cd:

```
cd /vagrant
```

For vagrant documentation, please refer to [vagrantup.com](https://www.vagrantup.com/)

### Host

Vagrant is configured for api-local.changemyworldnow.com to 192.168.56.101
Update your host file to point to that IP

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
$ php public/index.php phinx migrate -e zf2
```

#### DB Seeding

Currently only games and user name candidates will be seeded.  To run the seed:

```
$ vagrant ssh
$ cd /var/www
$ php vendor/bin/phinx seed:run -c config/seed.phinx.yml 
```

