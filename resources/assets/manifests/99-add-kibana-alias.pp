################################################################################
# DreamFactory Enterprise(tm) Installer Manifest
# (c) 2012-∞ by DreamFactory Software, Inc. All Rights Reserved.
#
# Add the .dfekibana alias
################################################################################

notify { 'announce-thyself': message => '[DFE] Kickstart ELK', }
Exec { cwd => $root, path => ['/usr/bin','/usr/sbin','/bin','/sbin'], }

## Add .dfekibana alias
exec { 'restart-elasticsearch':
  command => 'sudo service elasticsearch restart',
}->
exec { 'restart-kibana':
  command => 'sudo service kibana restart',
}->
exec { 'add-dfekibana-alias':
  command => 'curl -XPOST \'http://localhost:9200/_aliases\' -d \'{"actions":[{"add":{"index":".kibana","alias":".dfekibana"}}]}\'',
}
