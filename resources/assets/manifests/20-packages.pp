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
    'mongodb',
    'zip',
    'memcached',
    'redis-server',
    'git',
    'curl',
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
    "php-mongo"
  ]

  ## If SMTP is local, then install postfix
  if ($smtp_host == "localhost") or ($smtp_host == "127.0.0.1") or ($smtp_host == "127.0.1.1") {
    $_requiredPackages = union($_basePackages, [$preferred_mail_package])
  } else {
    $_requiredPackages = $_basePackages
  }

  package { $_requiredPackages:
    ensure  => latest
  }->
  package { $_removePackages:
    ensure  => absent
  }->
    ## Install/update Composer
  exec { 'install-composer':
    command     => "/usr/bin/curl -sS https://getcomposer.org/installer | /usr/bin/php",
    creates     => "$pwd/composer.phar",
    provider    => shell,
    cwd         => $root,
    environment => ["HOME=/home/$user"],
    require     => Package['curl']
  }->
  exec { 'move-installed-composer':
    command => "mv composer.phar $composer_bin; chmod a+x $composer_bin",
    creates => $composer_bin,
  }

}

class installPHP {
  $_php = [
    'php7.0',
    'php7.0-common',
    'php7.0-fpm',
    'php7.0-mysql',
    'php7.0-pgsql',
    'php7.0-ldap',
    'php7.0-memcached',
    'php7.0-sqlite',
    'php7.0-dev',
    'php7.0-mcrypt',
    'php7.0-curl',
    'php7.0-sybase',
  ]

  package { $_php:
    ensure  => 'installed'
  }

  ##Install updated MongoDB driver for PHP
  exec { "pecl install mongodb":
    command => "pecl install mongodb",
    unless => 'pecl info mongodb'
  }->

  # Make sure mongodb ini file is updated / Create symlinks
  file { "/etc/php/7.0/mods-available/mongodb.ini":
    content => 'extension=/usr/lib/php/20121212/mongodb.so',
    require => Exec["pecl install mongodb"]
  }->
  file { "/etc/php/7.0/fpm/conf.d/20-mongodb.ini":
    ensure => 'link',
    target => '../../mods-available/mongodb.ini'
  }->
  file { "/etc/php/7.0/cli/conf.d/20-mongodb.ini":
    ensure => 'link',
    target => '../../mods-available/mongodb.ini'
  }->
  ini_setting { "pm.max_children":
    ensure  => present,
    path    => '/etc/php/7.0/fpm/pool.d/www.conf',
    key_val_separator => '=',
    section => 'www',
    setting => 'pm.max_children',
    value   => '10'
  }
}

##------------------------------------------------------------------------------
## Logic
##------------------------------------------------------------------------------
##  Keep up-to-date
class { 'apt':
  update => {
    frequency => 'always'
  }
}

apt::ppa { 'ppa:ondrej/php':}

## Make this go first
class { updatePackages:
  stage => 'pre',
}->

class { installPHP: }

class { postfix:
  service_enable => true,
  service_ensure => running,
}->
/*exec { 'enable-mcrypt-settings':
  command  => "$php_enmod_bin mcrypt",
  provider => posix
}->*/
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
