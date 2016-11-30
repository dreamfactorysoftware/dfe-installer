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
    'nginx-extras',
    'php7.0',
    'php7.0-common',
    'php7.0-fpm',
    'php7.0-redis',
    'php7.0-pgsql',
    'php7.0-ldap',
    'php7.0-memcache',
    'php7.0-mongodb',
    'php7.0-sqlite3',
    'php7.0-dev',
    'php7.0-mcrypt',
    'php7.0-mysql',
    'php7.0-curl',
    'php7.0-sybase',
    'php7.0-xml',
    'php7.0-mbstring',
    'php7.0-soap',
    'php7.0-zip',
    'mongodb',
    'zip',
    'memcached',
    'redis-server',
    'git',
    'curl',
    'php5.6-curl',
    'sqlite3',
    'apt-file',
    'apt-utils',
    'software-properties-common',
    'autoconf',
    'g++',
    'make',
    'openssl',
    'libssl-dev',
    'libsasl2-dev',
    'libcurl4-openssl-dev',
    'libpcre3-dev',
    'pkg-config',
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
    $_requiredPackages = union($_basePackages, [$preferred_mail_package])
  } else {
    $_requiredPackages = $_basePackages
  }
  package { $_removePackages:
    ensure  => absent
  }->
  package { $_requiredPackages:
    ensure  => latest
  }->
  exec { 'install-composer':
    command => 'curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer',
  }

  ini_setting { "pm.max_children":
    ensure  => present,
    path    => '/etc/php/7.0/fpm/pool.d/www.conf',
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
}->
class { postfix:
  service_enable => true,
  service_ensure => running,
}->
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
