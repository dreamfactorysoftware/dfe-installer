################################################################################
# DreamFactory Enterprise(tm) Installer Manifest
# (c) 2012-∞ by DreamFactory Software, Inc. All Rights Reserved.
#
# Installs the DreamFactory v2.x instance code base
################################################################################

notify { 'announce-thyself':
  message => '[DFE] Install/update instance software',
}

##------------------------------------------------------------------------------
## Classes
##------------------------------------------------------------------------------

## A class that creates the directories required for a Laravel 5+ application.
## Permissions are set accordingly.
class laravelDirectories( $root, $owner, $group, $mode = 2775) {

  file { [
    "$root/bootstrap",
  ]:
    ensure => directory,
    owner  => $user,
    group  => $www_group,
    mode   => 2775,
  }->
  file { [
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
    owner  => $www_user,
    group  => $group,
    mode   => 2775,
  }

  ##  Blow away cache on update
  if ( true == str2bool($dfe_update) ) {
    exec { "remove-services-json":
      command         => "rm -f $root/bootstrap/cache/services.json",
      user            => root,
      onlyif          => "test -f $root/bootstrap/cache/services.json",
      path            => ['/usr/bin','/usr/sbin','/bin','/sbin'],
    }->
    exec { "remove-compiled-classes":
      command         => "rm -f $root/bootstrap/cache/compiled.php",
      user            => root,
      onlyif          => "test -f $root/bootstrap/cache/compiled.php",
      path            => ['/usr/bin','/usr/sbin','/bin','/sbin'],
    }
  }
}

## set the .env vars
class iniSettings( $root ) {

  $_env = { 'path' => "$root/.env", }
  $_settings = {
    '' => {
      'APP_LOG'          => 'single',
      'DB_DRIVER'        => 'mysql',
      'DF_INSTANCE_NAME' => 'dfe-instance',
      'DF_MANAGED'       => 'true',
    }
  }

  if ( false == str2bool($dfe_update) ) {
    ## Create .env file
    create_ini_settings($_settings, $_env)
  }
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

## Check file permissions
class checkPermissions( $root ) {

  exec { 'chmod-instance-storage':
    command     => "find $root/storage -type d -exec chmod 2775 {} \\;",
    provider    => shell,
    cwd         => $root,
    environment => ["HOME=/home/$user"]
  }->
  exec { 'chmod-instance-storage-files':
    command     => "find $root/storage -type f -exec chmod 0664 {} \\;",
    provider    => shell,
    cwd         => $root,
    environment => ["HOME=/home/$user"]
  }->
  exec { 'chmod-instance-temp':
    command         => "find /tmp/.df-log -type d -exec chmod 2775 {} \\;",
    provider        => shell,
    cwd             => $root,
    onlyif          => "test -d /tmp/.df-log",
    environment     => ["HOME=/home/$user"]
  }->
  exec { 'chmod-instance-temp-files':
    command         => "find /tmp/.df-log -type f -exec chmod 0664 {} \\;",
    provider        => shell,
    cwd             => $root,
    onlyif          => "test -d /tmp/.df-log",
    environment     => ["HOME=/home/$user"]
  }->
  exec { "check-cached-services":
    command         => "chmod 0664 $root/bootstrap/cache/services.json && chown $www_user:$group $root/bootstrap/cache/services.json",
    user            => root,
    onlyif          => "test -f $root/bootstrap/cache/services.json",
    path            => ['/usr/bin','/usr/sbin','/bin','/sbin'],
  }->
  exec { "check-compiled-classes":
    command         => "chmod 0664 $root/bootstrap/cache/compiled.php && chown $www_user:$group $root/bootstrap/cache/compiled.php",
    user            => root,
    onlyif          => "test -f $root/bootstrap/cache/compiled.php",
    path            => ['/usr/bin','/usr/sbin','/bin','/sbin'],
  }->
  exec { "check-storage-log-file":
    command         => "chmod 0664 $root/storage/logs/laravel.log && chown $www_user:$group $root/storage/logs/laravel.log",
    user            => root,
    onlyif          => "test -f $root/storage/logs/laravel.log",
    path            => ['/usr/bin','/usr/sbin','/bin','/sbin'],
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
file { "$instance_root/.env":
  ensure => present,
  owner  => $user,
  group  => $www_group,
  mode   => 0640,
  source => "$instance_root/.env-dist"
}->
  ## Applies INI settings in $_settings to .env
class { iniSettings:
  root => $instance_root,
}->
  ## Make sure the directories are created with the right perms
class { laravelDirectories:
  root  => $instance_root,
  owner => $www_user,
  group => $group,
}->
exec { 'composer-update':
  command     => "$composer_bin update --quiet --no-interaction --no-dev --prefer-source",
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
