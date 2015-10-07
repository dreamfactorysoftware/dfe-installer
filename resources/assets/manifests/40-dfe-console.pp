## Installs dreamfactorysoftware/dfe-console

vcsrepo { "$release_path/console/$console_branch":
  ensure   => present,
  provider => git,
  source   => "https://${github_user_info}github.com/dreamfactorysoftware/dfe-console.git",
  user     => $user,
  owner    => $user,
  group    => $www_group,
  revision => $console_version
}->
exec { 'console-config':
  command     => "$composer_bin update",
  user        => $user,
  provider    => 'shell',
  cwd         => "$doc_root_base_path/console",
  environment => [ "HOME=/home/$user", ]
}->
file { "$doc_root_base_path/console/.env":
  ensure => present,
  source => "$doc_root_base_path/console/.env-dist",
}

## Create .env file
$_env = { 'path' => "$doc_root_base_path/console/.env", }

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

ini_setting { 'APP_URL':
  ensure  => present,
  path    => '/var/www/console/.env',
  section => '',
  setting => 'APP_URL',
  value   => "http://console.${vendor_id}.${domain}"
}->
ini_setting { 'DB_HOST':
  ensure  => present,
  path    => '/var/www/console/.env',
  section => '',
  setting => 'DB_HOST',
  value   => "${db_host}"
}->
ini_setting { 'DB_DATABASE':
  ensure  => present,
  path    => '/var/www/console/.env',
  section => '',
  setting => 'DB_DATABASE',
  value   => "${db_name}"
}->
ini_setting { 'DB_USERNAME':
  ensure  => present,
  path    => '/var/www/console/.env',
  section => '',
  setting => 'DB_USERNAME',
  value   => "${db_user}"
}->
ini_setting { 'DB_PASSWORD':
  ensure  => present,
  path    => '/var/www/console/.env',
  section => '',
  setting => 'DB_PASSWORD',
  value   => "${db_pwd}"
}->
ini_setting { 'MAILGUN_DOMAIN':
  ensure  => absent,
  path    => '/var/www/console/.env',
  section => '',
  setting => 'MAILGUN_DOMAIN',
}->
ini_setting { 'MAILGUN_SECRET_KEY':
  ensure  => absent,
  path    => '/var/www/console/.env',
  section => '',
  setting => 'MAILGUN_SECRET_KEY',
}->
ini_setting { 'DFE_CLUSTER_ID':
  ensure  => present,
  path    => '/var/www/console/.env',
  section => '',
  setting => 'DFE_CLUSTER_ID',
  value   => "cluster-${vendor_id}"
}->
ini_setting { 'DFE_DEFAULT_CLUSTER':
  ensure  => present,
  path    => '/var/www/console/.env',
  section => '',
  setting => 'DFE_DEFAULT_CLUSTER',
  value   => "cluster-${vendor_id}"
}->
ini_setting { 'DFE_DEFAULT_DATABASE':
  ensure  => present,
  path    => '/var/www/console/.env',
  section => '',
  setting => 'DFE_DEFAULT_DATABASE',
  value   => "db-${vendor_id}"
}->
ini_setting { 'DFE_SCRIPT_USER':
  ensure  => present,
  path    => '/var/www/console/.env',
  section => '',
  setting => 'DFE_SCRIPT_USER',
  value   => "${user}"
}->
ini_setting { 'DFE_DEFAULT_DNS_ZONE':
  ensure  => present,
  path    => '/var/www/console/.env',
  section => '',
  setting => 'DFE_DEFAULT_DNS_ZONE',
  value   => "${vendor_id}"
}->
ini_setting { 'DFE_DEFAULT_DOMAIN':
  ensure  => present,
  path    => '/var/www/console/.env',
  section => '',
  setting => 'DFE_DEFAULT_DOMAIN',
  value   => ".${vendor_id}.${domain}"
}->
ini_setting { 'DFE_DEFAULT_DNS_DOMAIN':
  ensure  => present,
  path    => '/var/www/console/.env',
  section => '',
  setting => 'DFE_DEFAULT_DNS_DOMAIN',
  value   => ".${domain}"
}->
ini_setting { 'SMTP_DRIVER':
  ensure  => present,
  path    => '/var/www/console/.env',
  section => '',
  setting => 'SMTP_DRIVER',
  value   => "smtp"
}->
ini_setting { 'SMTP_HOST':
  ensure  => present,
  path    => '/var/www/console/.env',
  section => '',
  setting => 'SMTP_HOST',
  value   => "${smtp_host}"
}->
ini_setting { 'SMTP_PORT':
  ensure  => present,
  path    => '/var/www/console/.env',
  section => '',
  setting => 'SMTP_PORT',
  value   => "${smtp_port}"
}->
ini_setting { 'MAIL_FROM_ADDRESS':
  ensure  => present,
  path    => '/var/www/console/.env',
  section => '',
  setting => 'MAIL_FROM_ADDRESS',
  value   => "${mail_from_address}"
}->
ini_setting { 'MAIL_FROM_NAME':
  ensure  => present,
  path    => '/var/www/console/.env',
  section => '',
  setting => 'MAIL_FROM_NAME',
  value   => "${mail_from_name}"
}->
ini_setting { 'MAIL_USERNAME':
  ensure  => present,
  path    => '/var/www/console/.env',
  section => '',
  setting => 'MAIL_USERNAME',
  value   => "${mail_username}"
}->
ini_setting { 'MAIL_PASSWORD':
  ensure  => present,
  path    => '/var/www/console/.env',
  section => '',
  setting => 'MAIL_PASSWORD',
  value   => "${mail_password}"
}->
ini_setting { 'DFE_HOSTED_BASE_PATH':
  ensure  => present,
  path    => '/var/www/console/.env',
  section => '',
  setting => 'DFE_HOSTED_BASE_PATH',
  value   => "${storage_path}"
}->
ini_setting { 'DFE_CONSOLE_API_URL':
  ensure  => present,
  path    => '/var/www/console/.env',
  section => '',
  setting => 'DFE_CONSOLE_API_URL',
  value   => "http://console.${vendor_id}.${domain}/api/v1/ops"
}->
file { ["/var/www/_releases/console/$console_branch/bootstrap",
  "/var/www/_releases/console/$console_branch/bootstrap/cache",
  "/var/www/_releases/console/$console_branch/storage",
  "/var/www/_releases/console/$console_branch/storage/framework",
  "/var/www/_releases/console/$console_branch/storage/framework/sessions",
  "/var/www/_releases/console/$console_branch/storage/framework/views",
  "/var/www/_releases/console/$console_branch/storage/logs",]:
  ensure => present,
  owner  => $www_user,
  group  => $group,
  mode   => 2775
}->
exec { 'generate-app-key':
  command     => "$artisan key:generate",
  user        => $user,
  provider    => 'shell',
  cwd         => '/var/www/console',
  environment => ["HOME=/home/$user"]
}->
exec { 'console-setup':
  command     => "$artisan dfe:setup --force --admin-password=\"${admin_pwd}\" ${admin_email}",
  user        => $user,
  provider    => 'shell',
  cwd         => '/var/www/console',
  environment => ["HOME=/home/$user"]
}->
exec { 'add_mount_local':
  command     => "$artisan dfe:mount create ${default_local_mount_name} -t LOCAL -p ${storage_path} -c {\"disk\":\"local\"}",
  user        => $user,
  provider    => 'shell',
  cwd         => '/var/www/console',
  environment => ["HOME=/home/$user"]
}->
exec { 'add_web_server':
  command     => "$artisan dfe:server create web-${vendor_id} -t web -a ${vendor_id}.${domain} -m ${default_local_mount_name} -c {}",
  user        => $user,
  provider    => 'shell',
  cwd         => '/var/www/console',
  environment => ["HOME=/home/$user"]
}->
exec { 'add_app_server':
  command     => "$artisan dfe:server create app-${vendor_id} -t app -a ${vendor_id}.${domain} -m ${default_local_mount_name} -c {}",
  user        => $user,
  provider    => 'shell',
  cwd         => '/var/www/console',
  environment => ["HOME=/home/$user"]
}->
exec { 'add_db_server':
  command     => "$artisan dfe:server create db-${vendor_id} -t db -a ${vendor_id}.${domain} -m ${default_local_mount_name} -c '{\"port\":\"3306\",\"username\":\"${db_user}\",\"password\":\"${db_password}\",\"driver\":\"mysql\",\"default-database-name\":\"\",\"multi-assign\":\"on\"}'",
  user        => $user,
  provider    => 'shell',
  cwd         => '/var/www/console',
  environment => ["HOME=/home/$user"]
}->
exec { 'add_cluster':
  command     => "$artisan dfe:cluster create cluster-${vendor_id} --subdomain ${vendor_id}.${domain}",
  user        => $user,
  provider    => 'shell',
  cwd         => '/var/www/console',
  environment => ["HOME=/home/$user"]
}->
exec { 'add_web_to_cluster':
  command     => "$artisan dfe:cluster add cluster-${vendor_id} --server-id web-${vendor_id}",
  user        => $user,
  provider    => 'shell',
  cwd         => '/var/www/console',
  environment => ["HOME=/home/$user"]
}->
exec { 'add_app_to_cluster':
  command     => "$artisan dfe:cluster add cluster-${vendor_id} --server-id app-${vendor_id}",
  user        => $user,
  provider    => 'shell',
  cwd         => '/var/www/console',
  environment => ["HOME=/home/$user"]
}->
exec { 'add_db_to_cluster':
  command     => "$artisan dfe:cluster add cluster-${vendor_id} --server-id db-${vendor_id}",
  user        => $user,
  provider    => 'shell',
  cwd         => '/var/www/console',
  environment => ["HOME=/home/$user"]
}->
exec { 'add_console_keys':
  command  => "cat /var/www/console/database/dfe/console.env >> /var/www/console/.env",
  provider => 'shell',
  user     => $user
}->
exec { 'clear_and_regenerate_cache':
  command     => "$artisan clear-compiled; $artisan cache:clear; $artisan config:clear; $artisan optimize",
  user        => $user,
  provider    => 'shell',
  cwd         => '/var/www/console',
  environment => ["HOME=/home/$user"]
}->
file { '/var/www/console/storage/logs/laravel.log':
  ensure => present,
  owner  => $www_user,
  group  => $storage_group,
  mode   => 0664
}->
file { '/var/www/.dfe.cluster.json':
  ensure => present,
  owner  => $user,
  group  => $www_group,
  mode   => 0644,
  source => '/var/www/console/database/dfe/.dfe.cluster.json'
}->
file { '/var/www/console':
  ensure => link,
  target => "/var/www/_releases/console/$console_branch",
}
