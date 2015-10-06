# Used to make sure that a few things get run before anything else does

stage { 'pre':
  before => Stage['main']
}

# Set up the WWW directory structure
file { [
  '/var/www', '/var/www/_releases',
  '/var/www/_releases/dashboard',
  '/var/www/_releases/dreamfactory',
  '/var/www/_releases/console', $mount_point,]:
  ensure => directory,
  owner  => $user,
  group  => $www_group,
  mode   => 2775
}->
file { '/var/www/.dfe-managed':
  ensure => present
}->
file { '/var/www/.maintenance-mode.off':
  ensure => present
}->
# Create the storage and log paths
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
}
