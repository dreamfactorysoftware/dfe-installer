#!/bin/bash
#
# @(#)$Id: install.sh,v 1.1.58 2015-11-16 dweiner/jablan $
#
# This file is part of DreamFactory Enterprise(tm)
#
# DreamFactory Enterprise(tm) Installer
# Copyright (c) 2012-2016 DreamFactory Software, Inc. All rights reserved.
#

##	Initial settings
. ./ansi.sh

VERSION=1.1.58
SYSTEM_TYPE=`uname -s`
ENV_FILE=./storage/.env-install
PHP_BIN=`which php`
PHP_ENMOD_BIN=`which php5enmod`
LOG_FILE=/tmp/dfe-installer.log
DFE_UPDATE=false
RUBY_BIN=`which ruby`

[ "x" = "${LANG}x" ] && LANG="en_US.UTF8"

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

#get info about OS - 'RedHat' or 'Debian'
OS_FAMILY=$(facter osfamily)
if [[ $OS_FAMILY == "Debian" ]]; then
    MANIFEST_PATH=./resources/assets/manifests/ubuntu
    _info "System is Debian / Ubuntu based"

elif [[ $OS_FAMILY == "RedHat" ]]; then
    MANIFEST_PATH=./resources/assets/manifests/rhel
    _info "System is RHEL/Centos based"
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

    for module in puppetlabs-stdlib puppetlabs-vcsrepo puppetlabs-mysql puppetlabs-apt example42-yum puppetlabs-inifile wcooley-user_ssh_pubkey dhoppe-postfix
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

## Defaults, versions, branches and executable locations
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
export FACTER_PERCONA_VERSION=57
export FACTER_NGINX_PATH=/etc/nginx
export FACTER_RUN_USER=${USER}
export FACTER_LOG_USER=ubuntu
export FACTER_STATIC_ZONE_NAME=local
export FACTER_INSTALL_HOSTNAME=`/bin/hostname`
export FACTER_VENDOR_ID=dfe
export FACTER_DC_ES_PORT=9200
export FACTER_SUPPORT_EMAIL_ADDRESS=support@dreamfactory.com
## 20160125-GHA: Now sourced from .env-install
#export FACTER_INSTALL_VERSION_KIBANA='4.3.0'
#export FACTER_INSTALL_VERSION_LOGSTASH='2.0'
#export FACTER_INSTALL_VERSION_ELASTICSEARCH='2.x'
## 20160125-GHA: Now sourced from .env-install
#export FACTER_CONSOLE_BRANCH=develop
#export FACTER_DASHBOARD_BRANCH=develop
#export FACTER_INSTANCE_BRANCH=develop

## SSL (change accordingly)
export FACTER_ENABLE_SSL=false
export FACTER_DEFAULT_PROTOCOL=http

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

## User/auth defaults
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
export FACTER_INSTANCE_CACHE_PATH=/tmp/.df-cache

## General information that is sourced
export FACTER_ADMIN_EMAIL FACTER_ADMIN_PWD FACTER_GH_TOKEN
export FACTER_MOUNT_POINT FACTER_DOMAIN FACTER_LOG_PATH
export FACTER_DC_HOST FACTER_DC_PORT FACTER_DC_INDEX_TYPE FACTER_DC_ES_CLUSTER FACTER_DC_ES_EXISTS

## Customisation, version, and branch info that is sourced
export FACTER_CUSTOM_CSS_FILE_SOURCE FACTER_CUSTOM_CSS_FILE_PATH FACTER_CUSTOM_CSS_FILE
export FACTER_LOGIN_SPLASH_IMAGE_SOURCE FACTER_LOGIN_SPLASH_IMAGE_PATH FACTER_LOGIN_SPLASH_IMAGE
export FACTER_NAVBAR_IMAGE_SOURCE FACTER_NAVBAR_IMAGE_PATH FACTER_NAVBAR_IMAGE
export FACTER_INSTALL_VERSION_KIBANA FACTER_INSTALL_VERSION_LOGSTASH FACTER_INSTALL_VERSION_ELASTICSEARCH
export FACTER_CONSOLE_BRANCH FACTER_DASHBOARD_BRANCH FACTER_INSTANCE_BRANCH

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
export FACTER_BLUEPRINT_PATH=${FACTER_MOUNT_POINT}/blueprints
export FACTER_BLUEPRINT_LOG_PATH=${FACTER_LOG_PATH}/blueprints
export FACTER_CAPSULE_PATH=${FACTER_MOUNT_POINT}/capsules
export FACTER_CAPSULE_LOG_PATH=${FACTER_LOG_PATH}/capsules

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
[ "true" = "${DFE_UPDATE}" ] && _info "Updating now...." || _info "Installing now..."

for manifest in $(ls ${MANIFEST_PATH}/*.pp)
do
	_info "Applying ${manifest}..."

    puppet apply -l "${LOG_FILE}" "${manifest}"
    _applyResult=$?

    ${RUBY_BIN} ./resources/assets/bin/check_puppet.rb --ignore-enabled --skipped 2 -c 2 -w 2 -f >>${LOG_FILE} 2>&1
    _checkResult=$?

    ## Check the number of failed resources and exit code
    if [ ${_checkResult} -ne 0 -o ${_applyResult} -ne 0 ]; then
        _error ""
        _error "Manifest ${B1}${manifest}${B2} application returned [{$_applyResult}]. Examination of summary result [{$_checkResult}]"
        _error ""
        _error "An unexpected error occurred during the processing of ${B1}${manifest}${B2}. Installation must be halted."
        _error "Please see the logged output of this script in file ${B1}/tmp/dfe-installer.log${B2}"
        _error ""
        _error "Additional information about the specific issue may be found in ${B1}/var/lib/puppet/state/last_run_[report|summary].yaml${B2}"
        _error ""

	    exit 1
    fi
done

_info "Complete! Output spooled to /tmp/dfe-installer.log"
