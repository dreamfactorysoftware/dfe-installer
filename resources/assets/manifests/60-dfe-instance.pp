################################################################################
# DreamFactory Enterprise(tm) Installer Manifest
# (c) 2012-∞ by DreamFactory Software, Inc. All Rights Reserved.
#
# Installs the DreamFactory v2.x instance code base
################################################################################

$_env = { 'path' => "$instance_path/.env", }
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

vcsrepo { "$release_path/dreamfactory/$instance_branch":
  ensure   => present,
  provider => git,
  source   => $instance_repo,
  user     => $user,
  owner    => $user,
  group    => $www_group,
  revision => $instance_version
}->
file { $instance_path:
  ensure => link,
  target => "$release_path/dreamfactory/$instance_branch",
}->
file { "$instance_path/.env":
  ensure => present,
  owner  => $user,
  group  => $www_group,
  mode   => 0775,
  source => "$instance_path/.env-dist"
}->
class { 'iniSettings':
  ## Applies INI settings in $_settings to .env
}->
exec { 'launchpad-composer-update':
  command     => "$composer_bin update",
  user        => $user,
  provider    => 'shell',
  cwd         => $instance_path,
  environment => ["HOME=/home/$user"]
}

## Make sure the directories are created with the right perms

file { [
  "$release_path/dreamfactory/$instance_branch/bootstrap/cache",
  "$release_path/dreamfactory/$instance_branch/storage",
  "$release_path/dreamfactory/$instance_branch/storage/logs",
  "$release_path/dreamfactory/$instance_branch/storage/framework",
  "$release_path/dreamfactory/$instance_branch/storage/framework/db",
  "$release_path/dreamfactory/$instance_branch/storage/framework/sessions",
  "$release_path/dreamfactory/$instance_branch/storage/framework/views"]:
  ensure => directory,
  owner  => $user,
  group  => $www_group,
  mode   => 2775,
}
