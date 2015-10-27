################################################################################
# DreamFactory Enterprise(tm) Installer Manifest
# (c) 2012-∞ by DreamFactory Software, Inc. All Rights Reserved.
#
# Ensures the necessary directory structure is in place
################################################################################

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
    mode   => 2775,
  }

## Create all directories under the $mount_point
  file { [
    $mount_point,
    "$mount_point/trash",
    $storage_path,
    $log_path,
    "$log_path/console",
    "$log_path/dashboard",
    "$log_path/hosted",
  ]:
    ensure  => directory,
    owner   => $www_user,
    group   => $group,
    mode    => 2775,
  }

}

############
## Logic
############

class { createRequiredDirectories:
## Creates the directories
}
file { [
## And our indicator files
  "$doc_root_base_path/.dfe-managed",
  "$doc_root_base_path/.maintenace-mode.off",
]:
  ensure => present,
  owner  => $www_user,
  group  => $group,
  mode   => 0660,
}
