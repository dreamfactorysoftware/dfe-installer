#!/bin/bash
#
# @(#)$Id: deb-install.sh,v 1.0.0 2015-10-30 jablan $
#
# This file is part of DreamFactory Enterprise(tm)
#
# DreamFactory Enterprise(tm) Remote Installer
# Copyright (c) DreamFactory Software, Inc. All rights reserved.
#
# Debian flavored easy install

##	Initial settings
. ./ansi.sh

VERSION=1.0.0
SYSTEM_TYPE=`uname -s`
LOG_FILE=/tmp/dfe-deb-installer.log
DFE_UPDATE=
APT_BIN=`which apt-get`

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
        -u|--update) DFE_UPDATE=" -u " ;;
        *) ;; ## Default/ignored
    esac

    shift
done

## Who am I?
if [ $UID -ne 0 ]; then
   _error "This script must be run as root"
   exit 1
fi

##  Can I?
if [ "${APT_BIN}" = "" ]; then
    _error "The \"apt-get\" utility was not found in your path. Please check your system."
    exit 2
fi

## Yes I can!
_info "Updating system packages..."
apt-get -qq update >>${LOG_FILE} 2>&1

_info "Upgrading system packages..."
apt-get -y --quiet upgrade >>${LOG_FILE} 2>&1

_info "Installing Git, PHP, and Puppet"
apt-get -y --quiet install git puppet php >>${LOG_FILE} 2>&1

_info "Starting installation..."
_rv=`./install.sh ${DFE_UPDATE} >>${LOG_FILE} 2>&1`

if [ ${_rv} -ne 0 ]; then
    _error "Install script did not complete successfully. Check log in \"${LOG_FILE}\" for more info."
    exit 3
fi

_info "Installation complete"
