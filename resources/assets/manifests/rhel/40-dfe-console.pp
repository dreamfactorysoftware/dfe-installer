################################################################################
# DreamFactory Enterprise(tm) Installer Manifest
# (c) 2012-2112 by DreamFactory Software, Inc. All Rights Reserved.
#
# Install dreamfactory/dfe-console
################################################################################

notify { 'announce-thyself': message => '[DFE] Install/update console software', }
Exec { path => ['/usr/bin','/usr/sbin','/bin','/sbin'], }

$db_server_config = "'{\"port\": 3306,\"username\": \"${db_user}\",\"password\": \"${db_pwd}\",\"database\": \"${db_name}\",\"driver\": \"${db_driver}\",\"default-database-name\": \"\",\"charset\": \"utf8\",\"collation\": \"utf8_unicode_ci\",\"prefix\": \"\",\"multi-assign\": \"on\"}'"

############
## Classes
############

## Defines the console .env settings. Relies on FACTER_* data
class iniSettings( $root, $zone, $domain, $protocol = "https") {
## Define our stuff
  $_env = { "path" => "$root/.env", }
  $_consoleUrl = "$protocol://console.${zone}.${domain}"
  $_dashboardUrl = "$protocol://dashboard.${zone}.${domain}"
  $_consoleApiUrl = "$_consoleUrl/api/v1/ops"

  $_settings = {
    "" => {
      "APP_DEBUG"                   => $app_debug,
      "APP_URL"                     => $_consoleUrl,
      "DB_HOST"                     => $db_host,
      "DB_DATABASE"                 => $db_name,
      "DB_USERNAME"                 => $db_user,
      "DB_PASSWORD"                 => $db_pwd,
      "DFE_CLUSTER_ID"              => "cluster-${zone}",
      "DFE_DEFAULT_CLUSTER"         => "cluster-${zone}",
      "DFE_DEFAULT_DATABASE"        => "db-${zone}",
      "DFE_SCRIPT_USER"             => $user,
      "DFE_DEFAULT_DNS_ZONE"        => $zone,
      "DFE_DEFAULT_DNS_DOMAIN"      => $domain,
      "DFE_DEFAULT_DOMAIN"          => "${zone}.${domain}",
      "DFE_DEFAULT_DOMAIN_PROTOCOL" => $default_protocol,
      "DFE_STATIC_ZONE_NAME"        => $static_zone_name,
      "SMTP_DRIVER"                 => "smtp",
      "SMTP_HOST"                   => $smtp_host,
      "SMTP_PORT"                   => $smtp_port,
      "MAIL_FROM_ADDRESS"           => $mail_from_address,
      "MAIL_FROM_NAME"              => $mail_from_name,
      "MAIL_USERNAME"               => $mail_username,
      "MAIL_PASSWORD"               => $mail_password,
      "DFE_HOSTED_BASE_PATH"        => $storage_path,
      "DFE_SNAPSHOT_TRASH_PATH"     => $trash_path,
      "DFE_DASHBOARD_URL"           => $_dashboardUrl,
      "DFE_SUPPORT_EMAIL_ADDRESS"   => $support_email_address,
      "DFE_CONSOLE_API_URL"         => $_consoleApiUrl,
      "DFE_AUDIT_HOST"              => $dc_host,
      "DFE_AUDIT_PORT"              => $dc_port,
      "DFE_AUDIT_CLIENT_HOST"       => $dc_client_host,
      "DFE_AUDIT_CLIENT_PORT"       => $dc_client_port,
      "DFE_CAPSULE_PATH"            => $capsule_path,
      "DFE_CAPSULE_LOG_PATH"        => $capsule_log_path,
    }
  }

## Update the .env file
  create_ini_settings($_settings, $_env)
}

## Defines the console .env settings. Relies on FACTER_* data
class customIniSettings( $root, $zone, $domain, $protocol = "https") {
  ## Define our stuff
  $_env = { "path" => "$root/.env", }

  ## Custom CSS file
  if ( '' != $custom_css_file ) {
    $_customCss = {
      "" => {
        "DFE_CUSTOM_CSS_FILE"         => "/css/$custom_css_file",
      }
    }

    create_ini_settings($_customCss, $_env)
  }

  ## The navbar image
  if ( '' != $navbar_image ) {
    $_navbarImage = {
      "" => {
        "DFE_NAVBAR_IMAGE"         => "/img/$navbar_image",
      }
    }

    create_ini_settings($_navbarImage, $_env)
  }

  ## The login_splashPP image
  if ( '' != $login_splash_image ) {
    $_loginSplashImage = {
      "" => {
        "DFE_LOGIN_SPLASH_IMAGE"         => "/img/$login_splash_image",
      }
    }

    create_ini_settings($_loginSplashImage, $_env)
  }
}

##  Initial set up
class setupApp( $root ) {
  if ( false == str2bool($dfe_update) ) {
    exec { "generate-app-key":
      command     => "$artisan key:generate",
      user        => $user,
      provider    => shell,
      cwd         => $root,
      environment => ["HOME=/home/$user"]
    }->
    exec { "run-setup-command":
      command     => "$artisan dfe:setup --force --admin-password=\"${admin_pwd}\" \"${admin_email}\"",
      user        => $user,
      provider    => shell,
      cwd         => $root,
      environment => ["HOME=/home/$user"]
    }->
    exec { "append-api-keys":
      command         => "cat $console_root/database/dfe/console.env >> $root/.env",
      user            => root,
      onlyif          => "test -f $console_root/database/dfe/console.env",
    }->
    exec { "append-customs":
      command         => "cat $console_root/storage/customs.env >> $root/.env",
      user            => root,
      onlyif          => "test -f $console_root/storage/customs.env",
    }->
    file { "$doc_root_base_path/.dfe.cluster.json":
      ensure => present,
      owner  => $user,
      group  => $www_group,
      mode   => 0640,
      source => "$console_root/database/dfe/.dfe.cluster.json"
    }->
    class { createInitialCluster:
      root => $root,
    }
  }
}

##  Creates the initial default cluster
class createInitialCluster( $root ) {
##  Only on new installs
  if ( true == str2bool($dfe_update) ) {
    exec { "composer-update":
      command     => "composer update",
      user        => $user,
      provider    => shell,
      cwd         => $console_root,
      timeout     => 1800,
      environment => [ "HOME=/home/$user", ]
    }
  } else {
    exec { "create-web-server":
      command     => "$artisan dfe:server create web-${vendor_id} -t web -a ${vendor_id}.${domain} -m ${default_local_mount_name} -c {}",
      user        => $user,
      provider    => shell,
      cwd         => $root,
    }->
    exec { "create-app-server":
      command     => "$artisan dfe:server create app-${vendor_id} -t app -a ${vendor_id}.${domain} -m ${default_local_mount_name} -c {}",
      user        => $user,
      provider    => shell,
      cwd         => $root,
      environment => ["HOME=/home/$user"]
    }->
    exec { "create-db-server":
      command     => "$artisan dfe:server create db-${vendor_id} -t db -a ${vendor_id}.${domain} -m ${default_local_mount_name} -c ${db_server_config}",
      user        => $user,
      provider    => shell,
      cwd         => $root,
      environment => ["HOME=/home/$user"]
    }->
    exec { "create-cluster":
      command     => "$artisan dfe:cluster create cluster-${vendor_id} --subdomain ${vendor_id}.${domain}",
      user        => $user,
      provider    => shell,
      cwd         => $root,
      environment => ["HOME=/home/$user"]
    }->
    exec { "assign-cluster-web-server":
      command     => "$artisan dfe:cluster add cluster-${vendor_id} --server-id web-${vendor_id}",
      user        => $user,
      provider    => shell,
      cwd         => $root,
      environment => ["HOME=/home/$user"]
    }->
    exec { "assign-cluster-app-server":
      command     => "$artisan dfe:cluster add cluster-${vendor_id} --server-id app-${vendor_id}",
      user        => $user,
      provider    => shell,
      cwd         => $root,
      environment => ["HOME=/home/$user"]
    }->
    exec { "assign-cluster-db-server":
      command     => "$artisan dfe:cluster add cluster-${vendor_id} --server-id db-${vendor_id}",
      user        => $user,
      provider    => shell,
      cwd         => $root,
      environment => ["HOME=/home/$user"]
    }->
    exec { "composer-update":
      command     => "$composer_bin update",
      user        => $user,
      provider    => shell,
      cwd         => $console_root,
      timeout     => 1800,
      environment => [ "HOME=/home/$user", ]
    }
  }
}

class laravelDirectories( $root, $owner, $group, $mode = '2775') {
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
    owner  => $owner,
    group  => $group,
    mode   => $mode,
  }

## Blow away cached files on an update
  if ( true == str2bool($dfe_update) ) {
    exec { "remove-services-json":
      command         => "rm -f $root/bootstrap/cache/services.json",
      user            => root,
      onlyif          => "test -f $root/bootstrap/cache/services.json",
    }->
    exec { "remove-compiled-classes":
      command         => "rm -f $root/bootstrap/cache/compiled.php",
      user            => root,
      onlyif          => "test -f $root/bootstrap/cache/compiled.php",
    }
  }
}

## Checks directory/file permissions
class checkPermissions( $root, $dir_mode = '2775', $file_mode = '0664' ) {
  exec { 'chown-and-pwn':
    user            => root,
    command         => "chown -R ${www_user}:${group} ${root}/storage/ ${root}/bootstrap/cache/",
    cwd             => $root,
    environment     => ["HOME=/home/${user}"]
  }->
  exec { 'chmod-storage':
    user            => root,
    command         => "find ${root}/storage -type d -exec chmod ${dir_mode} {} \\;",
    onlyif          => "test -d ${root}/storage",
    cwd             => $root,
    environment     => ["HOME=/home/${user}"]
  }->
  exec { 'chmod-storage-files':
    user            => root,
    command         => "find ${root}/storage -type f -exec chmod ${file_mode} {} \\;",
    onlyif          => "test -d ${root}/storage",
    cwd             => $root,
    environment     => ["HOME=/home/${user}"]
  }->
  exec { "check-bootstrap-cache":
    user            => root,
    command         => "chmod ${file_mode} ${root}/bootstrap/cache/* && chown ${www_user}:${group} ${root}/bootstrap/cache/*",
    onlyif          => "test -f ${root}/bootstrap/cache/compiled.php",
    cwd             => $root,
  }->
  exec { "check-storage-log-file":
    user            => root,
    command         => "chmod ${file_mode} ${root}/storage/logs/*.log && chown ${www_user}:${group} ${root}/storage/logs/*.log",
    onlyif          => "test -f $root/storage/logs/laravel.log",
    cwd             => $root,
  }
}

##  Create an environment file
class createEnvFile( $root, $source = ".env-dist" ) {
##  On new installs only
  if ( false == str2bool($dfe_update) ) {
    file { "${root}/.env":
      ensure => present,
      owner  => $user,
      group  => $www_group,
      mode   => 0640,
      source => "${root}/${source}",
    }
  }
}

##  Customize the application
class customizeApp( $root ) {
  if '' != $custom_css_file_source {
    file { "$root/public/css/$custom_css_file":
      ensure => file,
      owner  => $user,
      group  => $www_group,
      mode   => 0640,
      source => $custom_css_file_source,
    }
  }

  if '' != $login_splash_image_source {
    file { "$root/public/img/$login_splash_image":
      ensure => file,
      owner  => $user,
      group  => $www_group,
      mode   => 0640,
      source => $login_splash_image_source,
    }
  }

  if '' != $navbar_image_source {
    file { "$root/public/img/$navbar_image":
      ensure => file,
      owner  => $user,
      group  => $www_group,
      mode   => 0640,
      source => $navbar_image_source,
    }
  }
}

############
## Logic
############

vcsrepo { "$console_release/$console_branch":
  ensure   => latest,
  provider => git,
  source   => $console_repo,
  user     => $user,
  owner    => $user,
  group    => $www_group,
  revision => $console_branch,
}->
file { $console_root:
  ensure => link,
  target => "$console_release/$console_branch",
}->
class { createEnvFile:
  root => $console_root,
}->
class { iniSettings:
## Applies INI settings in $_settings to .env
  root     => $console_root,
  zone     => $vendor_id,
  domain   => $domain,
  protocol => $default_protocol,
}->
class { customIniSettings:
## Applies custom INI settings in $_settings to .env
  root     => $console_root,
  zone     => $vendor_id,
  domain   => $domain,
  protocol => $default_protocol,
}->
class { laravelDirectories:
  root    => $console_root,
  owner   => $www_user,
  group   => $group,
}->
exec { "composer-console-update":
  command     => "composer update",
  user        => $user,
  provider    => shell,
  cwd         => $console_root,
  timeout     => 2000,
  environment => [ "HOME=/home/$user", ]
}->
class { setupApp:
  root => $console_root,
}->
class { customizeApp:
  root => $console_root,
}->
class { checkPermissions:
  root => $console_root,
}
