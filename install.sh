#!/bin/bash
#
# @(#)$Id: install.sh,v 1.1.32 2015-11-16 dweiner/jablan $
#
# This file is part of DreamFactory Enterprise(tm)
#
# DreamFactory Enterprise(tm) Installer
# Copyright (c) 2012-2016 DreamFactory Software, Inc. All rights reserved.
#

##	Initial settings
. ./ansi.sh

VERSION=1.1.32
SYSTEM_TYPE=`uname -s`
MANIFEST_PATH=./resources/assets/manifests
ENV_FILE=./storage/.env-install
PHP_BIN=`which php`
PHP_ENMOD_BIN=`which php5enmod`
LOG_FILE=/tmp/dfe-installer.log
DFE_UPDATE=false

## Basic usage statement
usage() {
	_msg "usage" ${_YELLOW} "${_ME} [-u|--update]"
	exit 1
}

## Check out the command line
while [[ $# > 0 ]] ; do
    key="$1"

    case ${key} in
        -h|--help) usage ;;
        -u|--update) DFE_UPDATE=true ;;
        *) ;; ## Default/ignored
    esac

    shift
done

## Who am I?
if [ $UID -ne 0 ]; then
   _error "This script must be run as root"
   exit 1
fi

if [ -z "${PHP_BIN}" ]; then
    _error "PHP v5.5+ must be installed to run this script."
    exit 1
fi

if [[ -n $1 ]]; then
    echo "Last line of file specified as non-opt/last argument:"
    tail -1 $1
fi

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

    for module in puppetlabs-stdlib puppetlabs-vcsrepo puppetlabs-mysql puppetlabs-apt puppetlabs-inifile wcooley-user_ssh_pubkey dhoppe-postfix
    do
        if [[ ${INSTALLED_MODULES} != *"${module}"* ]]; then
            puppet module install ${module} >/dev/null
            if [ $? -ne 0 ] ; then
                _error "Error during installation of required puppet module \"${module}\""
                exit 1;
            fi
            _count=$((_count+1))
        fi
    done

    [ ${_count} -ne 0 ] && _info "Installed ${_count} required puppet modules"

    ##  Update composer if already installed
    if [ -f "${FACTER_COMPOSER_BIN}" ]; then
        ${FACTER_COMPOSER_BIN} self-update --quiet
        _info "Composer updated to latest version"
    fi
}

## Defaults and executable locations
export FACTER_APP_DEBUG=true
export FACTER_DFE_UPDATE=${DFE_UPDATE}
export FACTER_PREFERRED_MAIL_PACKAGE=exim4
export FACTER_PHP_BIN=${PHP_BIN}
export FACTER_PHP_ENMOD_BIN=${PHP_ENMOD_BIN}
export FACTER_ARTISAN="${PHP_BIN} artisan"
export FACTER_COMPOSER_ROOT=/usr/local/bin
export FACTER_COMPOSER_BIN="${FACTER_COMPOSER_ROOT}/composer"
export FACTER_DEFAULT_LOCAL_MOUNT_NAME=mount-local-1
export FACTER_FSTAB=/etc/fstab
export FACTER_USER_PWD=`openssl rand -base64 32`
export FACTER_PERCONA_VERSION=5.6
export FACTER_NGINX_PATH=/etc/nginx
export FACTER_DEFAULT_PROTOCOL=http
export FACTER_RUN_USER=${USER}
export FACTER_LOG_USER=ubuntu
export FACTER_STATIC_ZONE_NAME=local
export FACTER_INSTALL_HOSTNAME=`/bin/hostname`
export FACTER_VENDOR_ID=dfe
export FACTER_DC_ES_PORT=9200
export FACTER_SUPPORT_EMAIL_ADDRESS=support@dreamfactory.com

## Needs to match DB name in dfe_local.schema.sql
export FACTER_DB_NAME=dfe_local
export FACTER_DB_DRIVER=mysql

## Paths
export FACTER_DOC_ROOT_BASE_PATH=/var/www
export FACTER_SERVER_CONFIG_PATH="${FACTER_DOC_ROOT_BASE_PATH}/launchpad/server/config"
export FACTER_RELEASE_PATH="${FACTER_DOC_ROOT_BASE_PATH}/_releases"
export FACTER_ELK_STACK_ROOT=/opt/elk

export FACTER_CONSOLE_ROOT="${FACTER_DOC_ROOT_BASE_PATH}/console"
export FACTER_DASHBOARD_ROOT="${FACTER_DOC_ROOT_BASE_PATH}/dashboard"
export FACTER_INSTANCE_ROOT="${FACTER_DOC_ROOT_BASE_PATH}/launchpad"

export FACTER_CONSOLE_RELEASE="${FACTER_RELEASE_PATH}/console"
export FACTER_DASHBOARD_RELEASE="${FACTER_RELEASE_PATH}/dashboard"
export FACTER_INSTANCE_RELEASE="${FACTER_RELEASE_PATH}/launchpad"

## Users & Branches
export FACTER_USER=dfadmin
export FACTER_GROUP=dfadmin
export FACTER_STORAGE_GROUP=dfadmin
export FACTER_MYSQL_USER=mysql
export FACTER_MYSQL_GROUP=mysql
export FACTER_HOME=$HOME
export FACTER_PWD=${PWD}
export FACTER_DB_USER=dfe_user
export FACTER_DB_PWD=dfe_user
export FACTER_DB_HOST=localhost
export FACTER_DB_NAME=dfe_local
export FACTER_CONSOLE_BRANCH=develop
export FACTER_DASHBOARD_BRANCH=develop
export FACTER_INSTANCE_BRANCH=develop
export FACTER_INSTANCE_CACHE_PATH=/tmp/.df-cache

## Blanks filled in by .env-install
export FACTER_ADMIN_EMAIL FACTER_ADMIN_PWD FACTER_GH_TOKEN
export FACTER_MOUNT_POINT FACTER_DOMAIN FACTER_LOG_PATH
export FACTER_DC_HOST FACTER_DC_PORT FACTER_DC_INDEX_TYPE FACTER_DC_ES_CLUSTER FACTER_DC_ES_EXISTS

## Rotate log
#[ -f "${LOG_FILE}" ] && mv "${LOG_FILE}" "${LOG_FILE}.1"

## Header
sectionHeader " ${B1}DreamFactory Enterprise(tm)${B2} ${SYSTEM_TYPE} Installer v${VERSION}"

## Find settings file...
if [ -f "${ENV_FILE}" ]; then
    . "${ENV_FILE}"
else
    _error "No installation configuration file found. Please fill out the web form."
    exit 2
fi

##  Check requirements
_checkPuppetModules

## Composite/aggregate values
export FACTER_STORAGE_USER=${FACTER_USER}
export FACTER_STORAGE_PATH=${FACTER_MOUNT_POINT}/${FACTER_STORAGE_PATH}
export FACTER_TRASH_PATH=${FACTER_MOUNT_POINT}/trash
export FACTER_INSTANCE_LOG_PATH=${FACTER_LOG_PATH}/instance
export FACTER_SSL_CERT_STUB=$(echo ${FACTER_DOMAIN} | tr '.' '-')
export FACTER_DC_INDEX_TYPE="cluster-${FACTER_VENDOR_ID}"

## Repositories from which to pull
export FACTER_CONSOLE_REPO="https://github.com/dreamfactorysoftware/dfe-console.git"
export FACTER_DASHBOARD_REPO="https://github.com/dreamfactorysoftware/dfe-dashboard.git"
export FACTER_INSTANCE_REPO="https://github.com/dreamfactorysoftware/dreamfactory.git"

## Mail
export FACTER_SMTP_HOST=localhost
export FACTER_SMTP_PORT=25
export FACTER_MAIL_FROM_ADDRESS="no.reply@${FACTER_DOMAIN}"
export FACTER_MAIL_FROM_NAME=${FACTER_DOMAIN}
export FACTER_MAIL_USERNAME=""
export FACTER_MAIL_PASSWORD=""

## Manifest destiny
[ "true" = "${DFE_UPDATE}" ] && _info "Updating now.." || _info "Installing now..."

for manifest in $(ls ${MANIFEST_PATH}/*.pp)
do
	_info "Applying ${manifest}..."
	puppet apply -l "${LOG_FILE}" "${manifest}"

    if [ $? -ne 0 ]; then
        _error "An unexpected result code of $? was returned. Halting."
        _error "See logged output in file /tmp/dfe-installer.log"
	    exit 1
    fi
done

_info "Complete! Output spooled to /tmp/dfe-installer.log"
