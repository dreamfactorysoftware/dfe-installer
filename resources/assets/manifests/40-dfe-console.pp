## Installs dreamfactorysoftware/dfe-console

$_env = { 'path' => "$doc_root_base_path/console/.env", }
$_appUrl = "http://console.${vendor_id}.${domain}"
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

vcsrepo { "$release_path/console/$console_branch":
  ensure   => present,
  provider => git,
  source   => $console_repo,
  user     => $user,
  owner    => $user,
  group    => $www_group,
  revision => $console_version
}->file { "$doc_root_base_path/console":
  ensure => link,
  target => "$release_path/console/$console_branch",
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
file { "$doc_root_base_path/console/.env":
  ensure => present,
  source => "$doc_root_base_path/console/.env-dist",
}->
exec { 'console-config':
  command     => "$composer_bin update",
  user        => $user,
  provider    => 'shell',
  cwd         => "$doc_root_base_path/console",
  environment => [ "HOME=/home/$user", ]
}

## Create .env file
create_ini_settings($_settings, $_env)

exec { 'generate-app-key':
  command     => "$artisan key:generate",
  user        => $user,
  provider    => 'shell',
  cwd         => "$doc_root_base_path/console",
  environment => ["HOME=/home/$user"]
}->
exec { 'console-setup':
  command     => "$artisan dfe:setup --force --admin-password=\"${admin_pwd}\" ${admin_email}",
  user        => $user,
  provider    => 'shell',
  cwd         => "$doc_root_base_path/console",
  environment => ["HOME=/home/$user"]
}->
file { "$doc_root_base_path/.dfe.cluster.json":
  ensure => present,
  owner  => $user,
  group  => $www_group,
  mode   => 0644,
  source => "$doc_root_base_path/console/database/dfe/.dfe.cluster.json"
}->exec { 'add_console_keys':
  command  => "cat $doc_root_base_path/console/database/dfe/console.env >> $doc_root_base_path/console/.env",
  provider => 'shell',
  user     => $user
}

exec { 'add_web_server':
  command     => "$artisan dfe:server create web-${vendor_id} -t web -a ${vendor_id}.${domain} -m ${default_local_mount_name} -c {}",
  user        => $user,
  provider    => 'shell',
  cwd         => "$doc_root_base_path/console",
  environment => ["HOME=/home/$user"]
}->
exec { 'add_app_server':
  command     => "$artisan dfe:server create app-${vendor_id} -t app -a ${vendor_id}.${domain} -m ${default_local_mount_name} -c {}",
  user        => $user,
  provider    => 'shell',
  cwd         => "$doc_root_base_path/console",
  environment => ["HOME=/home/$user"]
}->
exec { 'add_db_server':
  command     => "$artisan dfe:server create db-${vendor_id} -t db -a ${vendor_id}.${domain} -m ${default_local_mount_name} -c '{\"port\":\"3306\",\"username\":\"${db_user}\",\"password\":\"${db_password}\",\"driver\":\"mysql\",\"default-database-name\":\"\",\"multi-assign\":\"on\"}'",
  user        => $user,
  provider    => 'shell',
  cwd         => "$doc_root_base_path/console",
  environment => ["HOME=/home/$user"]
}->
exec { 'add_cluster':
  command     => "$artisan dfe:cluster create cluster-${vendor_id} --subdomain ${vendor_id}.${domain}",
  user        => $user,
  provider    => 'shell',
  cwd         => "$doc_root_base_path/console",
  environment => ["HOME=/home/$user"]
}->
exec { 'add_web_to_cluster':
  command     => "$artisan dfe:cluster add cluster-${vendor_id} --server-id web-${vendor_id}",
  user        => $user,
  provider    => 'shell',
  cwd         => "$doc_root_base_path/console",
  environment => ["HOME=/home/$user"]
}->
exec { 'add_app_to_cluster':
  command     => "$artisan dfe:cluster add cluster-${vendor_id} --server-id app-${vendor_id}",
  user        => $user,
  provider    => 'shell',
  cwd         => "$doc_root_base_path/console",
  environment => ["HOME=/home/$user"]
}->
exec { 'add_db_to_cluster':
  command     => "$artisan dfe:cluster add cluster-${vendor_id} --server-id db-${vendor_id}",
  user        => $user,
  provider    => 'shell',
  cwd         => "$doc_root_base_path/console",
  environment => ["HOME=/home/$user"]
}

exec { 'clear_and_regenerate_cache':
  command     => "$artisan clear-compiled; $artisan cache:clear; $artisan config:clear; $artisan optimize",
  user        => $user,
  provider    => 'shell',
  cwd         => "$doc_root_base_path/console",
  environment => ["HOME=/home/$user"]
}

file { "$doc_root_base_path/console/storage/logs/laravel.log":
  ensure => present,
  owner  => $www_user,
  group  => $storage_group,
  mode   => 0664
}
