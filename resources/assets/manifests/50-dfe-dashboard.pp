## Installs dreamfactorysoftware/dfe-dashboard

vcsrepo { "${release_path}/dashboard/$dashboard_branch":
  ensure   => present,
  provider => git,
  source   => "https://${github_user_info}github.com/dreamfactorysoftware/dfe-dashboard.git",
  user     => $user,
  owner    => $user,
  group    => $www_group,
  revision => $dashboard_version,
}->
exec { 'dashboard-config':
  command     => "${composer_bin}/usr/local/bin/composer update",
  user        => $user,
  provider    => 'shell',
  cwd         => '/var/www/dashboard',
  environment => ["HOME=/home/$user"]
}->
file { '/var/www/dashboard/.env':
  ensure => present,
  source => '/var/www/dashboard/.env-dist'
}->
exec { 'add_dashboard_keys':
  command  => "cat /var/www/console/database/dfe/dashboard.env >> /var/www/dashboard/.env",
  provider => 'shell',
  user     => $user
}->
exec { 'generate-app-key':
  command     => "$artisan key:generate",
  user        => $user,
  provider    => 'shell',
  cwd         => "$doc_root_base_path/dashboard",
  environment => ["HOME=/home/$user"]
}

$_env = { 'path' => "/var/www/dashboard/.env", }

$_settings = {
  '' =>
  {
    'APP_DEBUG' => $app_debug,
    'APP_URL' => "http://console.${vendor_id}.${domain}",
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
    'DFE_CONSOLE_API_URL' => "http://console.${vendor_id}.${domain}/api/v1/ops",
  }
}

create_ini_settings($_settings, $_env)

file { [
  "/var/www/_releases/dashboard/$dashboard_branch/bootstrap",
  "/var/www/_releases/dashboard/$dashboard_branch/bootstrap/cache",
  "/var/www/_releases/dashboard/$dashboard_branch/storage",
  "/var/www/_releases/dashboard/$dashboard_branch/storage/framework",
  "/var/www/_releases/dashboard/$dashboard_branch/storage/framework/sessions",
  "/var/www/_releases/dashboard/$dashboard_branch/storage/framework/views",
  "/var/www/_releases/dashboard/$dashboard_branch/storage/logs",
]:
  ensure => directory,
  owner  => $www_user,
  group  => $group,
  mode   => 2775,
}->
exec { 'clear_and_regenerate_cache':
  command     => "$artisan clear-compiled; $artisan cache:clear; $artisan config:clear; $artisan optimize",
  user        => $user,
  provider    => 'shell',
  cwd         => '/var/www/dashboard',
  environment => ["HOME=/home/$user"]
}->
file { '/var/www/dashboard/storage/logs/laravel.log':
  ensure => present,
  owner  => $www_user,
  group  => $storage_group,
  mode   => '0664'
}->
## Only make symlink if installed successfully
file { '/var/www/dashboard':
  ensure => link,
  target => "/var/www/_releases/dashboard/$dashboard_branch",
}

