Virtual machine build
=====================

This file describe the way to build a virtual machine for development environment.
For local development environment installation, use the [local development environment guide](./local-developement-environment.md).

Development virtual machine
---------------------------

The `docker/virtual-machine/` directory contain files used by [Packer](https://packer.io/) to build a machine with all required dependencies plus an NFS export and a Samba server.
The virtual machine can be built for Qemu and VirtualBox.

### Dependencies

* [Packer](https://learn.hashicorp.com/tutorials/packer/getting-started-install)
* [Qemu](https://www.qemu.org/download/) and/or [VirtualBox](https://www.virtualbox.org/wiki/Downloads)

### How to

The last step can take a long time.
You can visualize the virtual machine installation during the process.
The virtual machine will be stopped automatically if the installation succeeds or fails.

1. Go into `docker/virtual-machine/` directory
2. Run `packer build dev.json` (you can build one provider only with `packer build --only=qemu dev.json` or `packer build --only=virtualbox-iso dev.json`)

You don't need to do anything during the build.
If anything requires a manual step, this is a bug.

When finised, you'll have one or more `output-*` directories (depending on the builders you ran) containing the generated virtual images.

### Technical details

Following the `dev.json` file, know that Packer follow these steps automatically:

* download the Debian 10 netinstall ISO and check its sha256 sum
* mount an HTTP server so the Debian preseed file will be available for the machine
* boot the ISO in the builder and wait five seconds
* type the `boot_commands` instruction so the ISO use the preseed file (reachable with HTTP)
* let the installation process for at most 30 minutes (during this time, the Debian installer will follow the `preseed-dev.cfg` file, no manual step required)
* when the machine rebooted, SSH into the machine and run the `install-dev.sh` script (which configure file sharing and install Docker)
* after running the script, shutdown the virtual machine
