vcsrepo { "$release_path/dreamfactory/$dsp_branch":
  ensure   => present,
  provider => git,
  source   => $instance_repo,
  user     => $user,
  owner    => $user,
  group    => $www_group,
  revision => $dsp_version
}->
file { "$doc_root_base_path/launchpad":
  ensure => link,
  target => "$release_path/dreamfactory/$dsp_branch",
}->
file { "$doc_root_base_path/launchpad/.env":
  ensure => present,
  owner  => $user,
  group  => $www_group,
  mode   => 0775,
  source => "$doc_root_base_path/launchpad/.env-dist"
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
  command     => "$composer_bin update",
  user        => $user,
  provider    => 'shell',
  cwd         => '/var/www/launchpad',
  environment => ["HOME=/home/$user"]
}->
file { [
  "$release_path/dreamfactory/$dsp_branch/bootstrap/cache",
  "$release_path/dreamfactory/$dsp_branch/storage",
  "$release_path/dreamfactory/$dsp_branch/storage/logs",
  "$release_path/dreamfactory/$dsp_branch/storage/framework",
  "$release_path/dreamfactory/$dsp_branch/storage/framework/db",
  "$release_path/dreamfactory/$dsp_branch/storage/framework/sessions",
  "$release_path/dreamfactory/$dsp_branch/storage/framework/views"]:
  ensure => directory,
  owner  => $user,
  group  => $www_group,
  mode   => 2775,
}
