Local development environment
=============================

This document explains how to use the local environment and on which technologies it relies.

For your first run, install listed dependencies and use the setup documented in the "How to use" section.


Virtual machine
---------------

You can run the local development inside a Debian virtual machine.
If you want to build the vitual machine, follow the [virtual machine build guide](./virtual-machines-build.md).

If you decide to use this virtual machine, you don't need to read the "dependencies" section.

If you use Linux and prefere a raw and responsive local environment, see the [dependencies section](#dependencies).

In order to run the VM, all you'll need is:

* [Qemu](https://www.qemu.org/download/) or [VirtualBox](https://www.virtualbox.org/wiki/Downloads)
* use at least 2 CPUs and 2Go of RAM (4 CPUs and 8 Go recommended) for the virtual machine
* edit your hosts file (see below)

### Hosts file

The host file is location is:

* on Windows, `C:\windows\system32\drivers\etc\hosts`
* on MacOS, Linux, BDS or other Unix-like, `/etc/hosts`

You need to add multiple domains linked to your VM's IP:

* `devdocker.netframe.online`
* `work.devdocker.netframe.online`
* `<workspace-name>.devdocker.netframe.online` (replacing the `<workspace-name>` with the name of the Netframe workspace you'll create)

Any new workspace on the local environment will have its own domain `<workspace-name>.devdocker.netframe.online` which you'll need to add to your host files.

For example, if your VM IP is `192.168.100.200` and you have a *Netframe* workspace and a *Test* workspace, you'll add:

```
192.168.100.200 devdocker.netframe.online work.devdocker.netframe.online # fixed domains
192.168.100.200 netframe.devdocker.netframe.online test.devdocker.netframe.online # workspaces domains
```

### User

The default user is `netframe`, its password is `netframe`.
This user has `sudo` rights with its own password so you can do whatever you want.
You don't need `sudo` to run Docker commands as the user is in the `docker` group.

### Included softwares

* all required [dependencies](#dependencies) in order to run the project (Docker, Docker Compose, GNU make)
* an SSH server (you can SSH to `netframe` with its password and use `sshfs`)
* a NFS server and a Samba server, both using the `netframe` user with the `netframe` password and sharing the `~/share/` directory
* some utils (htop, curl, httpie, …)

You can install other softwares with APT, the Debian package manager.


Dependencies
------------

* [Docker](https://docs.docker.com/engine/install/)
* for Mac and Linux distributions, [user's right to use docker](https://docs.docker.com/engine/install/linux-postinstall/#manage-docker-as-a-non-root-user)
* [docker-compose](https://docs.docker.com/compose/install/)
* GNU make

This environment has not been tested on Windows 10 yet.
If you use Windows 10, you can try this local environment with [WSL 2](https://docs.microsoft.com/en-us/windows/wsl/install-win10) and install the [Linux kernel upadte package](https://docs.microsoft.com/windows/wsl/wsl2-kernel) and Ubuntu/Debian (installing the required dependencies).

### Docker for Mac

By default, Docker for Mac use 2 CPU cores and 2GB of memory.
You can (and should) change these limits on Docker settings.

Minimal: 2 CPU cores and 4GB of RAM.
Recommended: 4 CPU cores and 8GB of RAM.
Feel free to take something between those values (eg 3 CPU cores and 6GB of RAM).

### Modern Linux distros configuration

#### CGroups

Docker only support first CGroup version at the time of this writing.
If your distro uses CGroup v2 by default, add this kernel argument (before rebooting) in order to use v1: `systemd.unified_cgroup_hierarchy=0`.

#### Firewall

Docker Compose creates internal networks and need to have authorization to have network access and make local connections, you'll need to run these commands:

```sh
sudo firewall-cmd --permanent --zone=trusted --add-interface=docker0
sudo firewall-cmd --permanent --zone=FedoraWorkstation --add-masquerade
sudo firewall-cmd --reload
sudo systemctl restart docker.service # or sudo service docker restart
```


Quickly: how to use
-------------------

If you use the [virtual machine](#virtual-machine), you have to run following steps into the VM.
Otherwise, you can run these steps in the host.

Tip for virtual machine: the `~/share/` directory can be mounted with NFS and Samba, you should clone the repo inside it.

### Setup

After cloning this repo, you should ensure your current directory rights are OK: `sudo chown -R $USER:$USER ./ && sudo chmod -R o+rx ./`.

Note that Nginx and PHP-FPM in Docker, using volumes, seem to have issues when the working directory path contains a dot (`.`).

Clone the project's repository if you don't have it locally and `cd` into the project's directory.

Run `make setup`.

If you have issues during the setup, it can be an authorization problem in a project's directory like `node_modules/` or `vendor/`.

### Launch

Just run `make start` to start the project. You can stop it by pressing `Ctrl-C` or running `make stop` in another terminal.

You can see the project in your browser at [work.devdocker.netframe.online:8000](http://work.devdocker.netframe.online:8000).

### Leave/clean

If you won't contribute anymore to this project, you should run `make clean` before removing the project's directory, otherwise you'll have some dandling Docker containers and volumes on your computer.

After that, you can remove the project's directory of your disk.


Using the environment
---------------------

### Main commands

TL;DR: run `make help` to have a list of commands

* `make setup` for project base setup
* `make update` for services updates (when updating `docker/laravel-base-env`, `docker-compose.yml` or `docker/*.Dockerfile`
* `make start`/`make stop`/`make restart` for starting/stopping/restarting the environment (`Ctrl-C` can also stop the environment)
* `make clean` for removing generated containers, networks and volumes
* `make enter:*` to enter a specific container (see `make help` for possible services listing)
* `make services:list` give a list of all services (for more informations, see the `docker-compose.yml` file)
* `make services:status` give the status of existing services

Note: some commands are just aliases for `docker-compose` commands to abstract the underlying technologie for less technical users.

### Utils services

You can use :

* a [phpMyAdmin](https://www.phpmyadmin.net/) on [localhost:8080](http://localhost:8080) to access the MySQL/MariaDB database
* a [Kibana](https://www.elastic.co/kibana) on [localhost:8081](http://localhost:8081) to access the ElasticSearch server

### For more advanced tasks

The environment is based on Docker Compose so you can use an `docker-compose` (or `docker`) command you want.

Tips about Docker Compose:

* use `docker-compose ps` to see which services are runnning or `docker-compose top` for more technical details
* if `make stop` or `docker-compose stop` doesn't work, `docker-compose kill` can help you
* `docker-compose logs` allow you to see services logs, `docker-compose logs [service]` can be used to list a specific service's logs
* `docker-compose help` will give you more commands
* as `make enter:*` launch a "fresh" container, if you want to enter in a running container you can run `docker-compose exec [service] bash`
* to use a package manager, you can `make enter:php` (composer) or `make enter:node` (npm)


What is created for the environment
-----------------------------------

By default, Docker Compose create a Docker network for the project.

The `docker-compose.yml` files describe other Docker objects which will be created:

* images (custom for services with a `build` section, downloaded from Docker Hub for services with an `image` section)
* containers (one for each service, can be more if a `docker-compose run` or `make enter:*` command is used)
* volumes (see the `volumes` section at the bottom of the file)

When using `docker-compose down`, volumes aren't removed.
That's why the `make clean` command is here, because it ensures those objects are removed.recommended, because it ensures volumes are removed.


Troubleshooting
---------------

In case of problem with the environment,you can try different things:

* run a `make stop` in order to stop all services, or a simple `make restart`
* you can debug a faulty container by entering using `make enter:*` but you'll have a "fresh" container
* if you want to enter a running container, you can use `docker-compose exec [service] bash`
* if a container has starting problems (especially Laravel Echo Server), you can use `docker-compose rm [service]` to remove it (don't worry, it'll be created back at next `make start`)
* a `make clean && make setup` can be a good shot in case of big problem but you'll lose all data

If you really can't fix your issue, maybe a `make clean` and a fresh `git clone` followed by a `make setup` can be a solution.


Tips and quirks
---------------

### Enable OnlyOffice

RUn `make enter:php` and, inside the container, `php artisan enable:onlyoffice <instance_slug>`, replace `<instance_slug>` by the slug of the instance you want.

First, follow the **How to connect** part of the [super admin guide](admin-user.md).
Then, open the (management interface)[http://work.devdocker.netframe.online:8000/management]

Here, go ton `Instances` and click on the one which need Only Office.
In `Apps`, check `Only Office`.
