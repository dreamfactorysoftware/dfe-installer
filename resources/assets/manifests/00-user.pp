# Setup the user and the ssh keys

group { $group:
  ensure => present
}->
group { $storage_group:
  ensure  => present,
  members => [$user],
}->
user { $user:
  ensure     => present,
  expiry     => absent,
  home       => "/home/$user",
  groups     => [$www_group, $storage_group],
  managehome => true,
  password   => pw_hash($user_pwd, 'sha-512', 'HVQeSnVR'),
  shell      => '/bin/bash',
  gid        => $group,
}->
user_ssh_pubkey { "${user}/ssh-rsa@console.${vendor_id}.${domain}":
  bits   => 2048,
  target => "/home/${user}/.ssh/id_rsa",
  type   => 'rsa',
  user   => $user
}->
exec { 'add_public_key_to_authorized_keys':
  command  => "cat /home/$user/.ssh/id_rsa.pub >> /home/$user/.ssh/authorized_keys",
  provider => 'shell',
  user     => $user
}->file_line { 'sudo_rule':
  path => '/etc/sudoers',
  line => "$user  ALL=(ALL) NOPASSWD:ALL",
}->
file { "/home/$user/.gitconfig":
  ensure => present,
  owner  => $user,
  group  => $group,
  mode   => 0664,
  source => "$pwd/resources/assets/git/gitconfig",
}->
file { "/home/$user/.ssh/known_hosts":
  ensure => file,
  owner  => $user,
  group  => $group,
  mode   => 0600,
}->
file { "/home/$user/.ssh/authorized_keys":
  ensure  => file,
  owner   => $user,
  group   => $group,
  mode    => 0600,
}->
#file { "/home/$user/.composer":
#  ensure => directory,
#  owner  => $user,
#  group  => $group,
#  mode   => 2775,
#}->
#file { "/home/$user/.composer/auth.json":
#  ensure => present,
#  owner  => $user,
#  group  => $group,
#  mode   => 0600,
#  source => "$pwd/.composer/auth.json",
#}->
#file { "/home/$user/.composer/config.json":
#  ensure => present,
#  owner  => $user,
#  group  => $group,
#  mode   => 0600,
#  source => "$pwd/.composer/config.json",
#}->
exec { 'add_github_to_known_hosts':
  command  => "/usr/bin/ssh-keyscan -H github.com >> /home/$user/.ssh/known_hosts",
  provider => 'shell',
  user     => $user,
}
