################################################################################
# DreamFactory Enterprise(tm) Installer Manifest
# (c) 2012-2112 by DreamFactory Software, Inc. All Rights Reserved.
#
# Installs all required packages
################################################################################

notify { 'announce-thyself': message => '[DFE] Updating system packages', }
Exec { path => ['/usr/bin','/usr/sbin','/bin','/sbin'], }
stage { 'pre': before => Stage['main'], }

##------------------------------------------------------------------------------
## Classes
##------------------------------------------------------------------------------

class updatePackages {
  $_basePackages = [
    'epel-release',
    'nginx',
    'redis',
    'php70w',
    'php70w-common',
    'php70w-fpm',
    'php70w-mysqlnd',
    'php70w-pecl-redis',
    'php70w-pgsql',
    'php70w-ldap',
    'php70w-opcache',
    'php70w-pdo',
    'php70w-devel',
    'php70w-pear',
    'php70w-mcrypt',
    'php70w-mbstring',
    'php70w-pdo_dblib',
    'php70w-soap',
    'mongodb',
    'composer',
    'zip',
    'memcached',
    'git',
    'curl',
    'sqlite',
    'autoconf',
    'gcc-c++',
    'make',
    'openssl',
    'openssl-devel',
    'libcurl',
    'libcurl-devel',
    'pcre',
    'pcre-devel',
    'pkgconfig',
  ]

  $_removePackages = [
    "apache2",
    "apache2-bin",
    "apache2-data",
    "libapache2-mod-php5",
    "mysql-server",
    "mysql-client",
    "mysql-common"
  ]

  ## If SMTP is local, then install postfix
  if ($smtp_host == "localhost") or ($smtp_host == "127.0.0.1") or ($smtp_host == "127.0.1.1") {
    $_requiredPackages = union($_basePackages, ['exim'])
  } else {
    $_requiredPackages = $_basePackages
  }

  package { $_requiredPackages:
    ensure  => latest
  }->
  package { $_removePackages:
    ensure  => absent
  }->
  ##Install updated MongoDB driver for PHP
  exec { "pecl install mongodb":
    command => "pecl install mongodb",
    unless => 'pecl info mongodb'
  }->

  # The PECL install above seems to assure this in Centos/RHEL. Leave for now to be sure.
  file { "/etc/php.d/mongodb.ini":
    content => 'extension=/usr/lib64/php/modules/mongodb.so',
    require => Exec["pecl install mongodb"]
  }->
  ini_setting { "pm.max_children":
    ensure  => present,
    path    => '/etc/php-fpm.d/www.conf',
    key_val_separator => '=',
    section => 'www',
    setting => 'pm.max_children',
    value   => '50'
  }

}

##------------------------------------------------------------------------------
## Logic
##------------------------------------------------------------------------------

## Make this go first
class { updatePackages:
  stage => 'pre',
}
group { $www_group:
  ensure  => present,
  members => [$user]
}->
group { "mongodb":
  ensure  => present,
  members => [$user]
}->
group { $group:
  ensure  => present,
  members => [$user, $www_user],
}

