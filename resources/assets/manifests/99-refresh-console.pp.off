################################################################################
# DreamFactory Enterprise(tm) Installer Manifest
# (c) 2012-∞ by DreamFactory Software, Inc. All Rights Reserved.
#
# Refreshes the console
################################################################################

notify { 'announce-thyself': message => '[DFE] Refreshing console', }
Exec { path => ['/usr/bin','/usr/sbin','/bin','/sbin'], }

## Checks directory/file permissions
class checkPermissions( $root, $dir_mode = '2775', $file_mode = '0664' ) {
  exec { "clear-console-cache":
    command     => "sudo rm -rf bootstrap/cache/* storage/frameword/sessions/* storage/framework/views/*",
    user        => $user,
    provider    => shell,
    cwd         => $root,
    timeout     => 1800,
    environment => [ "HOME=/home/$user", ]
  }->
  exec { 'chown-and-pwn':
    user            => root,
    command         => "sudo chown -R ${www_user}:${group} ${root}/storage/ ${root}/bootstrap/cache/",
    cwd             => $root,
    environment     => ["HOME=/home/${user}"]
  }->
  exec { 'chmod-storage':
    user            => root,
    command         => "find ${root}/storage -type d -exec sudo chmod ${dir_mode} {} \\;",
    onlyif          => "test -d ${root}/storage",
    cwd             => $root,
    environment     => ["HOME=/home/${user}"]
  }->
  exec { 'chmod-storage-files':
    user            => root,
    command         => "find ${root}/storage -type f -exec sudo chmod ${file_mode} {} \\;",
    onlyif          => "test -d ${root}/storage",
    cwd             => $root,
    environment     => ["HOME=/home/${user}"]
  }->
  exec { "check-bootstrap-cache":
    user            => root,
    command         => "sudo chmod ${file_mode} ${root}/bootstrap/cache/* && sudo chown ${www_user}:${group} ${root}/bootstrap/cache/*",
    onlyif          => "test -f ${root}/bootstrap/cache/compiled.php",
    cwd             => $root,
  }->
  exec { "check-storage-log-file":
    user            => root,
    command         => "sudo chmod ${file_mode} ${root}/storage/logs/*.log && sudo chown ${www_user}:${group} ${root}/storage/logs/*.log",
    onlyif          => "test -f $root/storage/logs/laravel.log",
    cwd             => $root,
  }
}

############
## Console
############

exec { "pull-latest-console":
  command     => "git checkout $console_branch; git pull",
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
class { checkPermissions:
  root => $console_root,
}
