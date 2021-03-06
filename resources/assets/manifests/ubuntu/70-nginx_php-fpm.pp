################################################################################
# DreamFactory Enterprise(tm) Installer Manifest
# (c) 2012-2112 by DreamFactory Software, Inc. All Rights Reserved.
#
# Installs and configures nginx
################################################################################

notify { 'announce-thyself': message => '[DFE] Configuring PHP runtime and web server', }
Exec { path => ['/usr/bin','/usr/sbin','/bin','/sbin'], }

##------------------------------------------------------------------------------
## Variables
##------------------------------------------------------------------------------

## SSL cert file and names
$cert_file = "star-${vendor_id}-${ssl_cert_stub}.pem"
$key_file = "star-${vendor_id}-${ssl_cert_stub}.key"
$cert_name = "$pwd/SSL/$cert_file"
$key_name = "$pwd/SSL/$key_file"

## Figure out the hostnames
$instance_hostname = "*.${vendor_id}.${domain}"
$console_hostname = "console.${vendor_id}.${domain}"
$dashboard_hostname = "dashboard.${vendor_id}.${domain}"
$download_hostname = "download.${vendor_id}.${domain}"

## Set up SSL template parts
$ssl_enabled = str2bool($enable_ssl)

$ssl_include = $ssl_enabled ? {
  true => "include conf.d/ssl/dfe-instance.conf;",
  default => "",
}

$ssl_listen = $ssl_enabled ? {
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

## Parameters template
$fastcgi_include = "
    #   DFE Parameters
    fastcgi_param DFE_MANAGED \"1\";
    fastcgi_param DFE_CLUSTER_ID \"cluster-${vendor_id}\";
    fastcgi_param DFE_WEB_SERVER_ID \"web-${vendor_id}\";
    fastcgi_param DFE_APP_SERVER_ID \"app-${vendor_id}\";
    fastcgi_param DFE_DB_SERVER_ID \"db-${vendor_id}\";
    fastcgi_param DFE_DNS_ZONE \"${vendor_id}\";
    fastcgi_param DFE_DNS_DOMAIN \"${domain}\";
    fastcgi_param DFE_DOMAIN \"${vendor_id}.${domain}\";
    fastcgi_param DFE_DOMAIN_PROTOCOL \"$default_protocol\";
"

## Template for instance
$instance_content = "${content_header}

##**************************************************************************
## Configures a single server for HTTP and HTTPS (if enabled)
##**************************************************************************

# [instance].local:80[,443]
server {
	listen 80 default_server;
  ${ssl_listen}

	server_name ${instance_hostname};

	# Doc root
	root ${instance_root}/public;

	# Change locations/names as you please
	error_log ${log_path}/hosted/all.error.log;
	access_log ${log_path}/hosted/\$http_host.access.log combined;

  ${fastcgi_include}
  ${ssl_include}

	# Our DSP locations
	include dfe-locations.conf;
}"


## Console content
$console_content = "${content_header}

##**************************************************************************
## Configures a single server for HTTP and HTTPS
##**************************************************************************

server {
  listen 80;
  ${ssl_listen}

  server_name ${console_hostname} console.local;

  root ${console_root}/public;

  error_log ${log_path}/console/error.log;
  access_log ${log_path}/console/access.log combined;

  ${fastcgi_include}
  ${ssl_include}

  include dfe-locations.conf;
}"

## Dashboard content
$dashboard_content = "${content_header}

##**************************************************************************
## Configures a single server for HTTP and HTTPS
##**************************************************************************

server {
  listen 80;
  ${ssl_listen}

  server_name ${dashboard_hostname} ${download_hostname} dashboard.local;

  root ${dashboard_root}/public;

  error_log ${log_path}/dashboard/error.log;
  access_log ${log_path}/dashboard/access.log combined;

  ${fastcgi_include}
  ${ssl_include}

  include dfe-locations.conf;
}"

##------------------------------------------------------------------------------
## Services
##------------------------------------------------------------------------------

package { "nginx-extras":
  ensure => latest,
}->
service { "nginx":
  ensure  => running,
  enable  => true,
  require => Package["nginx-extras"],
}->
package { "php7.0-fpm":
  ensure => latest,
}->
service { "php7.0-fpm":
  ensure  => running,
  enable  => true,
  require => Package["php7.0-fpm"],
}->
service { "apache2":
  ensure => stopped,
  enable => false
}->
package { "apache2":
  ensure => absent
}

##------------------------------------------------------------------------------
## Logic
##------------------------------------------------------------------------------

if ( false == str2bool($dfe_update) ) {
  file { [
    "$nginx_path/conf.d",
    "$nginx_path/conf.d/ssl",
    "$nginx_path/mods-available",
    "$nginx_path/mods-enabled",
  ]:
    ensure=> directory,
  }->
  file { [
    "$nginx_path/sites-enabled/default",
    "$nginx_path/sites-available/default",
  ]:
    ensure => absent
  }->
  file { "$nginx_path/dfe-locations.conf":
    ensure => present,
    source => "$pwd/resources/assets/etc/nginx/dfe-locations.conf",
  }->
  file { "$nginx_path/conf.d/dfe.conf":
    ensure => present,
    source => "$pwd/resources/assets/etc/nginx/conf.d/dfe.conf"
  }->
  file { "$nginx_path/conf.d/ssl/dfe-instance.conf":
    ensure => present,
    source => "$pwd/resources/assets/etc/nginx/conf.d/ssl/dfe-instance.conf"
  }->
  file { "$nginx_path/nginx.conf":
    ensure => present,
    source => "$pwd/resources/assets/etc/nginx/nginx.conf",
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
  }->
  /* If the OS Family is debian/ubuntu, need to change the location of a couple things in the conf file - default is rhel/centos */
  file_line { 'server socket':
    path  => "$nginx_path/conf.d/dfe.conf",
    line  => 'server unix:/run/php/php7.0-fpm.sock;',
    match => 'server unix:\/var\/run\/php-fpm\/php-fpm.sock;',
  }->
  file_line { 'nginx user':
    path  => "$nginx_path/nginx.conf",
    line  => 'user www-data;',
    match => 'user nginx;',
  }->
  file_line { 'nginx include':
    path  => "$nginx_path/nginx.conf",
    line  => 'include /etc/nginx/sites-enabled/*;',
    match => '#include \/etc\/nginx\/sites-enabled\/\*;',
  }->
  exec { "restart-nginx":
    cwd         => $root,
    command     => "sudo service nginx restart",
  }->
  exec { "restart-php-fpm":
    cwd         => $root,
    command     => "sudo service php7.0-fpm restart"
  }->
  exec { "start-kibana-if-not-started":
    cwd         => $root,
    command     => "sudo service kibana restart",
  }
}
