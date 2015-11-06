################################################################################
# DreamFactory Enterprise(tm) Installer Manifest
# (c) 2012-∞ by DreamFactory Software, Inc. All Rights Reserved.
#
# users, groups, sudo
################################################################################

notify { 'announce-thyself': message => '[DFE] Creating required users and groups', }
stage { 'pre': before => Stage['main'], }

############
## Classes
############

Exec { path => ['/usr/bin','/usr/sbin','/bin','/sbin'], }

## Create and seed $root/.gitconfig and git auth for composer
class configureGitAuth( $root, $token ) {
  file { [
    $root,
    "$root/.composer",
  ]:
    ensure => directory,
    owner  => $user,
    group  => $group,
  }->
  file { "$root/.gitconfig":
    ensure => present,
    owner  => $user,
    group  => $group,
    source => "$pwd/resources/assets/git/gitconfig",
  }->
  file { "$root/.composer/auth.json":
    ensure  => file,
    owner   => $user,
    group   => $group,
    mode    => '640',
    content => "{\"github-oauth\": {\"github.com\": \"$token\"}}",
  }
}

##  Updates /etc/hosts
class updateHostsFile( $hostname, $ip = '127.0.1.1' ) {
  ## The host aliases we want
  $_hostAliases = [
    "console",
    "console.local",
    "dashboard",
    "dashboard.local",
    "kibana",
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
    file { "$root/.ssh":
      ensure => directory,
      owner  => $user,
      group  => $group,
      mode   => '700',
    }->
      #      ## Create private key for user
      #    user_ssh_pubkey { "${user}/ssh-rsa@console.${vendor_id}.${domain}":
      #      bits   => 4096,
      #      target => "$root/.ssh/id_dsa",
      #      type   => 'dsa',
      #      user   => $user,
      #    }->
    file { "$root/.ssh/authorized_keys":
      ensure => file,
      owner  => $user,
      group  => $group,
      mode   => '400',
      source => "/home/${log_user}/.ssh/authorized_keys",
    }->
    exec { 'add-public-rsa-key-to-authorized-keys':
      user        => $user,
      command     => "cat $root/.ssh/id_rsa.pub >> $root/.ssh/authorized_keys",
      onlyif      => "test -f $root/.ssh/id_rsa.pub",
      cwd         => $root,
      environment => ["HOME=/home/$user"]
    }->
    exec { 'add-public-dss-key-to-authorized-keys':
      user        => $user,
      command     => "cat $root/.ssh/id_dsa.pub >> $root/.ssh/authorized_keys",
      onlyif      => "test -f $root/.ssh/id_dsa.pub",
      cwd         => $root,
      environment => ["HOME=/home/$user"]
    }

    file_line { 'add-user-to-sudoers':
      path => '/etc/sudoers',
      line => "$user  ALL=(ALL) NOPASSWD:ALL",
    }
  }

  class { configureGitAuth:
    root    => "/home/$user",
    token   => $token,
    require => User[$user],
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
class { updateHostsFile:
  hostname => $install_hostname,
}

## Create user and git auth
class { createAdminUser:
  root  => "/home/$user",
  token => $gh_token,
}
