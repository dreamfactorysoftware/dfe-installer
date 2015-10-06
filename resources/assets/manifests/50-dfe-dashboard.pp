vcsrepo { "/var/www/_releases/dashboard/$dashboard_branch":
  ensure   => present,
  provider => git,
  source   => "https://${github_user_info}github.com/dreamfactorysoftware/dfe-dashboard.git",
  user     => $user,
  owner    => $group,
  group    => $www_group,
  revision => $dashboard_version,
}->
file { '/var/www/dashboard':
  ensure => link,
  target => "/var/www/_releases/dashboard/$dashboard_branch"
}->
exec { 'dashboard-config':
  command     => '/usr/local/bin/composer update',
  user        => $user,
  provider    => 'shell',
  cwd         => '/var/www/dashboard',
  environment => ["HOME=/home/$user"]
}->
file { '/var/www/dashboard/.env':
  ensure => present,
  source => '/var/www/dashboard/.env-dist'
}->
exec { 'add_console_keys':
  command  => "cat /var/www/console/database/dfe/dashboard.env >> /var/www/dashboard/.env",
  provider => 'shell',
  user     => $user
}->
exec { 'generate-app-key':
  command     => "/usr/bin/php artisan key:generate",
  user        => $user,
  provider    => 'shell',
  cwd         => '/var/www/dashboard',
  environment => ["HOME=/home/$user"]
}->
ini_setting { "APP_DEBUG":
  ensure  => present,
  path    => '/var/www/dashboard/.env',
  section => '',
  setting => 'APP_DEBUG',
  value   => $app_debug
}->
ini_setting { 'APP_URL':
  ensure  => present,
  path    => '/var/www/dashboard/.env',
  section => '',
  setting => 'APP_URL',
  value   => "http://console.${vendor_id}.${domain}"
}->
ini_setting { 'DB_HOST':
  ensure  => present,
  path    => '/var/www/dashboard/.env',
  section => '',
  setting => 'DB_HOST',
  value   => "${db_host}"
}->
ini_setting { 'DB_DATABASE':
  ensure  => present,
  path    => '/var/www/dashboard/.env',
  section => '',
  setting => 'DB_DATABASE',
  value   => "${db_name}"
}->
ini_setting { 'DB_USERNAME':
  ensure  => present,
  path    => '/var/www/dashboard/.env',
  section => '',
  setting => 'DB_USERNAME',
  value   => "${db_user}"
}->
ini_setting { 'DB_PASSWORD':
  ensure  => present,
  path    => '/var/www/dashboard/.env',
  section => '',
  setting => 'DB_PASSWORD',
  value   => "${db_pwd}"
}->
ini_setting { 'MAILGUN_DOMAIN':
  ensure  => absent,
  path    => '/var/www/dashboard/.env',
  section => '',
  setting => 'MAILGUN_DOMAIN',
}->
ini_setting { 'MAILGUN_SECRET_KEY':
  ensure  => absent,
  path    => '/var/www/dashboard/.env',
  section => '',
  setting => 'MAILGUN_SECRET_KEY',
}->
ini_setting { 'DFE_PARTNER_ID':
  ensure  => present,
  path    => '/var/www/console/.env',
  section => '',
  setting => 'DFE_PARTNER_ID',
  value   => "${vendor_id}"
}->
ini_setting { 'DFE_CLUSTER_ID':
  ensure  => present,
  path    => '/var/www/dashboard/.env',
  section => '',
  setting => 'DFE_CLUSTER_ID',
  value   => "cluster-${vendor_id}"
}->
ini_setting { 'DFE_DEFAULT_CLUSTER':
  ensure  => present,
  path    => '/var/www/dashboard/.env',
  section => '',
  setting => 'DFE_DEFAULT_CLUSTER',
  value   => "cluster-${vendor_id}"
}->
ini_setting { 'DFE_DEFAULT_DATABASE':
  ensure  => present,
  path    => '/var/www/dashboard/.env',
  section => '',
  setting => 'DFE_DEFAULT_DATABASE',
  value   => "db-${vendor_id}"
}->
ini_setting { 'DFE_SCRIPT_USER':
  ensure  => present,
  path    => '/var/www/dashboard/.env',
  section => '',
  setting => 'DFE_SCRIPT_USER',
  value   => "${user}"
}->
ini_setting { 'DFE_DEFAULT_DNS_ZONE':
  ensure  => present,
  path    => '/var/www/dashboard/.env',
  section => '',
  setting => 'DFE_DEFAULT_DNS_ZONE',
  value   => "${vendor_id}"
}->
ini_setting { 'DFE_DEFAULT_DOMAIN':
  ensure  => present,
  path    => '/var/www/dashboard/.env',
  section => '',
  setting => 'DFE_DEFAULT_DOMAIN',
  value   => ".${vendor_id}.${domain}"
}->
ini_setting { 'DFE_DEFAULT_DNS_DOMAIN':
  ensure  => present,
  path    => '/var/www/dashboard/.env',
  section => '',
  setting => 'DFE_DEFAULT_DNS_DOMAIN',
  value   => ".${domain}"
}->
ini_setting { 'SMTP_DRIVER':
  ensure  => present,
  path    => '/var/www/dashboard/.env',
  section => '',
  setting => 'SMTP_DRIVER',
  value   => "smtp"
}->
ini_setting { 'SMTP_HOST':
  ensure  => present,
  path    => '/var/www/dashboard/.env',
  section => '',
  setting => 'SMTP_HOST',
  value   => "${smtp_host}"
}->
ini_setting { 'SMTP_PORT':
  ensure  => present,
  path    => '/var/www/dashboard/.env',
  section => '',
  setting => 'SMTP_PORT',
  value   => "${smtp_port}"
}->
ini_setting { 'MAIL_FROM_ADDRESS':
  ensure  => present,
  path    => '/var/www/dashboard/.env',
  section => '',
  setting => 'MAIL_FROM_ADDRESS',
  value   => "${mail_from_address}"
}->
ini_setting { 'MAIL_FROM_NAME':
  ensure  => present,
  path    => '/var/www/dashboard/.env',
  section => '',
  setting => 'MAIL_FROM_NAME',
  value   => "${mail_from_name}"
}->
ini_setting { 'MAIL_USERNAME':
  ensure  => present,
  path    => '/var/www/dashboard/.env',
  section => '',
  setting => 'MAIL_USERNAME',
  value   => "${mail_username}"
}->
ini_setting { 'MAIL_PASSWORD':
  ensure  => present,
  path    => '/var/www/dashboard/.env',
  section => '',
  setting => 'MAIL_PASSWORD',
  value   => "${mail_password}"
}->
ini_setting { 'DFE_HOSTED_BASE_PATH':
  ensure  => present,
  path    => '/var/www/dashboard/.env',
  section => '',
  setting => 'DFE_HOSTED_BASE_PATH',
  value   => "${mount_point}/storage"
}->
ini_setting { 'DFE_CONSOLE_API_URL':
  ensure  => present,
  path    => '/var/www/dashboard/.env',
  section => '',
  setting => 'DFE_CONSOLE_API_URL',
  value   => "http://console.${vendor_id}.${domain}/api/v1/ops"
}->
file { "/var/www/_releases/dashboard/$dashboard_branch/storage":
  ensure => directory,
  owner  => $user,
  group  => $www_group,
  mode   => "2775"
}->
file { "/var/www/_releases/dashboard/$dashboard_branch/storage/framework":
  ensure => directory,
  owner  => $user,
  group  => $www_group,
  mode   => "2775"
}->
file { "/var/www/_releases/dashboard/$dashboard_branch/storage/logs":
  ensure => directory,
  owner  => $user,
  group  => $www_group,
  mode   => "2775"
}->
file { "/var/www/_releases/dashboard/$dashboard_branch/bootstrap/cache":
  ensure => directory,
  owner  => $user,
  group  => $www_group,
  mode   => "2775"
}->
file { "/var/www/_releases/dashboard/$dashboard_branch/storage/framework/sessions":
  ensure => directory,
  owner  => $user,
  group  => $www_group,
  mode   => "2775"
}->
file { "/var/www/_releases/dashboard/$dashboard_branch/storage/framework/views":
  ensure => directory,
  owner  => $user,
  group  => $www_group,
  mode   => "2775"
}->
exec { 'clear_and_regenerate_cache':
  command     => "/usr/bin/php artisan clear-compiled; /usr/bin/php artisan cache:clear; /usr/bin/php artisan config:clear; /usr/bin/php artisan optimize",
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
file { '/var/www/dashboard/bootstrap':
  ensure => present,
  owner  => $www_user,
  group  => $storage_group,
  mode   => '2775'
}->
file { '/var/www/dashboard/bootstrap/cache':
  ensure => present,
  owner  => $www_user,
  group  => $storage_group,
  mode   => '2775'
}
