################################################################################
# DreamFactory Enterprise(tm) Installer Manifest
# (c) 2012-∞ by DreamFactory Software, Inc. All Rights Reserved.
#
# Refreshes the apps
################################################################################

notify { 'announce-thyself': message => '[DFE] Refreshing apps', }
Exec { path => ['/usr/bin','/usr/sbin','/bin','/sbin'], }

## Checks directory/file permissions
class checkConsolePermissions( $root, $dir_mode = '2775', $file_mode = '0664' ) {
  exec { 'chown-and-pwn':
    user            => root,
    command         => "chown -R ${www_user}:${group} ${root}/storage/ ${root}/bootstrap/cache/",
    cwd             => $root,
    environment     => ["HOME=/home/${user}"]
  }->
  exec { 'chmod-storage':
    user            => root,
    command         => "find ${root}/storage -type d -exec chmod ${dir_mode} {} \\;",
    onlyif          => "test -d ${root}/storage",
    cwd             => $root,
    environment     => ["HOME=/home/${user}"]
  }->
  exec { 'chmod-storage-files':
    user            => root,
    command         => "find ${root}/storage -type f -exec chmod ${file_mode} {} \\;",
    onlyif          => "test -d ${root}/storage",
    cwd             => $root,
    environment     => ["HOME=/home/${user}"]
  }->
  exec { "check-bootstrap-cache":
    user            => root,
    command         => "chmod ${file_mode} ${root}/bootstrap/cache/* && chown ${www_user}:${group} ${root}/bootstrap/cache/*",
    onlyif          => "test -f ${root}/bootstrap/cache/compiled.php",
    cwd             => $root,
  }->
  exec { "check-storage-log-file":
    user            => root,
    command         => "chmod ${file_mode} ${root}/storage/logs/*.log && chown ${www_user}:${group} ${root}/storage/logs/*.log",
    onlyif          => "test -f $root/storage/logs/laravel.log",
    cwd             => $root,
  }
}

class checkDashboardPermissions( $root, $dir_mode = '2775', $file_mode = '0664' ) {
  exec { 'chown-and-pwn':
    user            => root,
    command         => "chown -R ${www_user}:${group} ${root}/storage/ ${root}/bootstrap/cache/",
    cwd             => $root,
    environment     => ["HOME=/home/${user}"]
  }->
  exec { 'chmod-storage':
    user            => root,
    command         => "find ${root}/storage -type d -exec chmod ${dir_mode} {} \\;",
    onlyif          => "test -d ${root}/storage",
    cwd             => $root,
    environment     => ["HOME=/home/${user}"]
  }->
  exec { 'chmod-storage-files':
    user            => root,
    command         => "find ${root}/storage -type f -exec chmod ${file_mode} {} \\;",
    onlyif          => "test -d ${root}/storage",
    cwd             => $root,
    environment     => ["HOME=/home/${user}"]
  }->
  exec { "check-bootstrap-cache":
    user            => root,
    command         => "chmod ${file_mode} ${root}/bootstrap/cache/* && chown ${www_user}:${group} ${root}/bootstrap/cache/*",
    onlyif          => "test -f ${root}/bootstrap/cache/compiled.php",
    cwd             => $root,
  }->
  exec { "check-storage-log-file":
    user            => root,
    command         => "chmod ${file_mode} ${root}/storage/logs/*.log && chown ${www_user}:${group} ${root}/storage/logs/*.log",
    onlyif          => "test -f $root/storage/logs/laravel.log",
    cwd             => $root,
  }
}

############
## Console
############

exec { "pull-latest-console":
  command     => "git pull",
  user        => $user,
  provider    => shell,
  cwd         => $console_root,
  timeout     => 1800,
  environment => [ "HOME=/home/$user", ]
}->
exec { "composer-update-console":
  command     => "$composer_bin update",
  user        => $user,
  provider    => shell,
  cwd         => $console_root,
  timeout     => 1800,
  environment => [ "HOME=/home/$user", ]
}->
class { checkConsolePermissions:
  root => $console_root,
}

############
## Dashboard
############

exec { "pull-latest-dashboard":
  command     => "git pull",
  user        => $user,
  provider    => shell,
  cwd         => $dashboard_root,
  timeout     => 1800,
  environment => [ "HOME=/home/$user", ]
}->
exec { "composer-update-dashboard":
  command     => "$composer_bin update",
  user        => $user,
  provider    => shell,
  cwd         => $dashboard_root,
  timeout     => 1800,
  environment => [ "HOME=/home/$user", ]
}->
class { checkDashboardPermissions:
  root => $dashboard_root,
}

