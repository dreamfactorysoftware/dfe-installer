################################################################################
# DreamFactory Enterprise(tm) Installer Manifest
# (c) 2012-∞ by DreamFactory Software, Inc. All Rights Reserved.
#
# Installs MySQL/Percona server
################################################################################

##------------------------------------------------------------------------------
## Variables
##------------------------------------------------------------------------------

$_logstashConfig = "input {
  gelf {
    type => \"$dc_index_type\"
    port => $dc_port
  }
}

filter {
  if [content] != \"\" {
    if [type] == \"$dc_index_type\" {
       json {
         source => \"content\"
         target => \"payload\"
         remove_field => [ \"__dfUI\" ]
       }
    }
  }
}

output {
  elasticsearch {
    host => \"$dc_host\"
    cluster => \"$es_cluster\"
    protocol => \"http\"
  }
}
"

##------------------------------------------------------------------------------
## Defaults
##------------------------------------------------------------------------------

notify { 'announce-thyself':
  message => '[DFE] The Mighty ELK',
}

File { owner => 0, group => 0, mode => 0644, }

Exec { path => ['/usr/bin','/usr/sbin','/bin','/sbin'], }

# ensure local apt cache index is up to date before beginning
exec { 'apt-get update':
  command => '/usr/bin/apt-get update'
}

##  ELK stack installer
class elk( $root ) {

  file { [
    $root,
    "$_installRoot/_releases",
    "$root/_releases/kibana",
    "$root/_releases/logstash",
  ]:
    ensure  => directory,
    owner   => $www_user,
    group   => $group,
    mode    => 2755,
    recurse => true,
  }

  ##  Java
  exec { "install-java8":
    command => "add-apt-repository -y ppa:webupd8team/java && sudo apt-get update && echo debconf shared/accepted-oracle-license-v1-1 select true | sudo debconf-set-selections && echo debconf shared/accepted-oracle-license-v1-1 seen true | sudo debconf-set-selections && sudo apt-get -y install oracle-java8-installer",
    cwd     => $root,
  }

  ##  Elasticsearch
  exec { "install-elasticsearch":
    unless  => 'service elasticsearch status',
    command => "wget -qO - https://packages.elastic.co/GPG-KEY-elasticsearch | sudo apt-key add - && echo 'deb http://packages.elastic.co/elasticsearch/2.x/debian stable main' | sudo tee -a /etc/apt/sources.list.d/elasticsearch.list && sudo apt-get -qq update && sudo apt-get -yq install elasticsearch",
    cwd     => $root,
  }

  # restart elasticsearch service
  service { "elasticsearch":
    ensure  => running,
    require => [
      Exec['install-elasticsearch'],
    ],
  }

  ##  Kibana
  exec { "download-kibana4":
    cwd     => "$root/_releases/kibana",
    command => "wget https://download.elastic.co/kibana/kibana/kibana-4.2.0-linux-x64.tar.gz",
    creates => "$root/_releases/kibana/kibana-4.2.0-linux-x64.tar.gz",
  }

  exec { "install-kibana4":
    # unless => 'service logstash status',
    cwd     => "$root/_releases/kibana",
    user    => $www_user,
    group   => $group,
    command => "tar xzf kibana-4.2.0-linux-x64.tar.gz",
    require => Exec["download-kibana4"],
  }->
  file { "$root/kibana":
    ensure => link,
    target => "$root/_releases/kibana/kibana-4.2.0-linux-x64",
  }

  ##  Logstash
  exec { "download-logstash":
    cwd     => "$root/_releases/logstash",
    command => "wget https://download.elastic.co/logstash/logstash/packages/debian/logstash_2.0.0-1_all.deb",
    creates => "$root/_releases/logstash/logstash_2.0.0-1_all.deb",
  }

  exec { "install-logstash":
    # unless => 'service logstash status',
    cwd     => "$root/_releases/logstash",
    user    => $www_user,
    group   => $group,
    command => "dpkg -i logstash_2.0.0-1_all.deb",
    require => Exec['download-logstash'],
    unless  => 'service logstash status',
  }

  # restart logstash service
  service { "logstash":
    ensure  => running,
    require => [
      Exec['install-logstash'],
    ],
  }

  ##  Cluster configuration
  file { '/etc/logstash/conf.d/100-dfe-cluster.conf':
    ensure  => file,
    content => $_logstashConfig,
    require => Exec['install-logstash'],
    notify  => Service['logstash'],
  }

}

class { elk:
  root => '/opt/elk',
}
