#!/bin/bash
#
# @(#)$Id: ansi.sh,v 1.03 2011-12-12 jablan $
#
# Some helper junk
#

# Don't re-include
if [ ${_df_scriptHelper:=0} -eq 1 ] ; then
    return
fi

_df_scriptHelper_=1
_ME=`basename $0`
BASE_PATH=`pwd`
_debug=${SDV_DEBUG:=0} # set to 1 to dump paths when running

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
