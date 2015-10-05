/********************************************************************
* MySQL/Percona specific items
********************************************************************/

# Always do an apt-get update
class { 'apt' :
  update => {
    frequency => 'always'
  }
}

exec { "apt-update":
  command => "/usr/bin/apt-get update"
}

apt::source { 'percona.trusty':
  comment  => 'Repo for percona db server',
  location => 'http://repo.percona.com/apt',
  release  => 'trusty',
  repos    => 'main',
  key      => {
    'id' => '430BDF5C56E7C94E848EE60C1C4CBDCDCD2EFD2A',
    'server' => 'keys.gnupg.net'
  },
  include  => {
    'src' => true,
    'deb' => true
  },
  notify   => Exec['apt-update']
}

class { 'mysql::server':
  root_password    => $mysql_root_pwd,
  override_options => {
    'mysqld' => {
      'bind-address' => '0.0.0.0',
    #      'user' => $mysql_user,
    #      'log-bin' => $mysql_log_bin_path,
    #      'log-error' => "$mysql_log_path/error.log",
    #      'slow-query-log-file' => "$mysql_log_path/slow.log"
    }
  },
  restart          => true,
  package_name     => "percona-server-server-${percona_version}",
  require          => Apt::Source['percona.trusty']
}

file { "/etc/mysql/conf.d/dreamfactory.my.cnf":
  source => "$pwd/resources/assets/etc/mysql/dreamfactory.my.cnf",
  ensure => present
}

class { 'mysql::client':
  package_name => "percona-server-client-${percona_version}",
  require      => Apt::Source['percona.trusty']
}

mysql::db { $db_name:
  ensure   => present,
  charset  => 'utf8',
  host     => $db_host,
  user     => $db_user,
  password => $db_pwd,
  sql      => "$pwd/resources/assets/sql/dfe_local.schema.sql"
}

mysql_grant { "${db_user}@${db_host}/*.*":
  ensure     => present,
  options    => ['GRANT'],
  privileges => ['ALL'],
  table      => '*.*',
  user       => "${db_user}@${db_host}",
  require    => Mysql::Db[$db_name]
}

group { 'mysql':
  ensure  => present,
  members => [$user]
}