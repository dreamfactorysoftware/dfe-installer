################################################################################
# DreamFactory Enterprise(tm) Installer Manifest
# (c) 2012-∞ by DreamFactory Software, Inc. All Rights Reserved.
#
# Installs and configures nginx
################################################################################

## SSL Certificate Names

$cert_file = "star-${vendor_id}-${ssl_cert_stub}.pem"
$key_file = "star-${vendor_id}-${ssl_cert_stub}.key"

$cert_name = "$pwd/SSL/$cert_file"
$key_name = "$pwd/SSL/$key_file"

## Hostnames

$instance_hostname = "*.${vendor_id}.${domain}"
$console_hostname = "console.${vendor_id}.${domain} console.local"
$dashboard_hostname = "dashboard.${vendor_id}.${domain} dashboard.local"

include stdlib

## We"re using nginx/php5-fpm and not apache

service { "nginx":
  ensure  => running,
  enable  => true
}->
service { "php5-fpm":
  ensure  => running,
  enable  => true
}->
service { "apache2":
  ensure => stopped,
  enable => false
}

## Make the configs

file { "$nginx_path/dsp-locations.conf":
  ensure => link,
  target => "$server_config_path/nginx/etc/nginx/dsp-locations.conf",
}->
file { "$nginx_path/conf.d/dreamfactory.http.conf":
  ensure => link,
  target => "$server_config_path/nginx/etc/nginx/conf.d/dreamfactory.http.conf"
}->
file { "$nginx_path/conf.d/ssl":
  ensure => directory,
}->
file { "$nginx_path/conf.d/ssl/dreamfactory.ssl.conf":
  ensure => present,
  source => "$server_config_path/nginx/etc/nginx/conf.d/ssl/dreamfactory.ssl.conf-dist"
}->
file_line { "add_ssl_cert_key":
  path     => "$nginx_path/conf.d/ssl/dreamfactory.ssl.conf",
  line     => "ssl_certificate_key                $nginx_path/conf.d/ssl/$key_file;",
  multiple => false,
  match    => ".*\/path\/to\/your\/signed\/certificate\/file.*"
}->
file_line { "add_ssl_cert":
  path     => "$nginx_path/conf.d/ssl/dreamfactory.ssl.conf",
  line     => "ssl_certificate                $nginx_path/conf.d/ssl/$cert_file;",
  multiple => false,
  match    => ".*\/path\/to\/your\/signed\/certificate\/key.*"
}->
file { "$nginx_path/conf.d/ssl/$cert_file":
  ensure => present,
  mode   => 0440,
  source => $cert_name
}->
file { "$nginx_path/conf.d/ssl/$key_file":
  ensure => present,
  mode   => 0440,
  source => $key_name
}->
file { "$nginx_path/conf.d/dreamfactory.php-fpm.conf":
  ensure => link,
  target => "$server_config_path/nginx/etc/nginx/conf.d/dreamfactory.php-fpm.conf"
}->
file { "$nginx_path/nginx.conf":
  ensure => link,
  target => "$server_config_path/nginx/etc/nginx/nginx.conf",
  notify => Service["nginx"]
}->
file { "$nginx_path/sites-enabled/default":
  ensure => absent
}->
file { "/etc/php5/mods-available/dreamfactory.ini":
  ensure  => link,
  target  => "$server_config_path/php/etc/php5/mods-available/dreamfactory.ini"
}->
file_line { "$server_config_path/php/etc/php5/mods-available/dreamfactory.ini":
  path   => "$server_config_path/php/etc/php5/mods-available/dreamfactory.ini",
  line   => "display_errors = 0",
  match  => ".*display_errors.*",
  notify => Service["php5-fpm"]
}->
exec { "enable-dreamfactory-module":
  command  => "$php_enmod_bin dreamfactory",
  provider => posix,
  notify   => Service["php5-fpm"]
}

##------------------------------------------------------------------------------
## Create the nginx site config files and link them to sites-available
##------------------------------------------------------------------------------

## Console

file { "$nginx_path/sites-available/console.conf":
  ensure  => present,
  content => "server {
    listen 80;
#    listen 443 ssl;

    server_name $console_hostname;

    root $console_root/public;

    error_log $log_path/console/error.log;
    access_log $log_path/console/access.log combined;

#    include conf.d/ssl/dreamfactory.ssl.conf;

    include dsp-locations.conf;
}"
}->
file { "$nginx_path/sites-enabled/console.conf":
  ensure => link,
  target => "$nginx_path/sites-available/console.conf"
}

## Dashboard

file { "$nginx_path/sites-available/dashboard.conf":
  ensure  => present,
  content => "server {
    listen 80;
#    listen 443 ssl;

    server_name $dashboard_hostname;

    root $dashboard_root/public;

    error_log $log_path/dashboard/error.log;
    access_log $log_path/dashboard/access.log combined;

#    include conf.d/ssl/dreamfactory.ssl.conf;

    include dsp-locations.conf;
}"
}->
file { "$nginx_path/sites-enabled/dashboard.conf":
  ensure => link,
  target => "$nginx_path/sites-available/dashboard.conf"
}

## Instances

file { "$nginx_path/sites-available/instance.conf":
  ensure  => present,
  content => "server {
    listen 80 default_server;
#   listen 443 ssl;

    server_name $instance_hostname;

    root $instance_root/public;

    error_log $log_path/hosted/all.error.log;
    access_log $log_path/hosted/\$http_host.access.log combined;

#    include conf.d/ssl/dreamfactory.ssl.conf;

    include dsp-locations.conf;
}"
}->
file { "$nginx_path/sites-enabled/instance.conf":
  ensure => link,
  target => "$nginx_path/sites-available/instance.conf"
}
