################################################################################
# DreamFactory Enterprise(tm) Installer Manifest
# (c) 2012-∞ by DreamFactory Software, Inc. All Rights Reserved.
#
# users, groups, sudo
################################################################################

notify { 'announce-thyself': message => '[DFE] Creating required users and groups', }
stage { 'pre': before => Stage['main'], }
Exec { path => ['/usr/bin','/usr/sbin','/bin','/sbin'], }

############
## Classes
############

##  Updates /etc/hosts
class updateHostsFile( $hostname, $ip = '127.0.1.1' ) {
  ## The host aliases we want
  $_hostAliases = [
    "console.local",
    "dashboard.local",
    "kibana.local",
    "console.${vendor_id}.${domain}",
    "dashboard.${vendor_id}.${domain}",
    "kibana.${vendor_id}.${domain}",
    "${vendor_id}.${domain}",
  ]

  host { $hostname:
    ensure       => present,
    ip           => $ip,
    host_aliases => $_hostAliases,
  }
}

##  Create the DFE admin user
class createAdminUser( $root, $token ) {
  ## Only new installations get a user
  if ( false == str2bool($dfe_update) ) {
    group { $group:
      ensure => present,
    }->
    user { $user:
      ensure     => present,
      expiry     => absent,
      gid        => $group,
      groups     => [$www_group],
      home       => $root,
      managehome => true,
      password   => pw_hash($user_pwd, 'sha-512', 'HVQeSnVR'),
      shell      => "/bin/bash",
    }->
    file_line { 'add-dfe-aliases-to-bashrc':
      path => "$root/.bashrc",
      line => "
alias dir='ls -ahl'
alias cmpu='composer update'
alias lvcc='sudo rm -rf /tmp/.df-cache/ /var/www/console/storage/bootstrap/cache/* /var/www/dashboard/bootstrap/cache/* /var/www/launchpad/bootstrap/cache/*'
alias ngtr='sudo service php5-fpm stop ; sudo service nginx stop ; sudo service php5-fpm start ; sudo service nginx start'
"
    }->
    file { "$root/.composer":
      ensure => directory,
      owner  => $user,
      group  => $group,
    }->
    file { "$root/.composer/auth.json":
      ensure  => file,
      owner   => $user,
      group   => $group,
      mode    => '640',
      content => "{\"github-oauth\": {\"github.com\": \"$token\"}}",
    }->
    file { "$root/.ssh":
      ensure  => directory,
      owner   => $user,
      group   => $group,
      mode    => '700',
    }->
    file { "$root/.ssh/authorized_keys":
      ensure => file,
      owner  => $user,
      group  => $group,
      mode   => '400',
      source => "/home/${log_user}/.ssh/authorized_keys",
    }->
    file_line { 'add-user-to-sudoers':
      path    => '/etc/sudoers',
      line    => "$user  ALL=(ALL) NOPASSWD:ALL",
    }->
    file { "$root/.gitconfig":
      ensure => present,
      owner  => $user,
      group  => $group,
      source => "$pwd/resources/assets/git/gitconfig",
    }
  }
}

##------------------------------------------------------------------------------
## Logic
##------------------------------------------------------------------------------

##  Keep up-to-date
class { 'apt':
  stage  => 'pre',
  update => {
    frequency => 'daily',
  },
}

##  Update the hosts file
class { "updateHostsFile":
  hostname => $install_hostname,
}

## Create user and git auth
class { "createAdminUser":
  root    => "/home/$user",
  token   => $gh_token,
}
