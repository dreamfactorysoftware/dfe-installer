## DreamFactory Enterprise(tm) Installer v1.2

Dreamfactory Enterprise should be installed on a 64 bit system. Currently only Debian / Ubuntu distributions are supported. Ideally, the system should be minimal, as the installer will pull many packages.
For all installations:

```bash
$ sudo apt-get -qq update && sudo apt-get -y -qq upgrade
```

The installer now supports multiple versions of php. Follow the kickoff instructions below for your php version, other php packages will be installed by the installer later:

###PHP 5.x

```bash
$ sudo apt-get install -y -q php5 puppet git
```

###PHP 7.0.x
To install PHP 7 on most distributions, currently you have to add a repo to apt. Perform this before installing PHP 7:

```bash
$ sudo add-apt-repository ppa:ondrej/php
$ sudo apt-get update
$ sudo apt-get install php7.0 puppet git
```

Continuing for all installations, now clone the dfe-installer in your home directory:


```bash
$ cd ~
$ git clone git@github.com:dreamfactorysoftware/dfe-installer
$ cd dfe-installer
```
