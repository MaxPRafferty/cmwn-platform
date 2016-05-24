Change My World Now API 
=======================

[![wercker status](https://app.wercker.com/status/971cb383ce3b1f71f539b9e090ccb362/m "wercker status")](https://app.wercker.com/project/bykey/971cb383ce3b1f71f539b9e090ccb362)


Requirements
------------

docker 1.11+
docker-compose 1.7+

Installing
----------

Using docker to install is simple.  1st make sure you have docker machine running:
 
```bash
$ docker-machine start
```

Afterwards you need to ensure that your host can talk to docker:

```bash
$ eval $(docker-machine env)
```

Then you can run the install script and follow the instructions after the script runs:

```bash
$ bin/install.sh
```

Development 
-----------

After you run the install script, you are ready to get started.  To start docker just run:

```bash
$ docker-composer up -d 
```

_Note: the -d flag means the container will run in the back ground_

FAQ:
---

__Q:__ I get the following error: "ERROR: Couldn't connect to Docker daemon - you might need to run 'docker-machine start default'."

__A:__ This happens when you restart your computer or when you open a new terminal window.  Run: ``eval $(docker-machine env)``
