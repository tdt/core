bash "set default locale to UTF-8" do
  code <<-EOH
update-locale LANG=en_US.UTF-8 LC_ALL=en_US.UTF-8
dpkg-reconfigure locales
EOH
end
#
# dont't prompt for host key verfication (if any)
template "/home/vagrant/.ssh/config" do
  user "vagrant"
  group "vagrant"
  mode "0600"
  source "config"
end

execute "apt-get update"
package "python-software-properties"

bash "Add PPA for latest PHP" do
  code <<-EOH
  sudo add-apt-repository ppa:ondrej/php5
  EOH
end

execute "apt-get update"

# install the software we need
%w(
curl
tmux
vim
git
mysql-server
php5
php5-dev
php5-curl
php5-memcache
php5-mysql
php5-mcrypt
memcached
apache2
libapache2-mod-php5
nodejs
build-essential
openssl
libssl-dev
).each { | pkg | package pkg }

template "/home/vagrant/.bash_aliases" do
  user "vagrant"
  mode "0644"
  source ".bash_aliases.erb"
end

template "/home/vagrant/.bash_profile" do
  user "vagrant"
  group "vagrant"
  source ".bash_profile"
end

execute "a2enmod rewrite"
execute "a2enmod php5"

service "apache2" do
  supports :restart => true, :reload => true, :status => true
  action [ :enable, :start ]
end

file "/etc/apache2/sites-enabled/000-default.conf" do
  action :delete
end

template "/etc/apache2/sites-enabled/vhost.conf" do
  user "root"
  mode "0644"
  source "vhost.conf.erb"
  notifies :reload, "service[apache2]"
end

execute "date.timezone = UTC in php.ini?" do
 user "root"
 not_if "grep 'date.timezone = UTC' /etc/php5/cli/php.ini"
 command "echo -e '\ndate.timezone = UTC\n' >> /etc/php5/cli/php.ini"
end

bash "retrieve composer" do
  user "vagrant"
  cwd "/vagrant"
  code <<-EOH
  set -e

  # check if composer is installed
  if [ ! -f composer.phar ]
  then
    curl -s https://getcomposer.org/installer | php
  else
    php composer.phar selfupdate
  fi
  EOH
end

bash "Clone The DataTank repo" do
  user "vagrant"
  cwd "/vagrant"
  code <<-EOH
  set -e
  chmod -R  777 app/storage
  EOH
end

bash "Create database" do
  user "vagrant"
  cwd "/vagrant"
  not_if "echo 'show databases' | mysql -u root | grep datatank"
  code <<-EOH
  set -e
  echo "create database datatank" | mysql -u root
  EOH
end

template "/vagrant/app/config/database.php" do
  user "vagrant"
  group "vagrant"
  source "database.php"
end

template "/vagrant/app/config/app.php" do
  user "vagrant"
  group "vagrant"
  source "app.php"
end

bash "run composer" do
  user "vagrant"
  cwd "/vagrant"
  not_if "echo 'show tables' | mysql -u root datatank | grep migrations"
  code <<-EOH
  set -e
  export COMPOSER_HOME=/home/vagrant
  php composer.phar install
  EOH
end

bash "Load test data" do
  user "vagrant"
  cwd "/vagrant"
  only_if { node[:provcmd] == 'testdata' }
  code <<-EOH
  php artisan db:seed --class=DemoDataSeeder
  EOH
end

