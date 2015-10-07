$_requiredPackages = [
  'nginx-extras',
  'php5',
  'php5-fpm',
  'php5-mysql',
  'php5-redis',
  'php5-pgsql',
  'php5-mongo',
  'php5-ldap',
  'php5-memcached',
  'php5-dev',
  'php5-mcrypt',
  'php5-curl',
  'php5-mssql',
  'mongodb',
  'fcgiwrap',
  'zip',
  'memcached',
  'redis-server',
  'git-core',
  'git-all',
  'openssl',
  'curl',
]

$_removePackages = [
  "apache2",
  "apache2-bin",
  "apache2-data",
  "libapache2-mod-php5",
]

## If SMTP is local, then install postfix
if ($smtp_host == "localhost") or ($smtp_host == "127.0.0.1") or ($smtp_host == "127.0.1.1") {
  $_requiredPackages = union($_requiredPackages, [$preferred_mail_package])
}

## Install/remove required packages
package { $_requiredPackages:
  ensure  => latest
}->
package { $_removePackages:
  ensure => absent
}->
exec { 'enable_mcrypt_settings':
  command  => '/usr/sbin/php5enmod mcrypt',
  provider => posix
}->
group { 'www-data':
  ensure  => present,
  members => [$user]
}->
group { 'mongodb':
  ensure  => present,
  members => [$user]
}->
group { $storage_group:
  ensure  => present,
  members => [$user, $www_user],
}

## Install Composer
exec { 'Install Composer':
  command => "/usr/bin/curl -sS https://getcomposer.org/installer | php; mv composer.phar $composer_bin; chmod a+x $composer_bin",
  creates => $composer_bin,
  require => Package['curl']
}
