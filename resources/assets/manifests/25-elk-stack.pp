################################################################################
# DreamFactory Enterprise(tm) Installer Manifest
# (c) 2012-∞ by DreamFactory Software, Inc. All Rights Reserved.
#
# Installs Elasticsearch, Logstash, and Kibana
################################################################################

notify { 'announce-thyself':
  message => '[DFE] Install/update ELK stack',
}

class { "apt":
  update => {
    frequency => daily
  }
}

apt::source { "elasticsearch.debian":
  comment  => "Repo for elasticsearch",
  location => "http://packages.elastic.co/elasticsearch/2.x",
  release  => "debian",
  repos    => "main",
  key      => {
    "id"     => "46095ACC8548582C1A2699A9D27D666CD88E42B4",
    "server" => "keys.gnupg.net"
  },
  include  => {
    "src" => false,
    "deb" => true
  },
  notify   => Exec["apt-update"]
}

##  Elasticsearch
file { '/opt/elasticsearch':
  ensure => directory,
  owner  => $www_user,
  group  => $group,
} ->
class { "elasticsearch":
  restart          => true,
  require          => Apt::Source["elasticsearch.debian"],
  java_install     => true,
  config           => {
    'cluster' => {
      'name' => "cluster-${vendor_id}",
    },
    'index'   => {
      'number_of_replicas' => '0',
      'number_of_shards'   => '1',
    },
    'network' => {
      'host' => '0.0.0.0',
    }
  }
} ->
service { "elasticsearch-service":
  name   => "elasticsearch",
  ensure => running
}
