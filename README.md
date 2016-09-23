## DreamFactory Gold(tm) Installer v1.2.1

DreamFactory Gold may now be installed on both Debian (Ubuntu 14.04/16.04) and RHEL/Centos (7.x). This installer has also been tested on Oracle Linux 7.2. The installer will automatically select the appropriate packages based on your distribution.

###Minimum Requirements
The installer utility installs DreamFactory Gold as an **all-in-one** package. A 64 bit Ubuntu or RHEL/Centos machine is required with a minimum of 8GB of RAM. For production environments, we recommend at least 16GB of RAM and at least 4 cores.

###Installation
Before installation, we recommend having as minimal distribution as possible. The installer will set up a full stack environment on your server and having a clean, minimal distribution will reduce the chance of any potential conflicts with existing packages during the install.

### Installation for RHEL/Centos 7.x

#### 1. Update yum

```bash
$ sudo yum update -y
```
#### 2. Install PHP7, Git, Puppet, composer, vim

__Add Webtatic Repos__
_RHEL/Centos versions 7.x_
```bash
$ sudo rpm -Uvh https://dl.fedoraproject.org/pub/epel/epel-release-latest-7.noarch.rpm
$ sudo rpm -Uvh https://mirror.webtatic.com/yum/el7/webtatic-release.rpm
```

Now install minimal packages:
```bash
$ sudo yum install php70w php70w-fpm git puppet composer -y
```

#### 3. Disable RequireTTY
Open the sudoers file and change the line:
```bash
$ sudo visudo

Default requiretty 
```
Put a "!" in front of requiretty so it reads
```
Default !requiretty
```
Save the file and quit.

#### 4. Disable SELINUX	
We recommend disabling selinux permanently. This will require a reboot of the server.
```bash
$ sudo vim /etc/selinux/config
```
Change to selinux=disabled. Save, quit and then reboot the server.
```bash
$ sudo reboot now
```
After the reboot, ensure that selinux is disabled:
```bash
$ sudo getenforce
Disabled
```
#### 5. Download and run the DFE Installer
```bash
$ git clone https://github.com/dreamfactorysoftware/dfe-installer.git
$ cd dfe-installer
    
$ php -S 0.0.0.0:8000 -t public/
```
Open a browser and point to http://127.0.0.1:8000 and fill out the config form. You may need to open port 8000 for your server as well or choose a different port.
Follow the instructions on the web form and submit it.

#### 6. Launch the Installer
From the dfe-installer directory, launch the install.sh script. 
```bash
$ sudo ./install.sh
```

### Installation for Ubuntu 14.04/16.04

#### 1. Add PPA Apt repository for PHP7
Do this for both Ubuntu 14 and 16 as well.
```bash
$ sudo add-apt-repository ppa:ondrej/php
$ sudo apt-get update
```
#### 2. Install PHP, Puppet and Git
```bash
$ sudo apt-get install php7.0 puppet git -y
```
#### 3. Download and run the DFE Installer
```bash
$ git clone https://github.com/dreamfactorysoftware/dfe-installer.git
$ cd dfe-installer

$ php -S 0.0.0.0:8000 -t public/
```
Open a browser and point to http://127.0.0.1:8000 and fill out the config form. You may need to open port 8000 for your server as well or choose a different port.
Follow the instructions on the web form and submit it.

#### 4. Launch the Installer
From the dfe-installer directory, launch the install.sh script. 
```bash
$ sudo ./install.sh
```

### Errors Encountered During Installation
During installation, if any errors are encountered, the installer will halt with an error message. Further detail can be found in the installation log file found in:
```
/tmp/dfe-installer.log
```
> If the installer halts and the error is subsequently handled, the installer may be run again and will pick up where it left off. 
> Be patient. Certain sections of the installer may take up to 10 minutes to complete and the entire installation process may take up to 
> 30 minutes.

