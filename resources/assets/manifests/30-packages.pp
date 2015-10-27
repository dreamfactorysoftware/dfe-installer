################################################################################
# DreamFactory Enterprise(tm) Installer Manifest
# (c) 2012-∞ by DreamFactory Software, Inc. All Rights Reserved.
#
# Installs all required packages
################################################################################

notify { 'announce-thyself':
  message => '[DFE] Updating system packages',
}

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
]

$_removePackages = [
  "apache2",
  "apache2-bin",
  "apache2-data",
  "libapache2-mod-php5",
]

## If SMTP is local, then install postfix
if ($smtp_host == "localhost") or ($smtp_host == "127.0.0.1") or ($smtp_host == "127.0.1.1") {
  $_requiredPackages = union($_basePackages, [$preferred_mail_package])
} else {
  $_requiredPackages = $_basePackages
}

##------------------------------------------------------------------------------
## Logic
##------------------------------------------------------------------------------

package { $_requiredPackages:
  ensure  => latest
}->
package { $_removePackages:
  ensure => absent
}->
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
}->
file_line { 'update-exim-config-type':
  path   => '/etc/exim4/update-exim4.conf.conf',
  line   => "dc_eximconfig_configtype='internet'",
  match  => ".*dc_eximconfig_configtype.*",
}->
file_line { 'update-exim-other-host':
  path   => '/etc/exim4/update-exim4.conf.conf',
  line   => "dc_other_hostname='${vendor_id}.${domain}'",
  match  => ".*dc_other_hostname.*",
}->
exec { 'update-exim-config':
  command  => '/usr/sbin/update-exim4.conf',
  provider => posix,
}

## Install/update Composer
if ( false == str2bool($dfe_update) ) {
  exec { 'install-composer':
    command => "/usr/bin/curl -sS https://getcomposer.org/installer | php; mv composer.phar $composer_bin; chmod a+x $composer_bin",
    creates => $composer_bin,
    require => Package['curl']
  }
} else {
  exec { 'update-composer':
    command         => "$composer_bin self-update --quiet",
    onlyif          => "test -f $composer_bin",
    path            => ['/usr/bin','/usr/sbin','/bin','/sbin'],
  }
}
