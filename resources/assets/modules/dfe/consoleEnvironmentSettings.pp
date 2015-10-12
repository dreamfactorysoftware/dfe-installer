################################################################################
# DreamFactory Enterprise(tm) Installer Manifest
# (c) 2012-∞ by DreamFactory Software, Inc. All Rights Reserved.
#
# class consoleEnvironmentSettings
################################################################################

############
## Classes
############

## Defines the console .env settings. Relies on FACTER_* data
class consoleEnvironmentSettings( $root, $zone, $domain, $protocol = 'https') {
## Define our stuff
  $_env = { 'path' => "$root/.env", }

  $_appUrl = "$protocol://console.${zone}.${domain}"

  $_settings = {
    '' => {
      'APP_DEBUG'                                  => $app_debug,
      'APP_URL'                                    => $_appUrl,
      'DB_HOST'                                    => $db_host,
      'DB_DATABASE'                                => $db_name,
      'DB_USERNAME'                                => $db_user,
      'DB_PASSWORD'                                => $db_pwd,
      'DFE_CLUSTER_ID'                             => "cluster-${zone}",
      'DFE_DEFAULT_CLUSTER'                        => "cluster-${zone}",
      'DFE_DEFAULT_DATABASE'                       => "db-${zone}",
      'DFE_SCRIPT_USER'                            => $user,
      'DFE_DEFAULT_DNS_ZONE'                       => $zone,
      'DFE_DEFAULT_DNS_DOMAIN'                     => $domain,
      'DFE_DEFAULT_DOMAIN'                         => "${zone}.${domain}",
      'DFE_DEFAULT_DOMAIN_PROTOCOL'                => $default_protocol,
      'DFE_STATIC_ZONE_NAME'                       => $static_zone_name,
      'SMTP_DRIVER'                                => 'smtp',
      'SMTP_HOST'                                  => $smtp_host,
      'SMTP_PORT'                                  => $smtp_port,
      'MAIL_FROM_ADDRESS'                          => $mail_from_address,
      'MAIL_FROM_NAME'                             => $mail_from_name,
      'MAIL_USERNAME'                              => $mail_username,
      'MAIL_PASSWORD'                              => $mail_password,
      'DFE_HOSTED_BASE_PATH'                       => $storage_path,
      'DFE_CONSOLE_API_URL'                        => "$_appUrl/api/v1/ops",
    }
  }

## Update the .env file
  create_ini_settings($_settings, $_env)
}

