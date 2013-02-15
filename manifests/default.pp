exec { "apt-get update":
  command => "/usr/bin/apt-get update",
}

Package {
  require => Exec["apt-get update"],
}

package { "apache2": }
package { "apache2.2-common": }
package { "php5": }
package { "php5-cli": }
package { "libapache2-mod-php5": }
package { "php-apc": }
package { "php5-gd": }
package { "php5-intl": }
package { "php5-mysql": }
package { "mysql-server": }

service { "apache2":
  ensure => "running",
  enable => "true",
  require => Package["apache2"],
}

file { "/etc/apache2/sites-available/default":
  notify => Service["apache2"],
  ensure => "present",
  source => "/vagrant/manifests/files/default.vhost",
  require => Package["apache2"],
}

exec { "mod rewrite":
  notify => Service["apache2"],
  command => "/usr/sbin/a2enmod rewrite",
  require => Package["apache2"],
}

file { "/etc/php5/conf.d/site.ini":
  notify => Service["apache2"],
  ensure => "present",
  source => "/vagrant/manifests/files/site.ini",
  require => Package["php5"],
}

service { "mysql":
  ensure => "running",
  enable => "true",
  require => Package["mysql-server"],
}

exec { "mysql password":
  unless => "/usr/bin/mysql -uroot -ppassword",
  command => "/usr/bin/mysqladmin -u root password password",
  notify => Service["mysql"],
  require => Package["mysql-server"],
}

exec { "create-db":
  unless => "/usr/bin/mysql -uscenarioed scenarioed",
  command => "/usr/bin/mysql -uroot -ppassword -e \"create database scenarioed; grant all on scenarioed.* to scenarioed@localhost;\"",
  require => Exec["mysql password"],
}
