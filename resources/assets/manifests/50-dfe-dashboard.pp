################################################################################
# DreamFactory Enterprise(tm) Installer Manifest
# (c) 2012-∞ by DreamFactory Software, Inc. All Rights Reserved.
#
# Install dreamfactory/dfe-dashboard
################################################################################

include dfe::laravelDirectories
include dfe::dashboardEnvironmentSettings

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
class { dfe::dashboardEnvironmentSettings:
## Applies INI settings in $_settings to .env
  root     => $dashboard_root,
  zone     => $vendor_id,
  domain   => $domain,
  protocol => $default_protocol,
}->
class { dfe::laravelDirectories:
  root  => $dashboard_root,
  owner => $www_user,
  group => $group,
}->
exec { 'add_dashboard_keys':
  command  => "cat $console_root/database/dfe/dashboard.env >> $dashboard_root/.env",
  provider => shell,
  user     => $user
}->
exec { 'dashboard-composer-update':
  command     => "$composer_bin update",
  user        => $user,
  provider    => shell,
  cwd         => $dashboard_root,
  environment => [ "HOME=/home/$user", ]
}->
exec { 'generate-app-key':
  command     => "$artisan key:generate",
  user        => $user,
  provider    => shell,
  cwd         => $dashboard_root,
  environment => ["HOME=/home/$user"]
}->
exec { 'clear-cache-and-optimize':
  command     => "$artisan clear-compiled ; $artisan cache:clear ; $artisan config:clear ; $artisan route:clear ; $artisan optimize",
  user        => $user,
  provider    => shell,
  cwd         => $dashboard_root,
  environment => ["HOME=/home/$user"]
}