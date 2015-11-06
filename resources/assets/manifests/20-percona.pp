################################################################################
# DreamFactory Enterprise(tm) Installer Manifest
# (c) 2012-∞ by DreamFactory Software, Inc. All Rights Reserved.
#
# Installs MySQL/Percona server
################################################################################

notify { 'announce-thyself': message => '[DFE] Install/update database software', }
stage { 'pre': before => Stage['main'], }
stage { 'post': after => Stage['main'], }

##------------------------------------------------------------------------------
## Classes
##------------------------------------------------------------------------------

##  Keep up-to-date
class { 'apt':
  stage  => 'pre',
  update => {
    frequency => 'daily',
  },
}

##  Creates a database once the server is installed
class createDatabase {
  ## Install database on non updates
  if ( false == str2bool($dfe_update)) {
    mysql::db { $db_name:
      ensure     => present,
      charset    => "utf8",
      host       => $db_host,
      user       => $db_user,
      password   => $db_pwd,
      sql        => "$pwd/resources/assets/sql/dfe_local.schema.sql",
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
}

##------------------------------------------------------------------------------
## Logic
##------------------------------------------------------------------------------

apt::source { "percona.trusty":
  comment  => "Repo for percona db server",
  location => "http://repo.percona.com/apt",
  release  => "trusty",
  repos    => "main",
  key      => {
    "id"     => "430BDF5C56E7C94E848EE60C1C4CBDCDCD2EFD2A",
    "server" => "keys.gnupg.net",
  },
  include  => {
    "src" => false,
    "deb" => true,
  },
  notify   => Exec["/usr/bin/apt-get -qq update"],
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

class { createDatabase:
  require => Apt::Source["percona.trusty"],
}

## Ensure $user is in the mysql group
group { 'mysql':
  ensure           => present,
  members          => [$user],
  require          => Apt::Source["percona.trusty"],
}
