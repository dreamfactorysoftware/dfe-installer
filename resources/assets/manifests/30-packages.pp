$base_packages = ['nginx-extras', 'php5', 'php5-fpm', 'php5-mysql', 'php5-redis', 'php5-pgsql', 'php5-mongo', 'php5-ldap', 'php5-memcached', 'php5-dev', 'php5-mcrypt', 'php5-curl', 'php5-mssql', 'mongodb', 'fcgiwrap', 'zip', 'memcached', 'redis-server', 'git-core' ,'git-all', 'openssl', 'curl']

if ($smtp_host == "localhost") or ($smtp_host == "127.0.0.1") or ($smtp_host == "127.0.1.1") {
  $packages = union($base_packages, ['postfix'])
} else {
  $packages = $base_packages
}

# Install required packages
package { $packages:
  ensure  => latest
}->
package { ["apache2", "apache2-bin", "apache2-data", "libapache2-mod-php5"]:
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

# Install Composer
exec { 'Install Composer':
  command => '/usr/bin/curl -sS https://getcomposer.org/installer | php; mv composer.phar /usr/local/bin/composer; chmod a+x /usr/local/bin/composer',
  creates => '/usr/local/bin/composer',
  require => Package['curl']
}
