## A class that creates the directories required for a Laravel 5+ application.
## Permissions are set accordingly.

class laravelDirectories( $root, $owner, $group, $mode = 2775) {

  file { [
    "$root/bootstrap",
    "$root/bootstrap/cache",
    "$root/storage",
    "$root/storage/framework",
    "$root/storage/framework/sessions",
    "$root/storage/framework/views",
    "$root/storage/logs",
  ]:
    ensure => directory,
    owner  => $owner,
    group  => $group,
    mode   => $mode,
  }

}