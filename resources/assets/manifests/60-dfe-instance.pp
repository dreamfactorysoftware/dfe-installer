################################################################################
# DreamFactory Enterprise(tm) Installer Manifest
# (c) 2012-∞ by DreamFactory Software, Inc. All Rights Reserved.
#
# Installs the DreamFactory v2.x instance code base
################################################################################

$_env = { 'path' => "$instance_root/.env", }
$_settings = {
  '' => {
    'MAIL_DRIVER'          => 'smtp',
    'MAIL_HOST'            => $smtp_host,
    'MAIL_PORT'            => $smtp_port,
    'MAIL_USERNAME'        => $mail_username,
    'MAIL_PASSWORD'        => $mail_password,
  }
}

class iniSettings {
  ## Create .env file
  create_ini_settings($_settings, $_env)
}

## Check out the repo

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
  mode   => 0775,
  source => "$instance_root/.env-dist"
}->
class { 'iniSettings':
  ## Applies INI settings in $_settings to .env
}->
exec { 'launchpad-composer-update':
  command     => "$composer_bin update",
  user        => $user,
  provider    => 'shell',
  cwd         => $instance_root,
  environment => ["HOME=/home/$user"]
}

## Make sure the directories are created with the right perms

file { [
  "$instance_release/$instance_branch/bootstrap/cache",
  "$instance_release/$instance_branch/storage",
  "$instance_release/$instance_branch/storage/logs",
  "$instance_release/$instance_branch/storage/framework",
  "$instance_release/$instance_branch/storage/framework/db",
  "$instance_release/$instance_branch/storage/framework/sessions",
  "$instance_release/$instance_branch/storage/framework/views"]:
  ensure => directory,
  owner  => $user,
  group  => $www_group,
  mode   => 2775,
}
