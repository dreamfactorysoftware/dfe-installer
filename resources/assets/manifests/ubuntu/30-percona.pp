################################################################################
# DreamFactory Enterprise(tm) Installer Manifest
# (c) 2012-2112 by DreamFactory Software, Inc. All Rights Reserved.
#
# Installs MySQL/Percona server
################################################################################

notify { 'announce-thyself': message => '[DFE] Install/update database software', }
Exec { path => ['/usr/bin','/usr/sbin','/bin','/sbin'], }

##  Keep up-to-date
class { 'apt':
  update => {
    frequency => 'always'
  }
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

exec { "apt-update":
  command => "/usr/bin/apt-get update"
}

apt::source { "percona.trusty":
  comment    => "Repo for percona db server",
  location   => "http://repo.percona.com/apt",
  release    => "trusty",
  repos      => "main",
  key        => '8507EFA5',
  key_server => 'keyserver.ubuntu.com',
  include    => {
    "src" => false,
    "deb" => true,
  },
  notify   => Exec['apt-update'],
}

## Install database on non updates
if ( false == str2bool($dfe_update)) {
  mysql::db { $db_name:
    ensure    => present,
    charset   => "utf8",
    host      => $db_host,
    user      => $db_user,
    password  => $db_pwd,
    sql       => "$pwd/resources/assets/sql/dfe_local.schema.sql",
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

