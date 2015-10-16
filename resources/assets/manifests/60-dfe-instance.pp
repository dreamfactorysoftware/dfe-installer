################################################################################
# DreamFactory Enterprise(tm) Installer Manifest
# (c) 2012-∞ by DreamFactory Software, Inc. All Rights Reserved.
#
# Installs the DreamFactory v2.x instance code base
################################################################################

##------------------------------------------------------------------------------
## Classes
##------------------------------------------------------------------------------

class iniSettings {

  $_env = { 'path' => "$instance_root/.env", }
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

class createDirectoryStructure {

  file { [
    "$instance_release/$instance_branch/bootstrap",
  ]:
    ensure => directory,
    owner  => $user,
    group  => $www_group,
    mode   => 2775,
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
    mode   => 2775,
  }

}

class correctFilePermissions {

  exec { 'chmod-instance-storage':
    command     => "find $pwd/storage -type d -exec chmod 2775 {} \\;",
    provider    => shell,
    cwd         => $instance_root,
    environment => ["HOME=/home/$user"]
  }->
  exec { 'chmod-instance-storage-files':
    command     => "find $pwd/storage -type f -exec chmod 0664 {} \\;",
    provider    => shell,
    cwd         => $instance_root,
    environment => ["HOME=/home/$user"]
  }

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
  }

}

class fixLogPermissions( $root, $owner, $group, $mode = 0664) {

  file { "$root/bootstrap/cache/services.json":
    ensure => present,
    owner  => $www_user,
    group  => $group,
    mode   => $mode,
    onlyif => "test ! -f $root/bootstrap/cache/services.json"
  }

  file { "$root/storage/logs/laravel.log":
    ensure => present,
    owner  => $www_user,
    group  => $group,
    mode   => $mode,
    onlyif => "test ! -f $root/storage/logs/laravel.log"
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
class { 'iniSettings':
  ## Applies INI settings in $_settings to .env
}->
class { 'createDirectoryStructure':
  ## Make sure the directories are created with the right perms
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
exec { 'clear-instance-cache-and-optimize':
  command     => "$artisan clear-compiled ; $artisan cache:clear ; $artisan config:clear ; $artisan route:clear ; $artisan optimize",
  user        => $user,
  provider    => shell,
  cwd         => $instance_root,
  environment => ["HOME=/home/$user"]
}->
class { correctFilePermissions:
  ## Ensure all files are writable by the web server
}->
  ## Fix up the permissions on the log file
class { fixLogPermissions:
  root  => $instance_root,
  owner => $www_user,
  group => $group,
}
