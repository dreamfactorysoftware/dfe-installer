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
    'php70w-pdo_dblib',
    'mongodb',
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
  /*file { "/etc/php5/fpm/conf.d/20-mongodb.ini":
    ensure => 'link',
    target => '../../mods-available/mongodb.ini'
  }->
  file { "/etc/php5/cli/conf.d/20-mongodb.ini":
    ensure => 'link',
    target => '../../mods-available/mongodb.ini'
  }->*/
  ini_setting { "pm.max_children":
    ensure  => present,
    path    => '/etc/php-fpm.d/www.conf',
    key_val_separator => '=',
    section => 'www',
    setting => 'pm.max_children',
    value   => '10'
  }

}

##------------------------------------------------------------------------------
## Logic
##------------------------------------------------------------------------------

## Make this go first
class { updatePackages:
  stage => 'pre',
}
/*
class { postfix:
  service_enable => true,
  service_ensure => running,
}->
exec { 'enable-mcrypt-settings':
  command  => "$php_enmod_bin mcrypt",
  provider => posix
}-> */
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

