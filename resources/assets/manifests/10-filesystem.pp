# Used to make sure that a few things get run before anything else does

stage { 'pre':
  before => Stage['main']
}

# Set up the WWW directory structure

file { '/var/www':
  ensure  => directory,
  owner   => $user,
  group   => $www_group
}->
file { '/var/www/_releases':
  ensure => directory,
  owner  => $user,
  group  => $www_group,
  mode   => '2775'
}->
file { '/var/www/_releases/dashboard':
  ensure => directory,
  owner  => $user,
  group  => $www_group,
  mode   => '2775'
}->
file { '/var/www/_releases/dreamfactory':
  ensure => directory,
  owner  => $user,
  group  => $www_group,
  mode   => '2775'
}->
file { '/var/www/_releases/console':
  ensure => directory,
  owner  => $user,
  group  => $www_group,
  mode   => '2775'
}->
file { '/var/www/.dfe-managed':
  ensure => present
}->
file { '/var/www/.maintenance-mode.off':
  ensure => present
}->
# Make sure that the storage directory / mount point is present and has the correct permissions
file { $mount_point:
  ensure => directory,
  owner  => $user,
  group  => $www_group,
  mode   => '2775'
}
# Setup the MySQL directories
file { $mysql_data_dir:
  ensure  => directory,
  owner   => $storage_user,
  group   => $storage_group,
  mode    => '0750'
}->
file { "$mount_point/mysql-binlog":
  ensure => directory,
  owner  => $storage_user,
  group  => $storage_group,
  mode   => '0750'
}
file { $mysql_log_bin_path:
  ensure => directory,
  owner  => $storage_user,
  group  => $storage_group,
  mode   => '0750'
}
# Create the storage location
file { $storage_path:
  ensure  => directory,
  owner   => $www_user,
  group   => $storage_group,
  mode    => '2775'
}
# Setup the log directories
file { $log_path:
  ensure  => directory,
  owner   => $storage_user,
  group   => $storage_group,
  mode    => '2775',
}->
file { "$log_path/console":
  ensure  => directory,
  owner   => $www_group,
  group   => $storage_group,
  mode    => '2750',
}->
file { "$log_path/dashboard":
  ensure  => directory,
  owner   => $www_group,
  group   => $storage_group,
  mode    => '2750',
}->file { "$log_path/hosted":
  ensure  => directory,
  owner   => $www_user,
  group   => $storage_group,
  mode    => '2750',
}->
file { "$mysql_log_path":
  ensure => directory,
  owner  => $storage_user,
  group  => $storage_group,
  mode   => '0750'
}->
file { "$mount_point/trash":
  ensure => directory,
  owner  => $www_user,
  group  => $storage_group,
  mode   => '2775'
}
