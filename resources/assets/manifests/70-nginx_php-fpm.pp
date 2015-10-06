/********************************************************************
*
* Change these variables as needed
*
********************************************************************/

# SSL Certificate Names

#$cert_name = "$pwd/SSL/star-${vendor_id}-${ssl_cert_stub}.pem"
#$key_name = "$pwd/SSL/star-${vendor_id}-${ssl_cert_stub}.key"

# Hostnames
$dsp_hostname = "*.${vendor_id}.${domain}"
$console_hostname = "console.${vendor_id}.${domain} console.local"
$dashboard_hostname = "dashboard.${vendor_id}.${domain} dashboard.local"
$add_to_etc_hosts = ['console.local', 'dashboard.local', "console.${vendor_id}.${domain}", "dashboard.${vendor_id}.${domain}"]

/********************************************************************
*
* Do Not Change Anything Below Here
*
********************************************************************/

include stdlib

# We're using nginx

service { 'nginx':
  ensure  => running,
  enable  => true
}

file { '/etc/nginx/dsp-locations.conf':
  ensure => link,
  target => '/var/www/launchpad/server/config/nginx/etc/nginx/dsp-locations.conf',
}->
file { '/etc/nginx/conf.d/dreamfactory.http.conf':
  ensure => link,
  target => '/var/www/launchpad/server/config/nginx/etc/nginx/conf.d/dreamfactory.http.conf'
}->
file { '/etc/nginx/conf.d/ssl':
  ensure => directory,
}->
file { '/etc/nginx/conf.d/ssl/dreamfactory.ssl.conf':
  ensure => present,
  source => '/var/www/launchpad/server/config/nginx/etc/nginx/conf.d/ssl/dreamfactory.ssl.conf-dist'
}->
#file_line { 'add_ssl_cert_key':
#  path     => '/etc/nginx/conf.d/ssl/dreamfactory.ssl.conf',
#  line     => "ssl_certificate_key                /etc/nginx/conf.d/ssl/dfe.key;",
#  multiple => false,
#  match    => '.*\/path\/to\/your\/signed\/certificate\/file.*'
#}->
#file_line { 'add_ssl_cert':
#  path     => '/etc/nginx/conf.d/ssl/dreamfactory.ssl.conf',
#  line     => "ssl_certificate                /etc/nginx/conf.d/ssl/dfe.pem;",
#  multiple => false,
#  match    => '.*\/path\/to\/your\/signed\/certificate\/key.*'
#}->
#file { '/etc/nginx/conf.d/ssl/dfe.pem':
#  ensure => present,
#  mode   => '0440',
#  source => $cert_name
#}->
#file { '/etc/nginx/conf.d/ssl/dfe.key':
#  ensure => present,
#  mode   => '0440',
#  source => $key_name
#}->
file { '/etc/nginx/conf.d/dreamfactory.php-fpm.conf':
  ensure => link,
  target => '/var/www/launchpad/server/config/nginx/etc/nginx/conf.d/dreamfactory.php-fpm.conf'
}->
file { '/etc/nginx/sites-available/instance.conf':
  ensure  => present,
  content =>'server {
    listen 80 default_server;
#    listen 443 ssl;

    #Server Name

    root /var/www/launchpad/public;

    error_log /data/logs/hosted/all.error.log;
    access_log /data/logs/hosted/$http_host.access.log combined;

#    include conf.d/ssl/dreamfactory.ssl.conf;

    include dsp-locations.conf;
}'
}->
file_line { 'add_dsp_servername':
  path  => '/etc/nginx/sites-available/instance.conf',
  line  => "server_name $dsp_hostname;",
  after => '#Server Name'
}->
file { '/etc/nginx/sites-enabled/00-instance.conf':
  ensure => link,
  target => '/etc/nginx/sites-available/instance.conf'
}->
file { '/etc/nginx/sites-available/console.conf':
  ensure  => present,
  content => 'server {
    listen 80;
#    listen 443 ssl;

    #Server Name

    root /var/www/console/public;

    error_log /data/logs/console/error.log;
    access_log /data/logs/console/access.log combined;

#    include conf.d/ssl/dreamfactory.ssl.conf;

    include dsp-locations.conf;
}'
}->
file_line { 'add_console_servername':
  path  => '/etc/nginx/sites-available/console.conf',
  line  => "server_name $console_hostname;",
  after => '#Server Name'
}->
file { '/etc/nginx/sites-enabled/console.conf':
  ensure => link,
  target => '/etc/nginx/sites-available/console.conf'
}->
file { '/etc/nginx/sites-available/dashboard.conf':
  ensure  => present,
  content => 'server {
    listen 80;
#    listen 443 ssl;

    #Server Name

    root /var/www/dashboard/public;

    error_log /data/logs/dashboard/error.log;
    access_log /data/logs/dashboard/access.log combined;

#    include conf.d/ssl/dreamfactory.ssl.conf;

    include dsp-locations.conf;
}'
}->
file_line { 'add_dashboard_servername':
  path  => '/etc/nginx/sites-available/dashboard.conf',
  line  => "server_name $dashboard_hostname;",
  after => "#Server Name",
}->
file { '/etc/nginx/sites-enabled/dashboard.conf':
  ensure => link,
  target => '/etc/nginx/sites-available/dashboard.conf'
}->
file { '/etc/nginx/sites-enabled/default':
  ensure => absent
}->
file { '/etc/nginx/nginx.conf':
  ensure => link,
  target => '/var/www/launchpad/server/config/nginx/etc/nginx/nginx.conf',
  notify => Service['nginx']
}->
host { 'localhost':
  ensure       => present,
  ip           => '127.0.0.1',
  host_aliases => $add_to_etc_hosts
}->
file { '/etc/php5/mods-available/dreamfactory.ini':
  ensure  => link,
  target  => '/var/www/launchpad/server/config/php/etc/php5/mods-available/dreamfactory.ini'
}->
exec { 'enable_dreamfactory_settings':
  command  => '/usr/sbin/php5enmod dreamfactory',
  provider => posix,
  notify   => Service['php5-fpm']
}->
file_line { '/var/www/launchpad/server/config/php/etc/php5/mods-available/dreamfactory.ini':
  path   => '/var/www/launchpad/server/config/php/etc/php5/mods-available/dreamfactory.ini',
  line   => 'display_errors = Off',
  match  => '.*display_errors.*',
  notify => Service['php5-fpm']
}->
service { 'apache2':
  ensure => stopped,
  enable => false
}

service { 'php5-fpm':
  ensure  => running,
  enable  => true
}
