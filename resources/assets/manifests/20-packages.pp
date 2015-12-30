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
    'php5',
    'php5-fpm',
    'php5-mysql',
    'php5-redis',
    'php5-pgsql',
    'php5-mongo',
    'php5-ldap',
    'php5-memcached',
    'php5-sqlite',
    'php5-dev',
    'php5-mcrypt',
    'php5-curl',
    'php5-mssql',
    'mongodb',
    'zip',
    'memcached',
    'redis-server',
    'git',
    'openssl',
    'curl',
    'sqlite3',
    'apt-file',
    'apt-utils',
    'software-properties-common',
  ]

  $_removePackages = [
#    "apache2",
#    "apache2-bin",
#    "apache2-data",
#    "libapache2-mod-php5",
  ]

  ## If SMTP is local, then install postfix
  if ($smtp_host == "localhost") or ($smtp_host == "127.0.0.1") or ($smtp_host == "127.0.1.1") {
    $_requiredPackages = union($_basePackages, [$preferred_mail_package])
  } else {
    $_requiredPackages = $_basePackages
  }

  package { $_requiredPackages:
    ensure  => latest
#  }->
#  package { $_removePackages:
#    ensure  => absent
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
}

exec { 'enable-mcrypt-settings':
  command  => "$php_enmod_bin mcrypt",
  provider => posix
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

## Install/update Composer
exec { 'install-composer':
  command => "/usr/bin/curl -sS https://getcomposer.org/installer | php; mv composer.phar $composer_bin; chmod a+x $composer_bin",
  creates => $composer_bin,
  require => Package['curl']
}
