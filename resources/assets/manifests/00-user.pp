################################################################################
# DreamFactory Enterprise(tm) Installer Manifest
# (c) 2012-∞ by DreamFactory Software, Inc. All Rights Reserved.
#
# users, groups, sudo
################################################################################

## Only new installations
if ( false == str2bool($dfe_update) ) {

  ## The host aliases we want
  $_hostAliases = [
    "$install_hostname",
    "console",
    "console.local",
    "dashboard",
    "dashboard.local",
    "console.${vendor_id}.${domain}",
    "dashboard.${vendor_id}.${domain}",
    "${vendor_id}.${domain}",
  ]


  ############
  ## Classes
  ############

  ## Create $user and $group. Create private key for user
  group { $group:
    ensure => present
  }->
  user { $user:
    ensure     => present,
    expiry     => absent,
    home       => "/home/$user",
    groups     => [$www_group],
    managehome => true,
    password   => pw_hash($user_pwd, 'sha-512', 'HVQeSnVR'),
    shell      => "/bin/bash",
    gid        => $group,
  }->
  user_ssh_pubkey { "${user}/ssh-rsa@console.${vendor_id}.${domain}":
    bits   => 2048,
    target => "/home/${user}/.ssh/id_rsa",
    type   => 'rsa',
    user   => $user
  }->
  file { "/home/$user/.ssh":
    ensure => directory,
    owner  => $user,
    group  => $group,
    mode   => 0600,
  }->
  exec { 'add-public-key-to-authorized-keys':
    command  => "cat /home/$user/.ssh/id_rsa.pub >> /home/$user/.ssh/authorized_keys",
    user     => $user,
    provider => shell,
  }->
  exec { 'add-ubuntu-key-to-authorized-keys':
    command  => "cat /home/$log_user/.ssh/authorized_keys >> /home/$user/.ssh/authorized_keys",
    provider => shell,
  }->
  file { "/home/$user/.ssh/authorized_keys":
    ensure => present,
    owner  => $user,
    group  => $group,
    mode   => 0400,
  }->
  file_line { 'sudo-rule':
    path => '/etc/sudoers',
    line => "$user  ALL=(ALL) NOPASSWD:ALL",
  }->
  file_line { 'bashrc-aliases':
    path => "/home/$user/.bashrc",
    line => "
alias dir='ls -ahl'
alias lvcc='sudo rm -rf /tmp/.df-* /var/www/console/storage/bootstrap/cache/* /var/www/dashboard/bootstrap/cache/* /var/www/launchpad/bootstrap/cache/*'
alias ngtr='sudo service php5-fpm stop ; sudo service nginx stop ; sudo service php5-fpm start ; sudo service nginx start'
"
  }->
  host { "localhost":
    ensure       => present,
    ip           => "127.0.0.1",
    host_aliases => $_hostAliases
  }

  ## Create and seed /home/$user

  file { "/home/$user/.gitconfig":
    ensure => present,
    owner  => $user,
    group  => $group,
    mode   => 0664,
    source => "$pwd/resources/assets/git/gitconfig",
  }
  ## This isn't really necessary
  #->
  #file { "/home/$user/.ssh/known_hosts":
  #  ensure => present,
  #  owner  => $user,
  #  group  => $group,
  #  mode   => 0600,
  #}->
  #exec { 'add-github-to-known-hosts':
  #  command  => "/usr/bin/ssh-keyscan -H github.com >> /home/$user/.ssh/known_hosts",
  #  provider => shell,
  #  user     => $user,
  #}

  file { "/home/$user/.composer":
    ensure => directory,
    owner  => $user,
    group  => $group,
    mode   => 2775,
  }->
  file { "/home/$user/.composer/auth.json":
    ensure => present,
    owner  => $user,
    group  => $group,
    mode   => 0600,
    source => "$pwd/resources/assets/.composer/auth.json",
  }->
  file { "/home/$user/.composer/config.json":
    ensure => present,
    owner  => $user,
    group  => $group,
    mode   => 0600,
    source => "$pwd/resources/assets/.composer/config.json",
  }
}
