################################################################################
# DreamFactory Enterprise(tm) Installer Manifest
# (c) 2012-∞ by DreamFactory Software, Inc. All Rights Reserved.
#
# Installs MySQL/Percona server
################################################################################

notify { 'announce-thyself': message => '[DFE] The Mighty ELK', }
File { owner => 0, group => 0, mode => '0644', }
Exec { path => ['/usr/bin','/usr/sbin','/bin','/sbin'], }

##------------------------------------------------------------------------------
## Variables
##------------------------------------------------------------------------------

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
    if ( false == str2bool($dfe_update) ) {
      ##  Java
      exec { "install-java8":
        command => "add-apt-repository -y ppa:webupd8team/java && echo debconf shared/accepted-oracle-license-v1-1 select true | sudo debconf-set-selections && echo debconf shared/accepted-oracle-license-v1-1 seen true | sudo debconf-set-selections && sudo apt-get -qq update && sudo apt-get -y install oracle-java8-installer",
        cwd     => $root,
      }

      ##  Elasticsearch
      exec { "install-elasticsearch":
        unless  => 'service elasticsearch status',
        command => "wget -qO - https://packages.elastic.co/GPG-KEY-elasticsearch | sudo apt-key add - && echo 'deb http://packages.elastic.co/elasticsearch/2.x/debian stable main' | sudo tee -a /etc/apt/sources.list.d/elasticsearch.list && sudo apt-get -qq update && sudo apt-get -y install elasticsearch",
        cwd     => $root,
        require => Exec['install-java8'],
      }

      # elasticsearch service
      service { "elasticsearch":
        ensure  => running,
        enable  => true,
        require => Exec['install-elasticsearch'],
      }
    }

    file_line { 'elasticsearch-force-ipv4':
      path   => '/etc/default/elasticsearch',
      line   => 'ES_JAVA_OPTS="-Djava.net.preferIPv4Stack=true"',
      match  => ".*ES_JAVA_OPTS.*",
    }
  }
}

##  Logstash installer
class installLogstash( $root ) {
  ##  Logstash
  exec { "install-logstash":
    unless  => 'service logstash status',
    command => "wget -qO - https://packages.elasticsearch.org/GPG-KEY-elasticsearch | sudo apt-key add - && echo 'deb http://packages.elasticsearch.org/logstash/2.0/debian stable main' | sudo tee -a /etc/apt/sources.list.d/logstash.list && sudo apt-get -qq update && sudo apt-get -y install logstash",
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
  ##  Kibana (v4.2.x not available on PPA as of 2015-11-03 hence the tarball)
  ##
  exec { "download-kibana":
    cwd     => "$root/_releases/kibana",
    command => "wget https://download.elastic.co/kibana/kibana/kibana-4.2.0-linux-x64.tar.gz",
    creates => "$root/_releases/kibana/kibana-4.2.0-linux-x64.tar.gz",
  }

  exec { "install-kibana":
    user        => $www_user,
    group       => $group,
    cwd         => "$root/_releases/kibana",
    command     => "tar xzf kibana-4.2.0-linux-x64.tar.gz",
    environment => ["HOME=/home/${user}"],
    require     => Exec["download-kibana"],
  }->
  file { "$root/kibana":
    ensure => link,
    target => "$root/_releases/kibana/kibana-4.2.0-linux-x64",
  }

  ##  Create a service definition
  file { '/etc/init/kibana.conf':
    ensure  => file,
    content => $_kibanaConfig,
    require => Exec['install-kibana'],
  }->
    ##  Kibana service
  exec { 'restart-kibana':
    user        => root,
    command     => 'sudo service kibana restart',
    cwd         => $root,
    environment => ["HOME=/home/${user}"]
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
  }->
  class { installKibana:
    root => $root,
  }
}

##  Install ELK stack if requested
class { elk:
  root   => $elk_stack_root,
  notify => Service['logstash'],
}
