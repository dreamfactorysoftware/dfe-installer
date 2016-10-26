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

## Ensure $user is in the mysql group
group { 'mysql':
  ensure    => present,
  members   => [$user],
  require   => Yumrepo['percona'],
}
package { "Percona-Server-server-${percona_version}":
  ensure  => present
}->
file_line { 'mysql.add-mysqld-block':
  ensure  => present,
  notify  => Service['mysql'],
  path  => "/etc/my.cnf",
  line => '[mysqld]',
}->
file_line { 'mysql.skip-grant-tables':
  ensure  => present,
  notify  => Service['mysql'],
  path  => "/etc/my.cnf",
  line => 'skip-grant-tables',
}->
ini_setting { 'alter-password-policy-absent':
  ensure => absent,
  path   => '/etc/my.cnf',
  key_val_separator => '=',
  section => 'mysqld',
  setting => 'validate_password_policy',
  value => 'LOW'
}->
service { "mysql-ensure-restart":
  name    => 'mysql',
  ensure  => 'running',
  enable  => true,
  require => File_line['mysql.skip-grant-tables']
}->
exec { "mysql_root_user":
  command => "/usr/bin/mysql mysql --execute=\"UPDATE user SET authentication_string = PASSWORD('$mysql_root_pwd') WHERE user = 'root';\""
}->
file_line { 'mysql_remove_skip':
  ensure => absent,
  path  => "/etc/my.cnf",
  line => 'skip-grant-tables',
}->
ini_setting { "user":
  ensure  => present,
  path  => "/etc/mytemp.cnf",
  key_val_separator => '=',
  section => 'client',
  setting => 'user',
  value   => 'root'
}->
ini_setting { "password":
  ensure  => present,
  path  => "/etc/mytemp.cnf",
  key_val_separator => '=',
  section => 'client',
  setting => 'password',
  value   => $mysql_root_pwd
}->
ini_setting { "host":
  ensure  => present,
  path  => "/etc/mytemp.cnf",
  key_val_separator => '=',
  section => 'client',
  setting => 'host',
  value   => 'localhost'
}->
exec { "mysql-restart":
  command => "sudo service mysql restart",
  require => File_line['mysql_remove_skip']
}->
#Need to create this temporary file to reset the MySQL password, else no other commands can happen
file { 'add_tmp_sql':
  path => "$pwd/tmp.sql",
  ensure  => 'present',
  content => "SET PASSWORD = PASSWORD('$mysql_root_pwd'); uninstall plugin validate_password",
}->
  #This is due to the restart we have to reset this pw before executing any other statements
exec { "reset_root_pw":
  command => "/usr/bin/mysql --defaults-extra-file=/etc/mytemp.cnf --connect-expired-password < tmp.sql"
}->
exec { "create_dfe_database":
  command => "/usr/bin/mysql --defaults-extra-file=/etc/mytemp.cnf --execute=\"CREATE DATABASE IF NOT EXISTS $db_name CHARACTER SET utf8 COLLATE utf8_general_ci;\""
}->
exec { "create_df_database":
  command => "/usr/bin/mysql --defaults-extra-file=/etc/mytemp.cnf --execute=\"CREATE DATABASE IF NOT EXISTS dreamfactory CHARACTER SET utf8 COLLATE utf8_general_ci;\""
}->
exec { "create_dfe_user":
  command => "/usr/bin/mysql --defaults-extra-file=/etc/mytemp.cnf --execute=\"CREATE USER IF NOT EXISTS '$db_user'@'$db_host' IDENTIFIED BY '$db_pwd';\""
}->
exec { "grant_dfe_user":
  command => "/usr/bin/mysql --defaults-extra-file=/etc/mytemp.cnf --execute=\"GRANT ALL PRIVILEGES ON *.* TO '$db_user'@'$db_host' WITH GRANT OPTION;\""
}->
exec {'import mysql':
  command => "/usr/bin/mysql -u$db_user -p$db_pwd -D $db_name < $pwd/resources/assets/sql/dfe_local.schema.sql",
}->
#put back the plugin for re-installs
exec {'reinstall plugin':
  command => "/usr/bin/mysql -u$db_user -p$db_pwd -D $db_name -e\"INSTALL PLUGIN validate_password SONAME 'validate_password.so';\""
}->
ini_setting { 'alter-password-policy':
  ensure => 'present',
  path   => '/etc/my.cnf',
  key_val_separator => '=',
  section => 'mysqld',
  setting => 'validate_password_policy',
  value => 'LOW'
}->
exec { "mysql-restart-post":
  command => "sudo service mysql restart",
  require => Ini_setting['alter-password-policy']
}



