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
  },
  databases => {
    "$db_name" => {
      ensure      => 'present',
      charset     => 'utf8',
    }
  },
  users => {
    "${db_user}@${db_host}" => {
      ensure        => 'present',
      password_hash => mysql_password("$db_pwd"),
    },
  },
  grants => {
    "${db_user}@${db_host}/*.*" => {
      ensure     => 'present',
      options    => ['GRANT'],
      privileges => ["ALL"],
      table      => '*.*',
      user       => "${db_user}@${db_host}",
    },
  }
}

exec {'import mysql':
  command => "/usr/bin/mysql -u$db_user -p$db_pwd -D $db_name < $pwd/resources/assets/sql/dfe_local.schema.sql",
}

## Ensure $user is in the mysql group
group { 'mysql':
  ensure    => present,
  members   => [$user],
  require   => Yumrepo['percona'],
}


# Dependencies definition
Yumrepo['percona']->
Class['mysql::server']



