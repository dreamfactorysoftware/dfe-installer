################################################################################
# DreamFactory Enterprise(tm) Installer Manifest
# (c) 2012-2112 by DreamFactory Software, Inc. All Rights Reserved.
#
# Installs MySQL/Percona server
################################################################################

notify { 'announce-thyself': message => '[DFE] The Mighty ELK', }
File { owner => 0, group => 0, mode => '0644', }
Exec { path => ['/usr/bin','/usr/sbin','/bin','/sbin'], }

##------------------------------------------------------------------------------
## Variables
##------------------------------------------------------------------------------

if true == str2bool($dfe_update)  {
  $_kibanaCommand = 'restart'
} else {
  $_kibanaCommand = 'start'
}

$_esConfig ="ES_USER=\"elasticsearch\"
ES_GROUP=\"elasticsearch\"
ES_MIN_MEM=\"256m\"
ES_MAX_MEM=\"2g\"
ES_JAVA_OPTS=\"-Djava.net.preferIPv4Stack=true\"

export ES_MIN_MEM ES_MAX_MEM
"

$_logstashConfig = "input {
  gelf {
    type => \"${dc_index_type}\"
    port => $dc_port
  }
}

filter {
  if [content] != \"\" {
    if [type] == \"${dc_index_type}\" {
       json {
         source => \"content\"
         target => \"payload\"
         remove_field => [ \"__dfUI\" ]
       }
    }
  }
}

output {
    elasticsearch { hosts => [\"${dc_host}:${dc_es_port}\"] }
    stdout { codec => rubydebug }
}
"

/*$_kibanaConfig = "# kibana.conf - log viewer
description \"Kibana logstash viewer\"

start on virtual-filesystems
stop on runlevel [06]

respawn
respawn limit 5 30
limit nofile 65550 65550

# Environment
env HOME=${elk_stack_root}/kibana
chdir ${elk_stack_root}/kibana
setuid ${www_user}
setgid ${group}
console log

# Run Kibana, which is in ${elk_stack_root}/kibana
script
    bin/kibana
end script
"*/

##------------------------------------------------------------------------------
## Classes
##------------------------------------------------------------------------------

class { 'java': }

class installElasticsearch( $root ) {
  ##  Only install if requested
  if ( false == str2bool($dc_es_exists) ) {

    ##  Elasticsearch
    exec { "install-elasticsearch-key":
      unless  => 'service elasticsearch status',
      command => "sudo rpm --import http://packages.elastic.co/GPG-KEY-elasticsearch",
      cwd     => $root,
      require => Class['java'],
    }->
    exec { "install-elasticsearch-repo":
      unless  => 'sudo service elasticsearch status',
      command => "echo '[elasticsearch-2.x]
name=Elasticsearch repository for 2.x packages
baseurl=http://packages.elastic.co/elasticsearch/2.x/centos
gpgcheck=1
gpgkey=http://packages.elastic.co/GPG-KEY-elasticsearch
enabled=1
' | sudo tee /etc/yum.repos.d/elasticsearch.repo",
      cwd     => $root,
      require => Class['java'],
    }->
    package { 'elasticsearch':
      ensure  => 'present'
    }->
    file { '/etc/default/elasticsearch':
      ensure  => file,
      content => $_esConfig,
    }->
    exec { "install-elasticsearch-plugins":
      user    => root,
      unless => 'sudo /usr/share/elasticsearch/bin/plugin list | grep hq',
      command => "sudo /usr/share/elasticsearch/bin/plugin install royrusso/elasticsearch-HQ",
      cwd     => '/usr/share/elasticsearch/bin',
    }

    # elasticsearch service
    service { "elasticsearch":
      ensure  => running,
      enable  => true,
      require => Exec['install-elasticsearch-plugins'],
    }
  }
}

##  Logstash installer
class installLogstash( $root ) {
  exec { "install-logstash-repo":
    unless  => 'sudo service logstash status',
    command => "echo '[logstash-2.2]
name=logstash repository for 2.2 packages
baseurl=http://packages.elasticsearch.org/logstash/2.2/centos
gpgcheck=1
gpgkey=http://packages.elasticsearch.org/GPG-KEY-elasticsearch
enabled=1' | sudo tee /etc/yum.repos.d/logstash.repo",
    cwd     => $root,
    require => Class['java'],
  }->
  package { 'logstash':
    ensure  => 'present'
  }->
  file { '/etc/default/logstash':
    ensure  => file,
    require => Package['logstash'],
  }->
  file_line { 'logstash-force-ipv4':
    path   => '/etc/default/logstash',
    line   => 'LS_JAVA_OPTS="-Djava.net.preferIPv4Stack=true"',
    match  => ".*LS_JAVA_OPTS.*",
  }

  # logstash service
  service { "logstash":
    ensure  => running,
    enable  => true,
    require => Package['logstash'],
  }

  ##  Cluster configuration
  file { '/etc/logstash/conf.d/100-dfe-cluster.conf':
    ensure  => file,
    content => $_logstashConfig,
    notify  => Service['logstash'],
    require => Package['logstash'],
  }
}


## Download and install Kibana
class installKibana( $root ) {
  ##
  ##  Kibana (v4.3.x not available on PPA as of 2015-11-30 hence the tarball)
  ##
  exec { "install-kibana-repo":
    unless  => 'sudo service logstash status',
    command => "echo '[kibana-4.4]
name=Kibana repository for 4.4.x packages
baseurl=http://packages.elastic.co/kibana/4.4/centos
gpgcheck=1
gpgkey=http://packages.elastic.co/GPG-KEY-elasticsearch
enabled=1' | sudo tee /etc/yum.repos.d/kibana.repo",
    cwd     => $root,
    require => Class['java'],
  }->
  package { 'kibana':
    ensure  => 'present'
  }->

  # Kibana service
  service { "kibana":
    ensure  => running,
    enable  => true,
    require => Package['kibana'],
  }

}

##  ELK stack installer
class elk( $root ) {
  file { [
    $root,
    "$root/_releases",
    "$root/_releases/kibana",
  ]:
    ensure  => directory,
    owner   => $www_user,
    group   => $group,
    mode    => 2755,
  }->
  class { installElasticsearch:
    root => $root,
  }->
  class { installLogstash:
    root => $root,
  }
  class { installKibana:
    root => $root,
  }
}

##  Install ELK stack if requested
if ( false == str2bool($dfe_update) ) {
  class { elk:
    root   => $elk_stack_root,
    notify => Service['logstash'],
  }
}
