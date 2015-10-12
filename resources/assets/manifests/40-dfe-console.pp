################################################################################
# DreamFactory Enterprise(tm) Installer Manifest
# (c) 2012-∞ by DreamFactory Software, Inc. All Rights Reserved.
#
# Install dreamfactory/dfe-console
################################################################################

############
## Classes
############

## Defines the console .env settings. Relies on FACTER_* data
class consoleEnvironmentSettings( $root, $zone, $domain, $protocol = 'https') {
## Define our stuff
  $_env = { 'path' => "$root/.env", }

  $_appUrl = "$protocol://console.${zone}.${domain}"

  $_settings = {
    '' => {
      'APP_DEBUG'                                  => $app_debug,
      'APP_URL'                                    => $_appUrl,
      'DB_HOST'                                    => $db_host,
      'DB_DATABASE'                                => $db_name,
      'DB_USERNAME'                                => $db_user,
      'DB_PASSWORD'                                => $db_pwd,
      'DFE_CLUSTER_ID'                             => "cluster-${zone}",
      'DFE_DEFAULT_CLUSTER'                        => "cluster-${zone}",
      'DFE_DEFAULT_DATABASE'                       => "db-${zone}",
      'DFE_SCRIPT_USER'                            => $user,
      'DFE_DEFAULT_DNS_ZONE'                       => $zone,
      'DFE_DEFAULT_DNS_DOMAIN'                     => $domain,
      'DFE_DEFAULT_DOMAIN'                         => "${zone}.${domain}",
      'DFE_DEFAULT_DOMAIN_PROTOCOL'                => $default_protocol,
      'DFE_STATIC_ZONE_NAME'                       => $static_zone_name,
      'SMTP_DRIVER'                                => 'smtp',
      'SMTP_HOST'                                  => $smtp_host,
      'SMTP_PORT'                                  => $smtp_port,
      'MAIL_FROM_ADDRESS'                          => $mail_from_address,
      'MAIL_FROM_NAME'                             => $mail_from_name,
      'MAIL_USERNAME'                              => $mail_username,
      'MAIL_PASSWORD'                              => $mail_password,
      'DFE_HOSTED_BASE_PATH'                       => $storage_path,
      'DFE_CONSOLE_API_URL'                        => "$_appUrl/api/v1/ops",
    }
  }

## Update the .env file
  create_ini_settings($_settings, $_env)
}

class laravelDirectories( $root, $owner, $group, $mode = 2775) {

  file { [
    "$root/bootstrap",
  ]:
    ensure => directory,
    owner  => $user,
    group  => $group,
    mode   => $mode,
  }->
  file { [
    "$root/bootstrap/cache",
  ]:
    ensure => directory,
    owner  => $www_user,
    group  => $group,
    mode   => $mode,
  }->
  file { [
    "$root/storage",
    "$root/storage/framework",
    "$root/storage/framework/sessions",
    "$root/storage/framework/views",
    "$root/storage/logs",
  ]:
    ensure => directory,
    owner  => $www_user,
    group  => $group,
    mode   => $mode,
  }->
  file { "$root/storage/logs/laravel.log":
    ensure => present,
    owner  => $www_user,
    group  => $group,
    mode   => 0664
  }
}

############
## Logic
############

##------------------------------------------------------------------------------
## Check out the repo, update composer, change file permissions...
##------------------------------------------------------------------------------

vcsrepo { "$console_release/$console_branch":
  ensure   => present,
  provider => git,
  source   => $console_repo,
  user     => $user,
  owner    => $user,
  group    => $www_group,
  revision => $console_version
}->
file { $console_root:
  ensure => link,
  target => "$console_release/$console_branch",
}->
file { "$console_root/.env":
  ensure => present,
  owner  => $user,
  group  => $www_group,
  mode   => 0750,
  source => "$console_root/.env-dist",
}->
class { dfe::consoleEnvironmentSettings:
## Applies INI settings in $_settings to .env
  root     => $console_root,
  zone     => $vendor_id,
  domain   => $domain,
  protocol => $default_protocol,
}->
class { dfe::laravelDirectories:
  root  => $console_root,
  owner => $www_user,
  group => $group,
}->
exec { 'console-composer-update':
  command     => "$composer_bin update",
  user        => $user,
  provider    => shell,
  cwd         => $console_root,
  environment => [ "HOME=/home/$user", ]
}->
exec { 'generate-app-key':
  command     => "$artisan key:generate",
  user        => $user,
  provider    => shell,
  cwd         => $console_root,
  environment => ["HOME=/home/$user"]
}->
exec { 'console-setup':
  command     => "$artisan dfe:setup --force --admin-password='${admin_pwd}' '${admin_email}'",
  user        => $user,
  provider    => shell,
  cwd         => $console_root,
  environment => ["HOME=/home/$user"]
}->
exec { 'add_console_keys':
  command  => "cat $console_root/database/dfe/console.env >> $console_root/.env",
  provider => shell,
  user     => $user
}->
file { "$doc_root_base_path/.dfe.cluster.json":
  ensure => present,
  owner  => $user,
  group  => $www_group,
  mode   => 0644,
  source => "$console_root/database/dfe/.dfe.cluster.json"
}->
exec { 'add_web_server':
  command     => "$artisan dfe:server create web-${vendor_id} -t web -a ${vendor_id}.${domain} -m ${default_local_mount_name} -c {}",
  user        => $user,
  provider    => shell,
  cwd         => $console_root,
}->
exec { 'add_app_server':
  command     => "$artisan dfe:server create app-${vendor_id} -t app -a ${vendor_id}.${domain} -m ${default_local_mount_name} -c {}",
  user        => $user,
  provider    => shell,
  cwd         => $console_root,
  environment => ["HOME=/home/$user"]
}->
exec { 'add_db_server':
  command     => "$artisan dfe:server create db-${vendor_id} -t db -a ${vendor_id}.${domain} -m ${default_local_mount_name} -c '{\"port\":\"3306\",\"username\":\"${db_user}\",\"password\":\"${db_password}\",\"driver\":\"mysql\",\"default-database-name\":\"\",\"multi-assign\":\"on\"}'",
  user        => $user,
  provider    => shell,
  cwd         => $console_root,
  environment => ["HOME=/home/$user"]
}->
exec { 'add_cluster':
  command     => "$artisan dfe:cluster create cluster-${vendor_id} --subdomain ${vendor_id}.${domain}",
  user        => $user,
  provider    => shell,
  cwd         => $console_root,
  environment => ["HOME=/home/$user"]
}->
exec { 'add_web_to_cluster':
  command     => "$artisan dfe:cluster add cluster-${vendor_id} --server-id web-${vendor_id}",
  user        => $user,
  provider    => shell,
  cwd         => $console_root,
  environment => ["HOME=/home/$user"]
}->
exec { 'add_app_to_cluster':
  command     => "$artisan dfe:cluster add cluster-${vendor_id} --server-id app-${vendor_id}",
  user        => $user,
  provider    => shell,
  cwd         => $console_root,
  environment => ["HOME=/home/$user"]
}->
exec { 'add_db_to_cluster':
  command     => "$artisan dfe:cluster add cluster-${vendor_id} --server-id db-${vendor_id}",
  user        => $user,
  provider    => shell,
  cwd         => $console_root,
  environment => ["HOME=/home/$user"]
}->
exec { 'clear-cache-and-optimize':
  command     => "$artisan clear-compiled; $artisan cache:clear; $artisan config:clear; $artisan optimize",
  user        => $user,
  provider    => shell,
  cwd         => $console_root,
  environment => ["HOME=/home/$user"]
}