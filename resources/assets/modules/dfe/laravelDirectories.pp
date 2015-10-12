## A class that creates the directories required for a Laravel 5+ application.
## Permissions are set accordingly.

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