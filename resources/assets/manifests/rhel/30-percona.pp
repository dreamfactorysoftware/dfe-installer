################################################################################
# DreamFactory Enterprise(tm) Installer Manifest
# (c) 2012-2112 by DreamFactory Software, Inc. All Rights Reserved.
#
# Installs MySQL/Percona server
################################################################################

notify { 'announce-thyself': message => '[DFE] Install/update database software', }
Exec { path => ['/usr/bin','/usr/sbin','/bin','/sbin'], }

yumrepo { 'percona':
  descr    => 'CentOS $releasever - Percona',
  baseurl  => 'http://repo.percona.com/centos/$releasever/os/$basearch/',
  gpgkey   => 'http://www.percona.com/downloads/percona-release/RPM-GPG-KEY-percona',
  enabled  => 1,
  gpgcheck => 1,
}

class {'mysql::server':
  package_name     => 'Percona-Server-server-57',
  package_ensure   => '5.7.11-4.1.el7',
  service_name     => 'mysql',
  config_file      => '/etc/my.cnf',
  includedir       => '/etc/my.cnf.d',
  root_password    => $mysql_root_pwd,
  override_options => {
    mysqld => {
      log-error => '/var/log/mysqld.log',
      pid-file  => '/var/run/mysqld/mysqld.pid',
    },
    mysqld_safe => {
      log-error => '/var/log/mysqld.log',
    },
  }
}

package { 'mariadb-libs':
  ensure  => latest,
  require   => Yumrepo['percona'],
}

## Install database on non updates

if ( false == str2bool($dfe_update)) {
 mysql::db { $db_name:
    ensure    => 'present',
    charset   => "utf8",
    host      => $db_host,
    user      => $db_user,
    password  => $db_pwd,
    sql       => "$pwd/resources/assets/sql/dfe_local.schema.sql",
   require   => Yumrepo['percona'],
 }

  ## Grant access to the DFE app user
  mysql_grant { "${db_user}@${db_host}*/*.*":
    ensure     => present,
    options    => ["GRANT"],
    privileges => ["ALL"],
    table      => "*.*",
    user       => "${db_user}@${db_host}",
    require    => Mysql::Db[$db_name],
  }
}

## Ensure $user is in the mysql group
/*
group { 'mysql':
  ensure    => present,
  members   => [$user],
  require   => Yumrepo['percona'],
}
*/


# Dependencies definition
Yumrepo['percona']->
Class['mysql::server']



