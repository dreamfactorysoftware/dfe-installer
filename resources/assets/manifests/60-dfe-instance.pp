################################################################################
# DreamFactory Enterprise(tm) Installer Manifest
# (c) 2012-∞ by DreamFactory Software, Inc. All Rights Reserved.
#
# Installs the DreamFactory v2.x instance code base
################################################################################

$_env = { 'path' => "$instance_root/.env", }
$_settings = {
  '' => {
    'DF_INSTANCE_NAME'     => 'dfe-instance',
    'DF_STANDALONE'        => 'false',
    'APP_LOG'              => 'single',
  }
}

class iniSettings {
  ## Create .env file
  create_ini_settings($_settings, $_env)
}

class createDirectoryStructure {
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
  exec { 'instance-storage-directories':
    command     => "find $pwd/storage -type d -exec chmod 2775 {} \\;",
    provider    => shell,
    cwd         => $instance_root,
    environment => ["HOME=/home/$user"]
  }->
  exec { 'instance-storage-files':
    command     => "find $pwd/storage -type f -exec chmod 0664 {} \\;",
    provider    => shell,
    cwd         => $instance_root,
    environment => ["HOME=/home/$user"]
  }->
  exec { 'instance-log-files':
    command     => "chown -R ${www_user}:${group} ${instance_root}/storage/logs /tmp/.df-log",
    provider    => shell,
    cwd         => "/tmp",
  }->
  exec { 'chmod-instance-log-files':
    command     => "chmod 0664 ${instance_root}/storage/logs/* /tmp/.df-log/*",
    provider    => shell,
    cwd         => $instance_root,
  }
}

class fixLogPermissions( $root, $owner, $group, $mode = 2775) {

  file { [
    "$root/storage/logs/laravel.log",
  ]:
    ensure => present,
    owner  => $www_user,
    group  => $group,
    mode   => $mode,
  }

}

##------------------------------------------------------------------------------
## Check out the repo, update composer, change file permissions...
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
exec { 'generate-app-key':
  command     => "$artisan key:generate",
  user        => $user,
  provider    => shell,
  cwd         => $instance_root,
  environment => ["HOME=/home/$user"]
}->
exec { 'clear-cache-and-optimize':
  command     => "$artisan clear-compiled ; $artisan cache:clear ; $artisan config:clear ; $artisan route:clear ; $artisan optimize",
  user        => $user,
  provider    => shell,
  cwd         => $instance_root,
  environment => ["HOME=/home/$user"]
}->
file { "$instance_root/storage/logs/laravel.log":
  ensure => present,
  owner  => $www_user,
  group  => $group,
  mode   => 0664
}->
class { 'correctFilePermissions':
  ## Ensure all files are writable by the web server
}->
  ## Fix up the permissions on the log file
class { fixLogPermissions:
  root  => $instance_root,
  owner => $www_user,
  group => $group,
}
