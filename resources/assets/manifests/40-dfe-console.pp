################################################################################
# DreamFactory Enterprise(tm) Installer Manifest
# (c) 2012-∞ by DreamFactory Software, Inc. All Rights Reserved.
#
# Install dreamfactory/dfe-console
################################################################################

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
class { consoleEnvironmentSettings:
## Applies INI settings in $_settings to .env
  root     => $console_root,
  zone     => $vendor_id,
  domain   => $domain,
  protocol => $default_protocol,
}->
class { laravelDirectories:
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
}->
file { "$console_root/storage/logs/laravel.log":
  ensure => present,
  owner  => $www_user,
  group  => $group,
  mode   => 0664
}
