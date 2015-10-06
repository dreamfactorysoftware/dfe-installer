#!/bin/bash
#
# @(#)$Id: install.sh,v 1.1.0 2015-10-05 dweiner/jablan $
#
# This file is part of DreamFactory Enterprise(tm)
#
# DreamFactory Enterprise(tm) Installer
# Copyright (c) 2012-2115 DreamFactory Software, Inc. All rights reserved.
#

##	Initial settings
. ./ansi.sh

VERSION=1.1.0
SYSTEM_TYPE=`uname -s`
MANIFEST_PATH=./resources/manifests/
ENV_FILE=./.env-install

## Who am I?
if [ $UID -ne 0 ]; then
   _error "This script must be run as root"
   exit 1
fi

## Basic usage statement
usage() {
	_msg "usage" ${_YELLOW} "${_ME}"
	exit 1
}

## Check for the puppet modules we need
_checkPuppetModules() {
    ## Create the hiera.yaml file for puppet
    echo "---
:backends:
  - yaml
  - json
:yaml:
  :datadir: /etc/puppet/hieradata
:json:
  :datadir: /etc/puppet/hieradata
:hierarchy: dreamfactory" > /etc/puppet/hiera.yaml

    [ ! -d "/etc/puppet/hieradata" ] && mkdir /etc/puppet/hieradata

    INSTALLED_MODULES=$(puppet module list)
    local _count=0

    for module in puppetlabs-stdlib puppetlabs-vcsrepo puppetlabs-mysql puppetlabs-apt puppetlabs-lvm puppetlabs-inifile wcooley-user_ssh_pubkey
    do
        if [[ ${INSTALLED_MODULES} != *"${module}"* ]]; then
            puppet module install ${module} >/dev/null
            if [ $? -ne 0 ] ; then
                _error "Error during installation of required puppet module \"${module}\""
                exit 1;
            fi
            _count=${_count}+1
        fi
    done

    _info "   > Installed ${_count} modules"
}

## Hard-coded defaults
export FACTER_DEFAULT_LOCAL_MOUNT_NAME=mount-local-1
export FACTER_FSTAB=/etc/fstab
export FACTER_MOUNT_OPTIONS=rw
export FACTER_LVM_NAME=dfe_lvm
export FACTER_VG_NAME=dfe_vg
export FACTER_USER_PWD=`openssl rand -base64 32`
export FACTER_PERCONA_VERSION=5.6
export FACTER_MYSQL_USER=mysql
export FACTER_MYSQL_GROUP=mysql
export FACTER_HOME=$HOME
export FACTER_PWD=${PWD}
export LC_ALL=en_US.UTF-8
export FACTER_DB_USER=dfe_user
export FACTER_DB_PWD=dfe_user
export FACTER_DB_HOST=localhost
export FACTER_DB_NAME=dfe_local
export FACTER_CONSOLE_VERSION=develop
export FACTER_CONSOLE_BRANCH=develop
export FACTER_DASHBOARD_VERSION=develop
export FACTER_DASHBOARD_BRANCH=develop
export FACTER_DSP_VERSION=develop
export FACTER_DSP_BRANCH=develop
export FACTER_APP_DEBUG=true

## Blanks
export FACTER_USER FACTER_GROUP
export FACTER_ADMIN_EMAIL FACTER_ADMIN_PWD
export FACTER_MOUNT_POINT FACTER_DOMAIN FACTER_GH_USER FACTER_GH_PWD
export FACTER_DEVICE FACTER_VG_SIZE FACTER_FSTYPE
export FACTER_SMTP_HOST FACTER_SMTP_PORT FACTER_MAIL_FROM_ADDRESS FACTER_MAIL_FROM_NAME
export FACTER_MAIL_USERNAME FACTER_MAIL_PASSWORD

## Header
sectionHeader " ${B1}DreamFactory Enterprise(tm)${B2} ${SYSTEM_TYPE} Installer v${VERSION}"

## Find settings file...
if [ -f ${ENV_FILE} ]; then
    . ${ENV_FILE}
else
    _error "No installation configuration file found. Please fill out the web form."
    exit 2
fi

_info "Checking system requirements..."

_checkPuppetModules

## Composite/aggregate values
export FACTER_STORAGE_USER=${FACTER_USER}
export FACTER_STORAGE_PATH=${FACTER_MOUNT_POINT}/${FACTER_STORAGE_PATH}
export FACTER_SSL_CERT_STUB=$(echo ${FACTER_DOMAIN} | tr '.' '-')
export FACTER_GITHUB_USER_INFO=${FACTER_GH_USER}\:${FACTER_GH_PWD}\@

_info "Installing..."

for manifest in $(ls ./resources/assets/manifests/*.pp)
do
	_info "Applying ${manifest}..."
	puppet apply ${manifest}

    if [ $? -ne 0 ]; then
	    break
    fi
done

_info "Complete!"
