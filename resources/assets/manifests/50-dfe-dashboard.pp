################################################################################
# DreamFactory Enterprise(tm) Installer Manifest
# (c) 2012-∞ by DreamFactory Software, Inc. All Rights Reserved.
#
# Install dreamfactory/dfe-dashboard
################################################################################

############
## Classes
############

## A class that creates the directories required for a Laravel 5+ application.
## Permissions are set accordingly.
class laravelDirectories( $root, $owner, $group, $mode = 2775 ) {

  file { [
    "$root/bootstrap",
  ]:
    ensure => directory,
    owner  => $user,
    group  => $www_group,
    mode   => $mode,
  }->
  file { [
    "$root/bootstrap/cache",
    "$root/storage",
    "$root/storage/framework",
    "$root/storage/framework/sessions",
    "$root/storage/framework/views",
    "$root/storage/logs",
  ]:
    ensure => directory,
    owner  => $www_user,
    group  => $group,
    mode   => $mode,
  }
}

## Defines the dashboard .env settings. Relies on FACTER_* data
class iniSettings( $root, $zone, $domain, $protocol = "https") {
  ## Define our stuff
  $_env = { "path" => "$root/.env", }
  $_consoleUrl = "$protocol://console.${zone}.${domain}"
  $_dashboardUrl = "$protocol://dashboard.${zone}.${domain}"
  $_consoleApiUrl = "$_consoleUrl/api/v1/ops"

  $_settings = {
    "" => {
      "APP_DEBUG"                                  => $app_debug,
      "APP_URL"                                    => $_dashboardUrl,
      "DB_HOST"                                    => $db_host,
      "DB_DATABASE"                                => $db_name,
      "DB_USERNAME"                                => $db_user,
      "DB_PASSWORD"                                => $db_pwd,
      "DFE_CLUSTER_ID"                             => "cluster-${zone}",
      "DFE_DEFAULT_CLUSTER"                        => "cluster-${zone}",
      "DFE_DEFAULT_DATABASE"                       => "db-${zone}",
      "DFE_SCRIPT_USER"                            => $user,
      "DFE_DEFAULT_DNS_ZONE"                       => $zone,
      "DFE_DEFAULT_DNS_DOMAIN"                     => $domain,
      "DFE_DEFAULT_DOMAIN"                         => "${zone}.${domain}",
      "DFE_DEFAULT_DOMAIN_PROTOCOL"                => $default_protocol,
      "DFE_STATIC_ZONE_NAME"                       => $static_zone_name,
      "SMTP_DRIVER"                                => "smtp",
      "SMTP_HOST"                                  => $smtp_host,
      "SMTP_PORT"                                  => $smtp_port,
      "MAIL_FROM_ADDRESS"                          => $mail_from_address,
      "MAIL_FROM_NAME"                             => $mail_from_name,
      "MAIL_USERNAME"                              => $mail_username,
      "MAIL_PASSWORD"                              => $mail_password,
      "DFE_HOSTED_BASE_PATH"                       => $storage_path,
      "DFE_CONSOLE_API_URL"                        => $_consoleApiUrl,
    }
  }

  ## Update the .env file
  create_ini_settings($_settings, $_env)
}

##------------------------------------------------------------------------------
## Check out the repo, update composer, change file permissions...
##------------------------------------------------------------------------------

vcsrepo { "$dashboard_release/$dashboard_branch":
  ensure   => present,
  provider => git,
  source   => $dashboard_repo,
  user     => $user,
  owner    => $user,
  group    => $www_group,
  revision => $dashboard_version,
}->
file { $dashboard_root:
  ensure => link,
  target => "$dashboard_release/$dashboard_branch",
}->
file { "$dashboard_root/.env":
  ensure => present,
  owner  => $user,
  group  => $www_group,
  mode   => 0750,
  source => "$dashboard_root/.env-dist",
}->
class { iniSettings:
  ## Applies INI settings in $_settings to .env
  root     => $dashboard_root,
  zone     => $vendor_id,
  domain   => $domain,
  protocol => $default_protocol,
}->
class { laravelDirectories:
  root  => $dashboard_root,
  owner => $www_user,
  group => $group,
}->
exec { "append-dashboard-api-keys":
  command  => "cat $console_root/database/dfe/dashboard.env >> $dashboard_root/.env",
  provider => shell,
  user     => $user
}->
exec { "remove-services-json":
  command         => "rm -f $dashboard_root/bootstrap/cache/services.json",
  user            => root,
  onlyif          => "test -f $dashboard_root/bootstrap/cache/services.json",
  path            => ['/usr/bin','/usr/sbin','/bin','/sbin'],
}->
exec { "dashboard-composer-update":
  command     => "$composer_bin update",
  user        => $user,
  provider    => shell,
  cwd         => $dashboard_root,
  environment => [ "HOME=/home/$user", ]
}->
exec { "generate-dashboard-app-key":
  command     => "$artisan key:generate",
  user        => $user,
  provider    => shell,
  cwd         => $dashboard_root,
  environment => ["HOME=/home/$user"]
}->
exec { "clc-clear-compiled":
  command     => "$artisan clear-compiled",
  user        => $user,
  provider    => shell,
  cwd         => $dashboard_root,
  environment => ["HOME=/home/$user"],
}->
exec { "clc-cache-clear":
  command     => "$artisan cache:clear",
  user        => $user,
  provider    => shell,
  cwd         => $dashboard_root,
  environment => ["HOME=/home/$user"],
}->
exec { "clc-config-clear":
  command     => "$artisan config:clear",
  user        => $user,
  provider    => shell,
  cwd         => $dashboard_root,
  environment => ["HOME=/home/$user"],
}->
exec { "clc-route-clear":
  command     => "$artisan route:clear",
  user        => $user,
  provider    => shell,
  cwd         => $dashboard_root,
  environment => ["HOME=/home/$user"],
}->
exec { "clc-optimize":
  command     => "$artisan optimize",
  user        => $user,
  provider    => shell,
  cwd         => $dashboard_root,
  environment => ["HOME=/home/$user"],
}->
exec { 'chmod-instance-storage':
  command     => "find $dashboard_root/storage -type d -exec chmod 2775 {} \\;",
  provider    => shell,
  cwd         => $dashboard_root,
  environment => ["HOME=/home/$user"]
}->
exec { 'chmod-instance-storage-files':
  command     => "find $dashboard_root/storage -type f -exec chmod 0664 {} \\;",
  provider    => shell,
  cwd         => $dashboard_root,
  environment => ["HOME=/home/$user"]
}->
exec { 'chmod-instance-temp':
  command     => "find /tmp/.df-log -type d -exec chmod 2775 {} \\;",
  provider    => shell,
  cwd         => $dashboard_root,
  environment => ["HOME=/home/$user"]
}->
exec { 'chmod-instance-temp-files':
  command     => "find /tmp/.df-log -type f -exec chmod 0664 {} \\;",
  provider    => shell,
  cwd         => $dashboard_root,
  environment => ["HOME=/home/$user"]
}->
exec { "check-cached-services":
  command         => "chmod 0664 $dashboard_root/bootstrap/cache/services.json",
  user            => root,
  onlyif          => "test -f $dashboard_root/bootstrap/cache/services.json",
  path            => ['/usr/bin','/usr/sbin','/bin','/sbin'],
}->
exec { "check-storage-log-file":
  command         => "chmod 0664 $dashboard_root/storage/logs/laravel.log",
  user            => root,
  onlyif          => "test -f $dashboard_root/storage/logs/laravel.log",
  path            => ['/usr/bin','/usr/sbin','/bin','/sbin'],
}
