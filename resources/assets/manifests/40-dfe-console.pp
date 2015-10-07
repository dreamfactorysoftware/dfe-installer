################################################################################
# DreamFactory Enterprise(tm) Installer Manifest
# (c) 2012-∞ by DreamFactory Software, Inc. All Rights Reserved.
#
# Install dreamfactory/dfe-console
################################################################################

$_env = { 'path' => "$console_path/.env", }
$_appUrl = "http://console.${vendor_id}.${domain}"
$_settings = {
  '' => {
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

vcsrepo { "$release_path/console/$console_branch":
  ensure   => present,
  provider => git,
  source   => $console_repo,
  user     => $user,
  owner    => $user,
  group    => $www_group,
  revision => $console_version
}->
file { $console_path:
  ensure => link,
  target => "$release_path/console/$console_branch",
}->
file { "$console_path/.env":
  ensure => present,
  source => "$console_path/.env-dist",
}->
class { 'iniSettings':
  ## Applies INI settings in $_settings to .env
}->
exec { 'console-composer-update':
  command     => "$composer_bin update",
  user        => $user,
  provider    => 'shell',
  cwd         => $console_path,
  environment => [ "HOME=/home/$user", ]
}->
exec { 'generate-app-key':
  command     => "$artisan key:generate",
  user        => $user,
  provider    => 'shell',
  cwd         => $console_path,
  environment => ["HOME=/home/$user"]
}->
exec { 'console-setup':
  command     => "$artisan dfe:setup --force --admin-password='${admin_pwd}' '${admin_email}'",
  user        => $user,
  provider    => 'shell',
  cwd         => $console_path,
  environment => ["HOME = /home/$user"]
}->
exec { 'add_console_keys':
  command  => "cat $console_path/database/dfe/console.env >> $console_path/.env",
  provider => 'shell',
  user     => $user
}->
file { "$doc_root_base_path/.dfe.cluster.json":
  ensure => present,
  owner  => $user,
  group  => $www_group,
  mode   => 0644,
  source => "$console_path/database/dfe/.dfe.cluster.json"
}->
exec { 'add_web_server':
  command     => "$artisan dfe:server create web-${vendor_id} -t web -a ${vendor_id}.${domain} -m ${default_local_mount_name} -c {}",
  user        => $user,
  provider    => 'shell',
  cwd         => $console_path,
}->
exec { 'add_app_server':
  command     => "$artisan dfe:server create app-${vendor_id} -t app -a ${vendor_id}.${domain} -m ${default_local_mount_name} -c {}",
  user        => $user,
  provider    => 'shell',
  cwd         => $console_path,
  environment => ["HOME =/home/$user"]
}->
exec { 'add_db_server':
  command     => "$artisan dfe:server create db-${vendor_id} -t db -a ${vendor_id}.${domain} -m ${default_local_mount_name} -c '{\"port\":\"3306\",\"username\":\"${db_user}\",\"password\":\"${db_password}\",\"driver\":\"mysql\",\"default-database-name\":\"\",\"multi-assign\":\"on\"}'",
  user        => $user,
  provider    => 'shell',
  cwd         => $console_path,
  environment => ["HOME = /home/$user"]
}->
exec { 'add_cluster':
  command     => "$artisan dfe:cluster create cluster-${vendor_id} --subdomain ${vendor_id}.${domain}",
  user        => $user,
  provider    => 'shell',
  cwd         => $console_path,
  environment => ["HOME = /home/$user"]
}->
exec { 'add_web_to_cluster':
  command     => "$artisan dfe:cluster add cluster-${vendor_id} --server-id web-${vendor_id}",
  user        => $user,
  provider    => 'shell',
  cwd         => $console_path,
  environment => ["HOME = /home/$user"]
}->
exec { 'add_app_to_cluster':
  command     => "$artisan dfe:cluster add cluster-${vendor_id} --server-id app-${vendor_id}",
  user        => $user,
  provider    => 'shell',
  cwd         => $console_path,
  environment => ["HOME = /home/$user"]
}->
exec { 'add_db_to_cluster':
  command     => "$artisan dfe:cluster add cluster-${vendor_id} --server-id db-${vendor_id}",
  user        => $user,
  provider    => 'shell',
  cwd         => $console_path,
  environment => ["HOME = /home/$user"]
}

exec { 'clear-cache-and-optimize':
  command     => "$artisan clear-compiled; $artisan cache:clear; $artisan config:clear; $artisan optimize",
  user        => $user,
  provider    => 'shell',
  cwd         => $console_path,
  environment => ["HOME=/home/$user"]
}->
file { [
  "$console_path/bootstrap",
  "$console_path/bootstrap/cache",
  "$console_path/storage",
  "$console_path/storage/framework",
  "$console_path/storage/framework/sessions",
  "$console_path/storage/framework/views",
  "$console_path/storage/logs",
]:
  ensure => directory,
  owner  => $www_user,
  group  => $group,
  mode   => 2775
}->
file { "$console_path/storage/logs/laravel.log":
  ensure => present,
  owner  => $www_user,
  group  => $group,
  mode   => 0664
}
