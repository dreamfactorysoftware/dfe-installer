# Helper classes for the dfe-installer
# === Authors
# Jerry Ablan <jerryablan@dreamfactory.com>
# === Copyright
# Copyright 2015 DreamFactory Software, Inc. All Rights Reserved.

class laravel {

  ##------------------------------------------------------------------------------
  ## Ensure the base directory structure and permissions
  ##------------------------------------------------------------------------------

  class ensureStructure( $root, $user, $www_group, $owner, $group, $mode = 2775) {

    file { [
      "$root/bootstrap",
    ]:
      ensure => directory,
      owner  => $user,
      group  => $www_group,
      mode   => $mode,
    }->
    file { [
      "$root/bootstrap/cache",
      "$root/storage",
      "$root/storage/framework",
      "$root/storage/framework/sessions",
      "$root/storage/framework/views",
      "$root/storage/logs",
    ]:
      ensure => directory,
      owner  => $www_user,
      group  => $group,
      mode   => $mode,
    }

  }

  ##------------------------------------------------------------------------------
  ## Reset the permissions on directories in a laravel app
  ##------------------------------------------------------------------------------

  class resetPermissions( $root, $user, $www_user, $group ) {

    exec { 'chmod-storage':
      command     => "find $root/storage -type d -exec chmod 2775 {} \\;",
      provider    => shell,
      cwd         => $root,
      environment => ["HOME=/home/$user"]
    }->
    exec { 'chmod-storage-files':
      command     => "find $root/storage -type f -exec chmod 0664 {} \\;",
      provider    => shell,
      cwd         => $root,
      environment => ["HOME=/home/$user"]
    }

    exec { 'chmod-temp':
      command     => "find /tmp/.df-log -type d -exec chmod 2775 {} \\;",
      provider    => shell,
      cwd         => $root,
      environment => ["HOME=/home/$user"]
    }->
    exec { 'chmod-temp-files':
      command     => "find /tmp/.df-log -type f -exec chmod 0664 {} \\;",
      provider    => shell,
      cwd         => $root,
      environment => ["HOME=/home/$user"]
    }

    exec { "check-services-json":
      command => "/bin/true",
      onlyif  => "/usr/bin/test -e $root/bootstrap/cache/services.json",
    }

    exec { "check-log-file":
      command => "/bin/true",
      onlyif  => "/usr/bin/test -e $root/storage/logs/laravel.log",
    }

    file { "$root/bootstrap/cache/services.json":
      ensure  => present,
      owner   => $www_user,
      group   => $group,
      mode    => 0664,
      require => Exec["check-services-json"],
    }->
    file { "$root/storage/logs/laravel.log":
      ensure  => present,
      owner   => $www_user,
      group   => $group,
      mode    => 0664,
      require => Exec["check-log-file"],
    }

  }

  ##------------------------------------------------------------------------------
  ## Update the .env variables
  ##------------------------------------------------------------------------------

  class updateEnvironment( $root, $settings) {

    ## Update the .env file
    $_env = { "path" => "$root/.env", }
    create_ini_settings($settings, $_env)

  }

}
