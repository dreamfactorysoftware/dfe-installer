################################################################################
# DreamFactory Enterprise(tm) Installer Manifest
# (c) 2012-2112 by DreamFactory Software, Inc. All Rights Reserved.
#
# Install dreamfactory/dfe-dashboard
################################################################################

notify { 'announce-thyself': message => '[DFE] Install/update dashboard software', }
Exec { path => ['/usr/bin','/usr/sbin','/bin','/sbin'], }

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
    owner  => $owner,
    group  => $group,
    mode   => $mode,
  }

  ## Blow away cache on update
  if ( true == str2bool($dfe_update) ) {
    exec { "remove-services-json":
      command         => "rm -f $root/bootstrap/cache/services.json",
      user            => root,
      onlyif          => "test -f $root/bootstrap/cache/services.json",
    }->
    exec { "remove-compiled-classes":
      command         => "rm -f $root/bootstrap/cache/compiled.php",
      user            => root,
      onlyif          => "test -f $root/bootstrap/cache/compiled.php",
    }
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

## Setup the app / composer update
class setupApp( $root ) {
  if ( false == str2bool($dfe_update) ) {
    exec { "generate-app-key":
      command     => "$artisan key:generate",
      user        => $user,
      provider    => shell,
      cwd         => $root,
      environment => ["HOME=/home/$user"]
    }
  }
}

## Checks directory/file permissions
class checkPermissions( $root, $dir_mode = '2775', $file_mode = '0664' ) {
  exec { 'chown-and-pwn':
    user            => root,
    command         => "chown -R ${www_user}:${group} ${root}/storage/ ${root}/bootstrap/cache/",
    cwd             => $root,
    environment     => ["HOME=/home/${user}"]
  }->
  exec { 'chmod-storage':
    user            => root,
    command         => "find ${root}/storage -type d -exec chmod ${dir_mode} {} \\;",
    onlyif          => "test -d ${root}/storage",
    cwd             => $root,
    environment     => ["HOME=/home/${user}"]
  }->
  exec { 'chmod-storage-files':
    user            => root,
    command         => "find ${root}/storage -type f -exec chmod ${file_mode} {} \\;",
    onlyif          => "test -d ${root}/storage",
    cwd             => $root,
    environment     => ["HOME=/home/${user}"]
  }->
  exec { "check-bootstrap-cache":
    user            => root,
    command         => "chmod ${file_mode} ${root}/bootstrap/cache/* && chown ${www_user}:${group} ${root}/bootstrap/cache/*",
    onlyif          => "test -f ${root}/bootstrap/cache/compiled.php",
    cwd             => $root,
  }->
  exec { "check-storage-log-file":
    user            => root,
    command         => "chmod ${file_mode} ${root}/storage/logs/*.log && chown ${www_user}:${group} ${root}/storage/logs/*.log",
    onlyif          => "test -f $root/storage/logs/laravel.log",
    cwd             => $root,
  }
}

##  Create an environment file
class createEnvFile( $root, $source = ".env-dist" ) {
  ##  On new installs only
  if ( false == str2bool($dfe_update) ) {
    file { "${root}/.env":
      ensure => present,
      owner  => $user,
      group  => $www_group,
      mode   => 0640,
      source => "${root}/${source}",
    }->
    exec { "append-api-keys":
      command         => "cat $console_root/database/dfe/dashboard.env >> $root/.env",
      user            => $user,
      onlyif          => "test -f $console_root/database/dfe/dashboard.env",
    }
  }
}

##------------------------------------------------------------------------------
## Check out the repo, update composer, change file permissions...
##------------------------------------------------------------------------------

vcsrepo { "$dashboard_release/$dashboard_branch":
  ensure   => latest,
  provider => git,
  source   => $dashboard_repo,
  user     => $user,
  owner    => $user,
  group    => $www_group,
  revision => $dashboard_branch,
}->
file { $dashboard_root:
  ensure => link,
  target => "$dashboard_release/$dashboard_branch",
}->
class { createEnvFile:
  root => $dashboard_root,
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
exec { "composer-install":
  command     => "$composer_bin install",
  user        => $user,
  provider    => shell,
  cwd         => $dashboard_root,
  timeout     => 1800,
  environment => [ "HOME=/home/$user", ]
}->
class { setupApp:
  root => $dashboard_root,
}->
exec { "composer-update":
  command     => "$composer_bin update",
  user        => $user,
  provider    => shell,
  cwd         => $dashboard_root,
  timeout     => 1800,
  environment => [ "HOME=/home/$user", ]
}->
class { checkPermissions:
  root => $dashboard_root,
}
