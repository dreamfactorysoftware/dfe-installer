################################################################################
# DreamFactory Enterprise(tm) Installer Manifest
# (c) 2012-∞ by DreamFactory Software, Inc. All Rights Reserved.
#
# Installs the DreamFactory v2.x instance code base
################################################################################

notify { 'announce-thyself': message => '[DFE] Install/update instance software', }
Exec { path => ['/usr/bin','/usr/sbin','/bin','/sbin'], }

##------------------------------------------------------------------------------
## Classes
##------------------------------------------------------------------------------

## Creates the directories required for a Laravel 5+ application. and sets permissions accordingly.
class laravelDirectories( $root, $owner, $group, $mode = '2775') {
  file { [
    "$root/bootstrap",
  ]:
    ensure => directory,
    owner  => $user,
    group  => $www_group,
    mode   => $mode,
  }->
  file { [
    "/tmp/.df-cache",
    "/tmp/.df-log",
    "$root/bootstrap/cache",
    "$root/storage",
    "$root/storage/app",
    "$root/storage/databases",
    "$root/storage/logs",
    "$root/storage/framework",
    "$root/storage/framework/cache",
    "$root/storage/framework/db",
    "$root/storage/framework/sessions",
    "$root/storage/framework/views",
    "$root/storage/scripting",
  ]:
    ensure => directory,
    owner  => $owner,
    group  => $group,
    mode   => $mode,
  }

  ##  Blow away cache on update
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

## set the .env vars
class iniSettings( $root ) {
  $_env = { 'path' => "$root/.env", }
  $_settings = {
    '' => {
      'APP_LOG'               => 'single',
      'DB_DRIVER'             => 'mysql',
      'DF_INSTANCE_NAME'      => "instance-${vendor_id}",
      'DF_MANAGED'            => 'true',
      'DFE_AUDIT_HOST'        => $dc_host,
      'DFE_AUDIT_PORT'        => $dc_port,
    }
  }

  create_ini_settings($_settings, $_env)
}

## Setup the app / composer update
class setupApp( $root ) {
  if ( false == str2bool($dfe_update) ) {
    exec { 'generate-app-key':
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

  ##  instance logs and cache
  exec { 'chown-and-pwn-tmp-log':
    user            => root,
    command         => "chown -R ${www_user}:${group} /tmp/.df-log",
    onlyif          => "test -d /tmp/.df-log",
    cwd             => $root,
  }->
  exec { 'chmod-temp-df-log':
    command         => "find /tmp/.df-log -type d -exec chmod ${dir_mode} {} \\;",
    cwd             => $root,
    onlyif          => "test -d /tmp/.df-log",
  }->
  exec { 'chmod-temp-df-log-files':
    command         => "find /tmp/.df-log -type f -exec chmod ${file_mode} {} \\;",
    cwd             => $root,
    onlyif          => "test -d /tmp/.df-log",
  }

  exec { 'chown-and-pwn-tmp-cache':
    user            => root,
    command         => "chown -R ${www_user}:${group} /tmp/.df-cache",
    onlyif          => "test -d /tmp/.df-cache",
    cwd             => $root,
  }->
  exec { 'chmod-temp-df-cache':
    command         => "find /tmp/.df-cache -type d -exec chmod ${dir_mode} {} \\;",
    cwd             => $root,
    onlyif          => "test -d /tmp/.df-cache",
  }->
  exec { 'chmod-temp-df-cache-files':
    command         => "find /tmp/.df-cache -type f -exec chmod ${file_mode} {} \\;",
    cwd             => $root,
    onlyif          => "test -d /tmp/.df-cache",
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
    }
  }
}

##------------------------------------------------------------------------------
## Logic
##------------------------------------------------------------------------------

vcsrepo { "$instance_release/$instance_branch":
  ensure   => present,
  provider => git,
  source   => $instance_repo,
  user     => $user,
  owner    => $user,
  group    => $www_group,
  revision => $instance_branch,
}->
file { $instance_root:
  ensure => link,
  target => "$instance_release/$instance_branch",
}->
class { createEnvFile:
  root => $instance_root,
}->
class { iniSettings:
  ## Applies INI settings in $_settings to .env
  root => $instance_root,
}->
  ## Make sure the directories are created with the right perms
class { laravelDirectories:
  root  => $instance_root,
  owner => $www_user,
  group => $group,
}->
exec { 'composer-update':
  command     => "$composer_bin update",
  user        => $user,
  provider    => shell,
  cwd         => $instance_root,
  timeout     => 1800,
  environment => [ "HOME=/home/$user", ]
}->
class { setupApp:
  root => $instance_root,
}->
class { checkPermissions:
  root => $instance_root,
}
