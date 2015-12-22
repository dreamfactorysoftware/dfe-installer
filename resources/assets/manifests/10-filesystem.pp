################################################################################
# DreamFactory Enterprise(tm) Installer Manifest
# (c) 2012-2112 by DreamFactory Software, Inc. All Rights Reserved.
#
# Ensures the necessary directory structure is in place
################################################################################

notify { 'announce-thyself': message => '[DFE] Creating required directory structure(s)', }

############
## Classes
############

class createRequiredDirectories {
  ## Create the root directories for the repos to be installed
  file { [
    $doc_root_base_path,
    $release_path,
    $console_release,
    $dashboard_release,
    $instance_release,
  ]:
    ensure => directory,
    owner  => $user,
    group  => $www_group,
    mode   => '2775',
  }

  ## Create all directories under the $mount_point
  file { [
    $mount_point,
    $blueprint_path,
    $capsule_path,
    $log_path,
    $storage_path,
    $trash_path,
    $blueprint_log_path,
    $capsule_log_path,
    "$log_path/console",
    "$log_path/dashboard",
    "$log_path/hosted",
    "$log_path/instance",
  ]:
    ensure  => directory,
    owner   => $www_user,
    group   => $group,
    mode    => '2775',
  }
}

class createRequiredFiles {
  file { [
    ## And our indicator files
    "$doc_root_base_path/.dfe-managed",
  ]:
    ensure => present,
    owner  => $www_user,
    group  => $group,
    mode   => '0660',
  }
}

############
## Logic
############

## Creates the directories & files
class {
  createRequiredDirectories:
}->
class {
  createRequiredFiles:
}
