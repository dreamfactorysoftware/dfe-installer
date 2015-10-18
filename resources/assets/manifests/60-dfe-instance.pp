################################################################################
# DreamFactory Enterprise(tm) Installer Manifest
# (c) 2012-∞ by DreamFactory Software, Inc. All Rights Reserved.
#
# Installs the DreamFactory v2.x instance code base
################################################################################

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
    mode   => $mode,
  }->
  file { [
    "/tmp/.df-log",
    "$instance_release/$instance_branch/bootstrap/cache",
    "$instance_release/$instance_branch/storage",
    "$instance_release/$instance_branch/storage/app",
    "$instance_release/$instance_branch/storage/databases",
    "$instance_release/$instance_branch/storage/logs",
    "$instance_release/$instance_branch/storage/framework",
    "$instance_release/$instance_branch/storage/framework/cache",
    "$instance_release/$instance_branch/storage/framework/db",
    "$instance_release/$instance_branch/storage/framework/sessions",
    "$instance_release/$instance_branch/storage/framework/views",
    "$instance_release/$instance_branch/storage/scripting",
  ]:
    ensure => directory,
    owner  => $www_user,
    group  => $group,
    mode   => $mode,
  }
}

class iniSettings( $root ) {

  $_env = { 'path' => "$root/.env", }
  $_settings = {
    '' => {
      'DF_INSTANCE_NAME'     => 'dfe-instance',
      'DF_STANDALONE'        => 'false',
      'APP_LOG'              => 'single',
    }
  }

  ## Create .env file
  create_ini_settings($_settings, $_env)
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
  revision => $instance_version
}->
file { $instance_root:
  ensure => link,
  target => "$instance_release/$instance_branch",
}->
file { "$instance_root/.env":
  ensure => present,
  owner  => $user,
  group  => $www_group,
  mode   => 0750,
  source => "$instance_root/.env-dist"
}->
  ## Applies INI settings in $_settings to .env
class { iniSettings:
  root => $instance_root,
}->
  ## Make sure the directories are created with the right perms
class { laravelDirectories:
  root => $instance_root,
}->
exec { 'instance-composer-update':
  command     => "$composer_bin update",
  user        => $user,
  provider    => shell,
  cwd         => $instance_root,
  environment => [ "HOME=/home/$user", ]
}->
exec { 'generate-instance-app-key':
  command     => "$artisan key:generate",
  user        => $user,
  provider    => shell,
  cwd         => $instance_root,
  environment => ["HOME=/home/$user"]
}

class clearCaches {

  Exec {
    user        => $user,
    provider    => shell,
    cwd         => $_instance_root,
    environment => ["HOME=/home/$user"],
  }

  exec { "clc-clear-compiled":
    command     => "$artisan clear-compiled",
  }->
  exec { "clc-cache-clear":
    command     => "$artisan cache:clear",
  }->
  exec { "clc-config-clear":
    command     => "$artisan config:clear",
  }->
  exec { "clc-route-clear":
    command     => "$artisan route:clear",
  }->
  exec { "clc-optimize":
    command     => "$artisan optimize",
  }

}

## Clear caches
class { clearCaches:
  root => $instance_root,
}

class resetFilePermissions {

  Exec {
    provider    => shell,
    cwd         => $instance_root,
    environment => ["HOME=/home/$user"]
  }

  exec { 'chmod-instance-storage':
    command => "find $instance_root/storage -type d -exec chmod 2775 {} \\;",
  }->
  exec { 'chmod-instance-storage-files':
    command => "find $instance_root/storage -type f -exec chmod 0664 {} \\;",
  }

  exec { 'chmod-instance-temp':
    command => "find /tmp/.df-log -type d -exec chmod 2775 {} \\;",
  }->
  exec { 'chmod-instance-temp-files':
    command => "find /tmp/.df-log -type f -exec chmod 0664 {} \\;",
  }

  exec { "check-cached-services":
    command         => "chmod 0664 $instance_root/bootstrap/cache/services.json",
    user            => root,
    onlyif          => "test -f $instance_root/bootstrap/cache/services.json",
    path            => ['/usr/bin','/usr/sbin','/bin','/sbin'],
  }

  exec { "check-storage-log-file":
    command         => "chmod 0664 $instance_root/storage/logs/laravel.log",
    user            => root,
    onlyif          => "test -f $instance_root/storage/logs/laravel.log",
    path            => ['/usr/bin','/usr/sbin','/bin','/sbin'],
  }

}

## Fix up file permissions
class { resetFilePermissions:
  root=> $instance_root,
}
