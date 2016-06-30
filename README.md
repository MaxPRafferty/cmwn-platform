Change My World Now API 
=======================

[![wercker status](https://app.wercker.com/status/971cb383ce3b1f71f539b9e090ccb362/m "wercker status")](https://app.wercker.com/project/bykey/971cb383ce3b1f71f539b9e090ccb362)


Requirements
------------

docker 1.11+
docker-compose 1.7+
VirtualBox 5.0+

Installing
----------

To setup the site, just run 


```bash
$ bin/install.sh
```

Development 
-----------

After you run the install script, you are ready to get started.  To start docker just run:

```bash
$ eval $(docker-machine env api)
$ docker-composer up -d 
```

_Note: the -d flag means the container will run in the back ground_

FAQ:
---

__Q:__ I get the following error: "ERROR: Couldn't connect to Docker daemon - you might need to run 'docker-machine start default'."

__A:__ This happens when you restart your computer or when you open a new terminal window.  Run: ``eval $(docker-machine env api)``
