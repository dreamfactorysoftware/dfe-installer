################################################################################
# DreamFactory Enterprise(tm) Installer Manifest
# (c) 2012-∞ by DreamFactory Software, Inc. All Rights Reserved.
#
# Install dreamfactory/dfe-dashboard
################################################################################

$_env = { 'path' => "$dashboard_path/.env", }
$_appUrl = "http://dashboard.${vendor_id}.${domain}"
$_settings = {
  '' =>
  {
    'APP_DEBUG'            => $app_debug,
    'APP_URL'              => $_appUrl,
    'DB_HOST'              => $db_host,
    'DB_DATABASE'          => $db_name,
    'DB_USERNAME'          => $db_user,
    'DB_PASSWORD'          => $db_pwd,
    'DFE_CLUSTER_ID'       => "cluster-${vendor_id}",
    'DFE_DEFAULT_CLUSTER'  => "cluster-${vendor_id}",
    'DFE_DEFAULT_DATABASE' => "db-${vendor_id}",
    'DFE_SCRIPT_USER'      => $user,
    'DFE_DEFAULT_DNS_ZONE' => $vendor_id,
    'DFE_DEFAULT_DOMAIN'   => "${vendor_id}.${domain}",
    'MAIL_DRIVER'          => 'smtp',
    'MAIL_HOST'            => $smtp_host,
    'MAIL_PORT'            => $smtp_port,
    'MAIL_FROM_ADDRESS'    => $mail_from_address,
    'MAIL_FROM_NAME'       => $mail_from_name,
    'MAIL_USERNAME'        => $mail_username,
    'MAIL_PASSWORD'        => $mail_password,
    'DFE_HOSTED_BASE_PATH' => $storage_path,
    'DFE_CONSOLE_API_URL'  => "$_appUrl/api/v1/ops",
  }
}

class iniSettings {
  ## Create .env file
  create_ini_settings($_settings, $_env)
}

vcsrepo { "$release_path/dashboard/$dashboard_branch":
  ensure   => present,
  provider => git,
  source   => $dashboard_repo,
  user     => $user,
  owner    => $user,
  group    => $www_group,
  revision => $dashboard_version,
}->
file { $dashboard_path:
  ensure => link,
  target => "$release_path/dashboard/$dashboard_branch",
}->
file { "$dashboard_path/.env":
  ensure => present,
  source => "$dashboard_path/.env-dist",
}->
class { 'iniSettings':
  ## Applies INI settings in $_settings to .env
}->
exec { 'add_dashboard_keys':
  command  => "cat $console_path/database/dfe/dashboard.env >> $dashboard_path/.env",
  provider => 'shell',
  user     => $user
}->
exec { 'dashboard-composer-update':
  command     => "$composer_bin update",
  user        => $user,
  provider    => 'shell',
  cwd         => $dashboard_path,
  environment => [ "HOME=/home/$user", ]
}->
exec { 'generate-app-key':
  command     => "$artisan key:generate",
  user        => $user,
  provider    => 'shell',
  cwd         => $dashboard_path,
  environment => ["HOME=/home/$user"]
}->
exec { 'clear-cache-and-optimize':
  command     => "$artisan clear-compiled ; $artisan cache:clear ; $artisan config:clear ; $artisan route:clear ; $artisan optimize",
  user        => $user,
  provider    => 'shell',
  cwd         => $dashboard_path,
  environment => ["HOME=/home/$user"]
}

file { [
  "$dashboard_path/bootstrap",
  "$dashboard_path/bootstrap/cache",
  "$dashboard_path/storage",
  "$dashboard_path/storage/framework",
  "$dashboard_path/storage/framework/sessions",
  "$dashboard_path/storage/framework/views",
  "$dashboard_path/storage/logs",
]:
  ensure => directory,
  owner  => $www_user,
  group  => $group,
  mode   => 2775
}->
file { "$dashboard_path/storage/logs/laravel.log":
  ensure => present,
  owner  => $www_user,
  group  => $group,
  mode   => 0664
}
