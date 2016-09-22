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

service { "nginx":
  ensure  => running,
  enable  => true,
}->
package { "php70w-fpm":
  ensure => latest,
}->
service { "php-fpm":
  ensure  => running,
  enable  => true,
  require => Package["php70w-fpm"],
}

service { "apache2":
  ensure => stopped,
  enable => false
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
    ensure => link,
    target => "$pwd/resources/assets/etc/nginx/nginx.conf",
  }->
  #Setup Nginx for RHEL/Centos
  ini_setting { "listen":
    ensure  => present,
    path    => "/etc/php-fpm.d/www.conf",
    key_val_separator => '=',
    section => 'www',
    setting => 'listen',
    value   => '/var/run/php-fpm/php-fpm.sock'
  }->
  ini_setting { "listen.mode":
    ensure  => present,
    path    => "/etc/php-fpm.d/www.conf",
    key_val_separator => '=',
    section => 'www',
    setting => 'listen.mode',
    value   => '0660'
  }->
  ini_setting { "listen.owner":
    ensure  => present,
    path    => "/etc/php-fpm.d/www.conf",
    key_val_separator => '=',
    section => 'www',
    setting => 'listen.owner',
    value   => 'nginx'
  }->
  ini_setting { "listen.group":
    ensure  => present,
    path    => "/etc/php-fpm.d/www.conf",
    key_val_separator => '=',
    section => 'www',
    setting => 'listen.group',
    value   => 'nginx'
  }->
  ini_setting { "user.nginx":
    ensure  => present,
    path    => "/etc/php-fpm.d/www.conf",
    key_val_separator => '=',
    section => 'www',
    setting => 'user',
    value   => 'nginx'
  }->
  ini_setting { "group.nginx":
    ensure  => present,
    path    => "/etc/php-fpm.d/www.conf",
    key_val_separator => '=',
    section => 'www',
    setting => 'group',
    value   => 'nginx'
  }->
  ini_setting { "pm.max_children2":
    ensure  => present,
    path    => "/etc/php-fpm.d/www.conf",
    key_val_separator => '=',
    section => 'www',
    setting => 'pm.max_children',
    value   => '100'
  }->
  ini_setting { "pm.start_servers":
    ensure  => present,
    path    => "/etc/php-fpm.d/www.conf",
    key_val_separator => '=',
    section => 'www',
    setting => 'pm.start_servers',
    value   => '20'
  }->
  ini_setting { "pm.min_spare_servers":
    ensure  => present,
    path    => "/etc/php-fpm.d/www.conf",
    key_val_separator => '=',
    section => 'www',
    setting => 'pm.min_spare_servers',
    value   => '10'
  }->
  ini_setting { "pm.max_spare_servers":
    ensure  => present,
    path    => "/etc/php-fpm.d/www.conf",
    key_val_separator => '=',
    section => 'www',
    setting => 'pm.max_spare_servers',
    value   => '20'
  }->
  /*file { "/etc/php/5.6/mods-available/dreamfactory.ini":
    ensure  => link,
    target  => "$server_config_path/php/etc/php/5.6/mods-available/dreamfactory.ini"
  }->*/
    # Needs to be based on $app_debug
    #  file_line { "update-dreamfactory-ini":
    #    path   => "/etc/php5/mods-available/dreamfactory.ini",
    #    line   => "display_errors = 0",
    #    match  => ".*display_errors.*",
    #  }->
    ## Enable our tweaks
    ## Instance
  file { "$nginx_path/conf.d/00-dfe-instance.conf":
    ensure  => present,
    content => $instance_content,
  }->
/*    ## Instance link
  file { "$nginx_path/conf.d/00-dfe-instance.conf":
    ensure  => link,
    target  => "$nginx_path/conf.d/00-dfe-instance.conf",
  }->*/
    ## Console
  file { "$nginx_path/conf.d/10-dfe-console.conf":
    ensure  => present,
    content => $console_content,
  }->
/*    ## Console link
  file { "$nginx_path/conf.d/10-dfe-console.conf":
    ensure => link,
    target => "$nginx_path/conf.d/10-dfe-console.conf"
  }->*/
    ## Dashboard
  file { "$nginx_path/conf.d/20-dfe-dashboard.conf":
    ensure  => present,
    content => $dashboard_content,
  }->
/*    ## Dashboard link
  file { "$nginx_path/conf.d/20-dfe-dashboard.conf":
    ensure   => link,
    target   => "$nginx_path/conf.d/20-dfe-dashboard.conf",
  }->*/
  /*exec { "enable-dreamfactory-module":
    command  => "$php_enmod_bin dreamfactory",
    notify   => Service["php5-fpm", "nginx"],
  }->*/
  exec { "restart-php-fpm":
    cwd         => $root,
    command     => "sudo systemctl restart php-fpm",
  }->
  exec { "restart-nginx":
    cwd         => $root,
    command     => "sudo systemctl restart nginx",
  }->
  exec { "start-kibana-if-not-started":
    cwd         => $root,
    command     => "sudo systemctl restart kibana",
  }
}
