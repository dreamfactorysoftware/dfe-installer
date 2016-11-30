################################################################################
# DreamFactory Enterprise(tm) Installer Manifest
# (c) 2012-2112 by DreamFactory Software, Inc. All Rights Reserved.
#
# Installs MySQL/Percona server
################################################################################

notify { 'announce-thyself': message => '[DFE] Install/update database software', }
Exec { path => ['/usr/bin','/usr/sbin','/bin','/sbin'], }

# Check to see if bringing their own database, in which case, don't install percona
if false == str2bool($exists_service_db)  {

  ##  Keep up-to-date
  class { 'apt':
    update => {
      frequency => 'always'
    }
  }

  exec { "apt-update":
    command => "/usr/bin/apt-get update"
  }

  apt::source { "percona.trusty":
    comment    => "Repo for percona db server",
    location   => "http://repo.percona.com/apt",
    release    => "trusty",
    repos      => "main",
    key        => {
      id      => '8507EFA5',
      server  => 'keyserver.ubuntu.com'
    },
    include    => {
      src => false,
      deb => true,
    },
    notify     => Exec['apt-update'],
  }

  class { mysql::server:
    root_password    => $mysql_root_pwd,
    restart          => true,
    package_name     => "percona-server-server-${percona_version}",
    require          => Apt::Source["percona.trusty"],
  }

  class { mysql::client:
    package_name => "percona-server-client-${percona_version}",
    require      => Apt::Source["percona.trusty"],
  }


  ## Install database on non updates
  if ( false == str2bool($dfe_update)) {
    mysql::db { $db_name:
      ensure    => present,
      charset   => "utf8",
      host      => $db_host,
      user      => $db_user,
      password  => $db_pwd,
      require   => Apt::Source["percona.trusty"],
    }

    ## Grant access to the DFE app user
    mysql_grant { "${db_user}@${db_host}/*.*":
      ensure     => present,
      options    => ["GRANT"],
      privileges => ["ALL"],
      table      => "*.*",
      user       => "${db_user}@${db_host}",
      require    => Mysql::Db[$db_name],
    }
  }

  ## Ensure $user is in the mysql group
  group { 'mysql':
    ensure    => present,
    members   => [$user],
    require   => Apt::Source["percona.trusty"],
  }

}

#common settings for all conditions
ini_setting { 'update_db_host':
  ensure  => present,
  path    => "$pwd/.env",
  setting => 'DB_HOST',
  value   => $db_host
}->
ini_setting { 'update_db_name':
  ensure  => present,
  path    => "$pwd/.env",
  setting => 'DB_DATABASE',
  value   => $db_name
}->
ini_setting { 'update_db_user':
  ensure  => present,
  path    => "$pwd/.env",
  setting => 'DB_USERNAME',
  value   => $db_user
}->
ini_setting { 'update_db_pass':
  ensure  => present,
  path    => "$pwd/.env",
  setting => 'DB_PASSWORD',
  value   => $db_pwd
}


if false == str2bool($exists_service_db)  {

  exec { "artisan_clear_config":
    command     => "$artisan config:clear",
    require     => mysql_grant["${db_user}@${db_host}/*.*"]
  }->
  exec { "artisan_run_migrations":
    command     => "$artisan setup:db",
    require     => mysql_grant["${db_user}@${db_host}/*.*"]
  }
} else {
  exec { "artisan_clear_config":
  command     => "$artisan config:clear",
  }->
  exec { "artisan_run_migrations":
  command     => "$artisan setup:db",
  }
}

