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


VERSION=1.0.0
SYSTEM_TYPE=`uname -s`
LOG_FILE=/tmp/dfe-deb-installer.log
DFE_UPDATE=
APT_BIN=`which apt-get`
_ME=`basename $0`
BASE_PATH=`pwd`
DFE_INSTALLER_REPO="https://github.com/dreamfactorysoftware/dfe-installer.git"
DFE_INSTALLER_DIR="dfe-installer"

## Server settings
LOCAL_IP=0.0.0.0
LOCAL_PORT=8000
PUBLIC_IP=127.0.0.1
PUBLIC_PORT=8000

## Get ec2 ip from the metadata
[ -f /etc/ec2_version ] && PUBLIC_IP=`curl http://169.254.169.254/latest/meta-data/public-ipv4`

## Make an url
PUBLIC_URL="http://${PUBLIC_IP}:${PUBLIC_PORT}"

################################################################################
# Colors
################################################################################

# Reset & Escape
C_ESC='\E['
C_CLR='\E[0m'
RESET=`tput sgr0`
alias _treset="tput sgr0"

# Foreground Codes
CF_BLK="30"
CF_RED="31"
CF_GRN="32"
CF_YLW="33"
CF_BLU="34"
CF_MAG="35"
CF_CYN="36"
CF_WHT="37"

# Background Codes
CB_BLK="40"
CB_RED="41"
CB_GRN="42"
CB_YLW="43"
CB_BLU="44"
CB_MAG="45"
CB_CYN="46"
CB_WHT="47"

_BLACK='30m'
_RED='31m'
_GREEN='32m'
_YELLOW='33m'
_BLUE='34m'
_MAGENTA='35m'
_CYAN='36m'
_WHITE='37m'

# Initialize terminal database
tput init

# Bold on/off
B1=`tput bold`
B2=`tput sgr0`

# Color Echo
# $1 = string to echo
# $2 = color
# $3 = bold (1=on,0=off)
# $4 = lf (1=newline)
function cecho()
{
	local _defaultMessage=""

	_message=${1:-${_defaultMessage}}
	_color=${2:-${_WHITE}}
	_bold=${3:-0}
	_lf=${4:-0}
	_b1=
	_b2=

    if [ ${_bold} -eq 1 ] ; then
    	_b1=${B1}
    	_b2=${B2}
    fi

    [ ${_lf} -ne 0 ] && _echo="-e" || _echo="-ne"

    echo ${_echo} "${_b1}\033[${_color}${_message}\033[0m${_b2}"

	return
}

# Generic message print
_msg() {
	_pre=$(echo -e "${1}:\t")
	cecho "${_pre}" "${2}"
	echo "$3"
}

_info() {
	_msg "${_ME}" "${_GREEN}" "$1"
}

_notice() {
	_msg "${_ME} notice" "${_YELLOW}" "$1"
}

_error() {
	_msg "${_ME} error" "${_RED}" "$1"
}

# Debug echo
# $1 = string to echo
function _dbg()
{
    if [ ${_debug} -eq 1 ] ; then
	    cecho "DEBUG: " ${_MAGENTA} 1 0
	    cecho "${1}" ${_YELLOW} 1 1
    fi
}

# Output a header thing
# $1 = text
function sectionHeader()
{
	cecho "********************************************************************************" ${_GREEN} 0 1
	cecho "*" ${_GREEN} 0 0 ; cecho "${1}" ${_WHITE} 1 1
	cecho "********************************************************************************" ${_GREEN} 0 1
	echo ""
}

[ "x" = "${TERM}x" ] && B1= && B2=

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
if [ ${UID} -ne 0 ]; then
   _error "This script must be run as root"
   exit 1
fi

##  Can I?
if [ "${APT_BIN}" = "" ]; then
    _error "The \"apt-get\" utility was not found in your path. Please check your system."
    exit 2
fi

## Yes I can!
sectionHeader " ${B1}DreamFactory Enterprise(tm)${B2} Debian Installer v${VERSION}"

_info "Updating system packages..."
apt-get -qq update >>${LOG_FILE} 2>&1

_info "Upgrading system packages..."
apt-get -y --quiet upgrade >>${LOG_FILE} 2>&1

_info "Installing Git, PHP, and Puppet..."
apt-get -y --quiet install git puppet php5 >>${LOG_FILE} 2>&1

if [ -d "./${DFE_INSTALLER_DIR}" ]; then
    _notice "Removing existing \"./${DFE_INSTALLER_DIR}\" directory..."
    rm -rf "./${DFE_INSTALLER_DIR}"
fi

_info "Retrieving full installation utility..."
git clone ${DFE_INSTALLER_REPO} "./${DFE_INSTALLER_DIR}" >>${LOG_FILE} 2>&1

if [ "$?" != "0" ]; then
    _error "Error pulling installation utility from repository. Check log in \"${LOG_FILE}\" for more info."
    exit 3
fi

echo # blank line
echo # blank line

_info "Starting configuration web utility. Please go to ${PUBLIC_URL} in your browser."
_info "When you are finished, press ${B1}<Control-C>${B2} to continue..."
cd ${DFE_INSTALLER_DIR}
php -S ${LOCAL_IP}:${LOCAL_PORT} -t public/ >>${LOG_FILE} 2>&1

_info "Beginning installation..."
./install.sh ${DFE_UPDATE} >>${LOG_FILE} 2>&1

if [ "$?" != "0" ]; then
    _error "Install script did not complete successfully. Check log in \"${LOG_FILE}\" for more info."
    exit 4
fi

cd
_info "Installation complete. Full output of the installation is available in \"${LOG_FILE}\""
