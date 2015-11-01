################################################################################
# DreamFactory Enterprise(tm) Installer Manifest
# (c) 2012-∞ by DreamFactory Software, Inc. All Rights Reserved.
#
# Installs MySQL/Percona server
################################################################################

notify { 'announce-thyself':
  message => '[DFE] The Mighty ELK',
}

Exec { path => ['/usr/bin','/usr/sbin','/bin','/sbin'], }

# ensure local apt cache index is up to date before beginning
exec { 'apt-get update':
  command => '/usr/bin/apt-get update'
}

##  ELK stack installer
class elk( $root ) {
  file { ["/opt/sites", "/opt/sites/kibana", "/opt/sites/test", "/opt/sites/_releases", "/opt/sites/_releases/kibana"]:
    ensure  => directory,
    owner   => $www_user,
    group   => $group,
    mode    => 2755,
    recurse => true,
  }

  exec { "install-java8":
    command => "add-apt-repository -y ppa:webupd8team/java && sudo apt-get update && echo debconf shared/accepted-oracle-license-v1-1 select true | sudo debconf-set-selections && echo debconf shared/accepted-oracle-license-v1-1 seen true | sudo debconf-set-selections && sudo apt-get -y install oracle-java8-installer",
    cwd     => $root,
  }

  exec { "install-elasticsearch":
    unless  => 'service elasticsearch status',
    command => "wget -qO - https://packages.elastic.co/GPG-KEY-elasticsearch | sudo apt-key add - && echo 'deb http://packages.elastic.co/elasticsearch/2.x/debian stable main' | sudo tee -a /etc/apt/sources.list.d/elasticsearch.list && sudo apt-get -qq update && sudo apt-get -yq install elasticsearch",
    cwd     => $root,
  }

  # custom configuration
  file{ '/etc/elasticsearch/elasticsearch.yml' :
    ensure  => present,
    source  => "$PWD/resources/assets/etc/elasticsearch.yml",
    require => Exec["install-elasticsearch"]
  }

  # restart elasticsearch service
  service { "elasticsearch":
    ensure  => running,
    require => [Exec['install-elasticsearch'], File['/etc/elasticsearch/elasticsearch.yml']],
  }

  exec { "install kopf":
    cwd     => "/home/$user",
    command => "sudo /usr/share/elasticsearch/bin/plugin -install lmenezes/elasticsearch-kopf",
    require => Exec['install-elasticsearch']
  }

  exec { "download-kibana4":
    cwd     => "/opt/sites/_releases/kibana",
    command => "wget https://download.elastic.co/kibana/kibana/kibana-4.2.0-linux-x64.tar.gz",
    creates => "/opt/sites/_releases/kibana/kibana-4.2.0-linux-x64.tar.gz",
  }

  exec { "install-kibana4":
    # unless => 'service logstash status',
    cwd     => "/opt/sites/_releases/kibana",
    command => "tar xzf kibana-4.2.0-linux-x64.tar.gz",
    require => Exec["download-kibana4"],
  }->
  file { "/opt/sites/kibana":
    ensure => link,
    target => "/opt/sites/_releases/kibana/kibana-4.2.0-linux-x64",
  }

  exec { "download-logstash":
    cwd     => "/var/cache/apt/archives",
    command => "wget https://download.elastic.co/logstash/logstash/packages/debian/logstash_2.0.0-1_all.deb",
    creates => "/var/cache/apt/archives/logstash_2.0.0-1_all.deb",
  }

  exec { "install-logstash":
    unless  => 'service logstash status',
    cwd     => "/var/cache/apt/archives",
    command => "dpkg -i logstash_2.0.0-1_all.deb && update-rc.d logstash defaults 95 10 && /etc/init.d/logstash restart",
    require => Exec["download-logstash"]
  }

}

class { elk:
  root => $pwd,
}
