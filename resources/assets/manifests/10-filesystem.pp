# Used to make sure that a few things get run before anything else does

stage { 'pre':
  before => Stage['main']
}

## Set up the code paths
file { [
  $mount_point,
  $doc_root_base_path,
  $release_path,
  "$release_path/console",
  "$release_path/dashboard",
  "$release_path/dreamfactory",
]:
  ensure => directory,
  owner  => $user,
  group  => $www_group,
  mode   => 2775,
}->
## Create the storage and log paths
file { [
  $storage_path,
  $log_path,
  "$log_path/console",
  "$log_path/dashboard",
  "$log_path/hosted",
  "$mount_point/trash",]:
  ensure  => directory,
  owner   => $storage_user,
  group   => $storage_group,
  mode    => 2775,
}->
## And our indicator files
file { [ "$doc_root_base_path/.dfe-managed", "$doc_root_base_path/.maintenace-mode.off"]:
  ensure => present,
}
