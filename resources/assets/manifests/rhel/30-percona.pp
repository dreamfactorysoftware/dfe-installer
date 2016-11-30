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

  yumrepo { 'percona':
    descr    => 'CentOS $releasever - Percona',
    baseurl  => 'http://repo.percona.com/centos/$releasever/os/$basearch/',
    gpgkey   => 'http://www.percona.com/downloads/percona-release/RPM-GPG-KEY-percona',
    enabled  => 1,
    gpgcheck => 1,
  }->

    ## Ensure $user is in the mysql group
  group { 'mysql':
    ensure    => present,
    members   => [$user, $log_user],
    require   => Yumrepo['percona'],
  }->
  package { "Percona-Server-server-${percona_version}":
    ensure  => present
  }->
  file_line { 'mysql.add-mysqld-block':
    ensure  => present,
    path    => "/etc/my.cnf",
    line    => '[mysqld]',
  }->
  file_line { 'mysql.skip-grant-tables':
    ensure  => present,
    path    => "/etc/my.cnf",
    line    => 'skip-grant-tables',
  }->
  ini_setting { 'alter-password-policy-absent':
    ensure            => 'absent',
    path              => '/etc/my.cnf',
    key_val_separator => '=',
    section           => 'mysqld',
    setting           => 'validate_password_policy',
    value             => 'LOW'
  }->
  exec { "mysql-restart1":
    command => "sudo service mysql restart",
    require => File_line['mysql.skip-grant-tables']
  }->
  exec { "mysql_root_user":
    command => "sudo /usr/bin/mysql mysql --execute=\"UPDATE user SET authentication_string = PASSWORD('$mysql_root_pwd') WHERE user = 'root';\""
  }->
  file_line { 'mysql_remove_skip':
    ensure => absent,
    path   => "/etc/my.cnf",
    line   => 'skip-grant-tables',
  }->
  ini_setting { "user":
    ensure            => present,
    path              => "/etc/mytemp.cnf",
    key_val_separator => '=',
    section           => 'client',
    setting           => 'user',
    value             => 'root'
  }->
  ini_setting { "password":
    ensure            => present,
    path              => "/etc/mytemp.cnf",
    key_val_separator => '=',
    section           => 'client',
    setting           => 'password',
    value             => $mysql_root_pwd
  }->
  ini_setting { "host":
    ensure            => present,
    path              => "/etc/mytemp.cnf",
    key_val_separator => '=',
    section           => 'client',
    setting           => 'host',
    value             => 'localhost'
  }->
  ini_setting { 'alter-password-policy-present':
    ensure            => 'present',
    path              => '/etc/my.cnf',
    key_val_separator => '=',
    section           => 'mysqld',
    setting           => 'validate_password_policy',
    value             => 'LOW'
  }->
  exec { "mysql-restart":
    command => "sudo service mysql restart",
    require => File_line['mysql_remove_skip']
  }->
    #Need to create this temporary file to reset the MySQL password, else no other commands can happen
  file { 'add_tmp_sql':
    path    => "$pwd/tmp.sql",
    ensure  => 'present',
    content => "SET PASSWORD = PASSWORD('$mysql_root_pwd');",
  }->
    #This is due to the restart we have to reset this pw before executing any other statements
  exec { "reset_root_pw":
    command => "/usr/bin/mysql --defaults-extra-file=/etc/mytemp.cnf --connect-expired-password < tmp.sql"
  }->
  exec { "create_dfe_database":
    command => "sudo /usr/bin/mysql -u root --password=$mysql_root_pwd --execute=\"CREATE DATABASE IF NOT EXISTS $db_name CHARACTER SET utf8 COLLATE utf8_general_ci;\""
  }->
  exec { "create_df_database":
    command => "sudo /usr/bin/mysql -u root --password=$mysql_root_pwd --execute=\"CREATE DATABASE IF NOT EXISTS dreamfactory CHARACTER SET utf8 COLLATE utf8_general_ci;\""
  }->
  exec { "create_dfe_user":
    command => "sudo /usr/bin/mysql -u root --password=$mysql_root_pwd --execute=\"CREATE USER IF NOT EXISTS '$db_user'@'$db_host' IDENTIFIED BY '$db_pwd';\""
  }->
  exec { "grant_dfe_user":
    command => "sudo /usr/bin/mysql -u root --password=$mysql_root_pwd --execute=\"GRANT ALL PRIVILEGES ON *.* TO '$db_user'@'$db_host' WITH GRANT OPTION;\""
  }

} #end if not installing to own location

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
    require => Exec["grant_dfe_user"]
  }->
  exec { "artisan_run_migrations":
    command     => "$artisan setup:db",
    require => Exec["grant_dfe_user"]
  }
} else {
  exec { "artisan_clear_config":
    command     => "$artisan config:clear",
  }->
  exec { "artisan_run_migrations":
    command     => "$artisan setup:db",
  }
}

