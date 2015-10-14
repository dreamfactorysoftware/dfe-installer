################################################################################
# DreamFactory Enterprise(tm) Installer Manifest
# (c) 2012-∞ by DreamFactory Software, Inc. All Rights Reserved.
#
# Installs and configures nginx
################################################################################

include stdlib

## SSL cert file and names
$cert_file = "star-${vendor_id}-${ssl_cert_stub}.pem"
$key_file = "star-${vendor_id}-${ssl_cert_stub}.key"
$cert_name = "$pwd/SSL/$cert_file"
$key_name = "$pwd/SSL/$key_file"

## Figure out the hostnames
$instance_hostname = "*.${vendor_id}.${domain}"
$console_hostname = "console.${vendor_id}.${domain}"
$dashboard_hostname = "dashboard.${vendor_id}.${domain}"

## Set up SSL template parts
$ssl_include = $enable_ssl ? {
  true => "include conf.d/ssl/dfe-instance.conf;",
  default => "",
}

$ssl_listen = $enable_ssl ? {
  true => "listen 443 ssl;",
  default => "",
}

## Set up some content templates
$content_header = "##**************************************************************************
##	This file was distributed with DreamFactory Enterprise(tm) Installer
##	Copyright 2015-2115 DreamFactory Software, Inc. All Rights Reserved.
##
##	Licensed under the Apache License, Version 2.0 (the \"License\");
##	you may not use this file except in compliance with the License.
##	You may obtain a copy of the License at
##
##	http://www.apache.org/licenses/LICENSE-2.0
##
##	Unless required by applicable law or agreed to in writing, software
##	distributed under the License is distributed on an \"AS IS\" BASIS,
##	WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
##	See the License for the specific language governing permissions and
##	limitations under the License.
##**************************************************************************"

## Template for instance
$instance_content = "${content_header}

##**************************************************************************
## Configures two separate servers for HTTP and HTTPS
##**************************************************************************

# [instance].local:80
server {
	listen 80 default_server;

	server_name $instance_hostname;

	# Doc root
	root $instance_root/public;

	# Change locations/names as you please
	error_log $log_path/hosted/all.error.log;
	access_log $log_path/hosted/\$http_host.access.log combined;

	# Our DSP locations
	include dfe-locations.conf;
}"

## SSL template for instance
if ( $enable_ssl ) {
  $instance_content = "${instance_content}

# [instance].local:443
server {
	listen 443 ssl;

	server_name $instance_hostname;

	# Doc root
	root $instance_root/public;

	# Change locations/names as you please
	error_log $log_path/hosted/all.error.log;
	access_log $log_path/hosted/\$http_host.access.log combined;

	# SSL config
	# This way you can keep it locked down a little better
	# or not. Just uncomment the directives and remove the include.
	include conf.d/ssl/dfe-instance.conf;

	# Our DSP locations
	include dfe-locations.conf;
}"
}

## Console content
$console_content = "${content_header}

##**************************************************************************
## Configures a single server for HTTP and HTTPS
##**************************************************************************

server {
  listen 80;
  $ssl_listen

  server_name $console_hostname console.local;

  root $console_root/public;

  error_log $log_path/console/error.log;
  access_log $log_path/console/access.log combined;

  $ssl_include

  include dfe-locations.conf;
}"

## Dashboard content
$dashboard_content = "${content_header}

##**************************************************************************
## Configures a single server for HTTP and HTTPS
##**************************************************************************

server {
  listen 80;
  $ssl_listen

  server_name $dashboard_hostname dashboard.local;

  root $dashboard_root/public;

  error_log $log_path/dashboard/error.log;
  access_log $log_path/dashboard/access.log combined;

  $ssl_include

  include dfe-locations.conf;
}"

## A class to create all of the nginx config files and links
class createServerConfigs {

  file { "$nginx_path/dfe-locations.conf":
    ensure => present,
    source => "$pwd/resources/assets/etc/nginx/dfe-locations.conf",
  }->
  file { "$nginx_path/conf.d/dfe.conf":
    ensure => present,
    source => "$pwd/resources/assets/etc/nginx/conf.d/dfe.conf"
  }->
  file { "$nginx_path/conf.d/ssl":
    ensure => directory,
  }->
  file { "$nginx_path/conf.d/ssl/dfe-instance.conf":
    ensure => present,
    source => "$pwd/resources/assets/etc/nginx/conf.d/ssl/dfe-instance.conf"
  }->
  file { "$nginx_path/nginx.conf":
    ensure => link,
    target => "$pwd/resources/assets/etc/nginx/nginx.conf",
  }->
  file { [
    "$nginx_path/sites-enabled/default",
    "$nginx_path/sites-available/default",
  ]:
    ensure => absent
  }->
  file { "/etc/php5/mods-available/dreamfactory.ini":
    ensure  => link,
    target  => "$server_config_path/php/etc/php5/mods-available/dreamfactory.ini"
  }->
    ## Enable our tweaks
  exec { "enable-dreamfactory-module":
    command  => "$php_enmod_bin dreamfactory",
    provider => posix,
  }->
    ## Instance
  file { "$nginx_path/sites-available/00-dfe-instance.conf":
    ensure  => present,
    content => $instance_content,
  }->
    ## Instance link
  file { "$nginx_path/sites-enabled/00-dfe-instance.conf":
    ensure  => link,
    target  => "$nginx_path/sites-available/00-dfe-instance.conf",
  }->
    ## Console
  file { "$nginx_path/sites-available/10-dfe-console.conf":
    ensure  => present,
    content => $console_content,
  }->
    ## Console link
  file { "$nginx_path/sites-enabled/10-dfe-console.conf":
    ensure => link,
    target => "$nginx_path/sites-available/10-dfe-console.conf"
  }->
    ## Dashboard
  file { "$nginx_path/sites-available/20-dfe-dashboard.conf":
    ensure  => present,
    content => $dashboard_content,
  }->
    ## Dashboard link
  file { "$nginx_path/sites-enabled/20-dfe-dashboard.conf":
    ensure   => link,
    target   => "$nginx_path/sites-available/20-dfe-dashboard.conf",
    ## Tell php5-fpm to restart now
    notify   => Service["php5-fpm"]
  }

  if ( !$app_debug) {
    file_line { 'update-dreamfactory-ini':
      path   => "/etc/php5/mods-available/dreamfactory.ini",
      line   => "display_errors = 0",
      match  => ".*display_errors.*",
      notify => Service["php5-fpm"]
    }
  }
}

##------------------------------------------------------------------------------
## We're using nginx/php5-fpm and not apache
##------------------------------------------------------------------------------

service { "nginx":
  ensure  => running,
  enable  => true,
}->
service { "php5-fpm":
  ensure  => running,
  enable  => true
}

service { "apache2":
  ensure => stopped,
  enable => false
}

class { createServerConfigs:
  ## Make sure our configs are written before we restart nginx
  notify => Service['nginx'],
}
