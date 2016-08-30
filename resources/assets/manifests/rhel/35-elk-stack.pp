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

$_kibanaConfig = "# kibana.conf - log viewer
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
"

##------------------------------------------------------------------------------
## Classes
##------------------------------------------------------------------------------

class installElasticsearch( $root ) {
  ##  Only install if requested
  if ( false == str2bool($dc_es_exists) ) {
    ##  Java
    java::oracle { 'jdk8' :
      ensure  => 'present',
      version => '8',
      java_se => 'jdk',
    }

    ##  Elasticsearch
    exec { "install-elasticsearch-key":
      unless  => 'service elasticsearch status',
      command => "sudo rpm --import http://packages.elastic.co/GPG-KEY-elasticsearch",
      cwd     => $root,
      require => Java::Oracle['jdk8'],
    }->
    exec { "install-elasticsearch-repo":
      unless  => 'service elasticsearch status',
      command => "echo '[elasticsearch-2.x]
name=Elasticsearch repository for 2.x packages
baseurl=http://packages.elastic.co/elasticsearch/2.x/centos
gpgcheck=1
gpgkey=http://packages.elastic.co/GPG-KEY-elasticsearch
enabled=1
' | sudo tee /etc/yum.repos.d/elasticsearch.repo",
      cwd     => $root,
      require => Java::Oracle['jdk8'],
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
/*class installLogstash( $root ) {
  exec { "install-logstash-key":
    unless  => 'service logstash status',
    command => "wget -qO - https://packages.elasticsearch.org/GPG-KEY-elasticsearch | sudo apt-key add - ",
    cwd     => $root,
  }->
  exec { "install-logstash-repo":
    unless  => 'service logstash status',
    command => "echo 'deb http://packages.elasticsearch.org/logstash/$install_version_logstash/debian stable main' | sudo tee -a /etc/apt/sources.list.d/logstash.list",
    cwd     => $root,
  }->
  exec { "install-logstash":
    unless  => 'service logstash status',
    command => "sudo apt-get -qq update && sudo apt-get -y install logstash",
    cwd     => $root,
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
    require => Exec['install-logstash'],
  }

  ##  Cluster configuration
  file { '/etc/logstash/conf.d/100-dfe-cluster.conf':
    ensure  => file,
    content => $_logstashConfig,
    notify  => Service['logstash'],
    require => Exec['install-logstash'],
  }
}


## Download and install Kibana
class installKibana( $root ) {
  ##
  ##  Kibana (v4.3.x not available on PPA as of 2015-11-30 hence the tarball)
  ##
  exec { "download-kibana":
    cwd     => "$root/_releases/kibana",
    command => "wget https://download.elastic.co/kibana/kibana/kibana-${install_version_kibana}-linux-x64.tar.gz",
    creates => "$root/_releases/kibana/kibana-${install_version_kibana}-linux-x64.tar.gz",
  }

  exec { "install-kibana":
    user        => $www_user,
    group       => $group,
    cwd         => "$root/_releases/kibana",
    command     => "tar xzf kibana-${install_version_kibana}-linux-x64.tar.gz",
    environment => ["HOME=/home/${user}"],
    require     => Exec["download-kibana"],
  }->
  file { "$root/kibana":
    ensure => link,
    target => "$root/_releases/kibana/kibana-${install_version_kibana}-linux-x64",
  }

  ##  Create a service definition
  file { '/etc/init/kibana.conf':
    ensure  => file,
    content => $_kibanaConfig,
    require => Exec['install-kibana'],
  }->
    ##  Kibana service
  exec { 'start-kibana':
    command     => "sudo service kibana start",
    cwd         => $root,
  }
}*/

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
  }
  /*class { installLogstash:
    root => $root,
  }->
  class { installKibana:
    root => $root,
  }*/
}

##  Install ELK stack if requested
if ( false == str2bool($dfe_update) ) {
  class { elk:
    root   => $elk_stack_root,
    #notify => Service['logstash'],
  }
}
