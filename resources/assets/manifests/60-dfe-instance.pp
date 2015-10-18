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
  root  => $instance_root,
  owner => $www_user,
  group => $group,
}->
exec { "remove-services-json":
  command         => "rm -f $instance_root/bootstrap/cache/services.json",
  user            => root,
  onlyif          => "test -f $instance_root/bootstrap/cache/services.json",
  path            => ['/usr/bin','/usr/sbin','/bin','/sbin'],
}->
exec { "remove-compiled-classes":
  command         => "rm -f $instance_root/bootstrap/cache/compiled.php",
  user            => root,
  onlyif          => "test -f $instance_root/bootstrap/cache/compiled.php",
  path            => ['/usr/bin','/usr/sbin','/bin','/sbin'],
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
}->
exec { "clc-clear-compiled":
  command     => "$artisan clear-compiled",
  user        => $user,
  provider    => shell,
  cwd         => $instance_root,
  environment => ["HOME=/home/$user"],
}->
exec { "clc-cache-clear":
  command     => "$artisan cache:clear",
  user        => $user,
  provider    => shell,
  cwd         => $instance_root,
  environment => ["HOME=/home/$user"],
}->
exec { "clc-config-clear":
  command     => "$artisan config:clear",
  user        => $user,
  provider    => shell,
  cwd         => $instance_root,
  environment => ["HOME=/home/$user"],
}->
exec { "clc-route-clear":
  command     => "$artisan route:clear",
  user        => $user,
  provider    => shell,
  cwd         => $instance_root,
  environment => ["HOME=/home/$user"],
}->
exec { "clc-optimize":
  command     => "$artisan optimize --force",
  user        => $user,
  provider    => shell,
  cwd         => $instance_root,
  environment => ["HOME=/home/$user"],
}->
exec { 'chmod-instance-storage':
  command     => "find $instance_root/storage -type d -exec chmod 2775 {} \\;",
  provider    => shell,
  cwd         => $instance_root,
  environment => ["HOME=/home/$user"]
}->
exec { 'chmod-instance-storage-files':
  command     => "find $instance_root/storage -type f -exec chmod 0664 {} \\;",
  provider    => shell,
  cwd         => $instance_root,
  environment => ["HOME=/home/$user"]
}->
exec { 'chmod-instance-temp':
  command     => "find /tmp/.df-log -type d -exec chmod 2775 {} \\;",
  provider    => shell,
  cwd         => $instance_root,
  environment => ["HOME=/home/$user"]
}->
exec { 'chmod-instance-temp-files':
  command     => "find /tmp/.df-log -type f -exec chmod 0664 {} \\;",
  provider    => shell,
  cwd         => $instance_root,
  environment => ["HOME=/home/$user"]
}->
exec { "check-cached-services":
  command         => "chmod 0664 $instance_root/bootstrap/cache/services.json && chown $www_user:$group $instance_root/bootstrap/cache/services.json",
  user            => root,
  onlyif          => "test -f $instance_root/bootstrap/cache/services.json",
  path            => ['/usr/bin','/usr/sbin','/bin','/sbin'],
}->
exec { "check-compiled-classes":
  command         => "chmod 0664 $instance_root/bootstrap/cache/compiled.php && chown $www_user:$group $instance_root/bootstrap/cache/compiled.php",
  user            => root,
  onlyif          => "test -f $instance_root/bootstrap/cache/compiled.php",
  path            => ['/usr/bin','/usr/sbin','/bin','/sbin'],
}->
exec { "check-storage-log-file":
  command         => "chmod 0664 $instance_root/storage/logs/laravel.log && chown $www_user:$group $instance_root/storage/logs/laravel.log",
  user            => root,
  onlyif          => "test -f $instance_root/storage/logs/laravel.log",
  path            => ['/usr/bin','/usr/sbin','/bin','/sbin'],
}
