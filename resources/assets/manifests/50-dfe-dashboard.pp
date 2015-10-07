## Installs dreamfactorysoftware/dfe-dashboard

$_env = { 'path' => "$doc_root_base_path/dashboard/.env", }
$_appUrl = "http://dashboard.${vendor_id}.${domain}"
$_settings = {
  '' =>
  {
    'APP_DEBUG' => $app_debug,
    'APP_URL' => $_appUrl,
    'DB_HOST' => $db_host,
    'DB_DATABASE' => $db_name,
    'DB_USERNAME' => $db_user,
    'DB_PASSWORD' => $db_pwd,
    'DFE_CLUSTER_ID' => "cluster-${vendor_id}",
    'DFE_DEFAULT_CLUSTER' =>  "cluster-${vendor_id}",
    'DFE_DEFAULT_DATABASE' => "db-${vendor_id}",
    'DFE_SCRIPT_USER' => $user,
    'DFE_DEFAULT_DNS_ZONE' => $vendor_id,
    'DFE_DEFAULT_DOMAIN' => "${vendor_id}.${domain}",
    'SMTP_DRIVER' => 'smtp',
    'SMTP_HOST'=> $smtp_host,
    'SMTP_PORT' => $smtp_port,
    'MAIL_FROM_ADDRESS' => $mail_from_address,
    'MAIL_FROM_NAME' => $mail_from_name,
    'MAIL_USERNAME' => $mail_username,
    'MAIL_PASSWORD' => $mail_password,
    'DFE_HOSTED_BASE_PATH' => $storage_path,
    'DFE_CONSOLE_API_URL' => "$_appUrl/api/v1/ops",
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
file { "$doc_root_base_path/dashboard/.env":
  ensure => present,
  source => "$doc_root_base_path/dashboard/.env-dist",
}->
class { 'iniSettings':

}->
exec { 'add_dashboard_keys':
  command  => "cat $doc_root_base_path/console/database/dfe/dashboard.env >> $doc_root_base_path/dashboard/.env",
  provider => 'shell',
  user     => $user
}->
exec { 'dashboard-composer-update':
  command     => "$composer_bin update",
  user        => $user,
  provider    => 'shell',
  cwd         => "$doc_root_base_path/dashboard",
  environment => [ "HOME=/home/$user", ]
}->
exec { 'generate-app-key':
  command     => "$artisan key:generate",
  user        => $user,
  provider    => 'shell',
  cwd         => "$doc_root_base_path/dashboard",
  environment => ["HOME=/home/$user"]
}->
exec { 'clear-cache-and-optimize':
  command     => "$artisan clear-compiled; $artisan cache:clear; $artisan config:clear; $artisan optimize",
  user        => $user,
  provider    => 'shell',
  cwd         => "$doc_root_base_path/dashboard",
  environment => ["HOME=/home/$user"]
}->
file { [
  "$release_path/dashboard/$dashboard_branch/bootstrap",
  "$release_path/dashboard/$dashboard_branch/bootstrap/cache",
  "$release_path/dashboard/$dashboard_branch/storage",
  "$release_path/dashboard/$dashboard_branch/storage/framework",
  "$release_path/dashboard/$dashboard_branch/storage/framework/sessions",
  "$release_path/dashboard/$dashboard_branch/storage/framework/views",
  "$release_path/dashboard/$dashboard_branch/storage/logs",
]:
  ensure => present,
  owner  => $www_user,
  group  => $group,
  mode   => 2775
}->
file { "$release_path/dashboard/$dashboard_branch/storage/logs/laravel.log":
  ensure => present,
  owner  => $www_user,
  group  => $storage_group,
  mode   => 0664
}->
## Only make symlink if installed successfully
file { "$doc_root_base_path/dashboard":
  ensure => link,
  target => "$release_path/dashboard/$dashboard_branch",
}
