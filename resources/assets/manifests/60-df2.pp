vcsrepo { "/var/www/_releases/dreamfactory/$dsp_location":
  ensure   => present,
  provider => git,
#  source   => "https://${github_user_info}github.com/dreamfactorysoftware/dfe-dashboard.git",
  source   => "git@github.com:dreamfactorysoftware/dreamfactory.git",
  user     => $user,
  owner    => $group,
  group    => $www_group,
  revision => $dsp_version
}->
file { '/var/www/launchpad':
  ensure => link,
  target => "/var/www/_releases/dreamfactory/$dsp_location",
}->
file { '/var/www/launchpad/.env':
  ensure => present,
  owner  => $user,
  group  => $www_group,
  mode   => '0775',
  source => '/var/www/launchpad/.env-dist'
}->
ini_setting { 'SMTP_DRIVER':
  ensure  => present,
  path    => '/var/www/launchpad/.env',
  section => '',
  setting => 'MAIL_DRIVER',
  value   => "smtp"
}->
ini_setting { 'SMTP_HOST':
  ensure  => present,
  path    => '/var/www/launchpad/.env',
  section => '',
  setting => 'MAIL_HOST',
  value   => "${smtp_host}"
}->
ini_setting { 'SMTP_PORT':
  ensure  => present,
  path    => '/var/www/launchpad/.env',
  section => '',
  setting => 'MAIL_PORT',
  value   => "${smtp_port}"
}->
ini_setting { 'MAIL_USERNAME':
  ensure  => present,
  path    => '/var/www/launchpad/.env',
  section => '',
  setting => 'MAIL_USERNAME',
  value   => "${mail_username}"
}->
ini_setting { 'MAIL_PASSWORD':
  ensure  => present,
  path    => '/var/www/launchpad/.env',
  section => '',
  setting => 'MAIL_PASSWORD',
  value   => "${mail_password}"
}->
exec { 'launchpad-config':
  command     => '/usr/local/bin/composer update',
  user        => $user,
  provider    => 'shell',
  cwd         => '/var/www/launchpad',
  environment => ["HOME=/home/$user"]
}->
file { "/var/www/_releases/dreamfactory/$dsp_location/storage":
  ensure => directory,
  owner  => $user,
  group  => $www_group,
  mode   => "2775"
}->
file { "/var/www/_releases/dreamfactory/$dsp_location/storage/framework":
  ensure => directory,
  owner  => $user,
  group  => $www_group,
  mode   => "2775"
}->
file { "/var/www/_releases/dreamfactory/$dsp_location/storage/logs":
  ensure => directory,
  owner  => $user,
  group  => $www_group,
  mode   => "2775"
}->
file { "/var/www/_releases/dreamfactory/$dsp_location/storage/bootstrap/cache":
  ensure => directory,
  owner  => $user,
  group  => $www_group,
  mode   => "2775"
}->
file { "/var/www/_releases/dreamfactory/$dsp_location/storage/framework/sessions":
  ensure => directory,
  owner  => $user,
  group  => $www_group,
  mode   => "2775"
}->
file { "/var/www/_releases/dreamfactory/$dsp_location/storage/framework/views":
  ensure => directory,
  owner  => $user,
  group  => $www_group,
  mode   => "2775"
}
