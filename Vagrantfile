# -*- mode: ruby -*-
# vi: set ft=ruby :

# By http://github.com/metaodi/datatank-vagrant
# Edited by Pieter Colpaert

##############################################################################
#                    setup your site's configuration                         #
##############################################################################

# the ip address where the vm can be accessed from the host
vm_ip                   = "172.23.5.42"
host_name               = "tdt.dev"
host_aliases            =  %w(www.tdt.dev)
vagrant_config_folder   = "/vagrant"

##############################################################################
#                        DO NOT CHANGE FROM HERE                             #
##############################################################################

require 'rbconfig'
WINDOWS = (RbConfig::CONFIG['host_os'] =~ /mswin|mingw|cygwin/) ? true : false
this_dir = File.dirname(__FILE__) + "/"

if ARGV.include?("up") || ARGV.include?("provision")
    # check if dump is supplied
    if ARGV.last[/^testdata$/]
      provcmd = "testdata"
      ARGV.pop
    end
end

Vagrant.configure("2") do |config|
    config.vm.define :ubuntu do |ubuntu|
        ubuntu.vm.box = "precise64"
        ubuntu.vm.box_url = "http://files.vagrantup.com/precise64.box"
        ubuntu.vm.synced_folder ".", "/vagrant", :nfs => !WINDOWS

        # whithout this symlinks can't be created on the shared folder
        ubuntu.vm.provider :virtualbox do |vb|
            vb.customize ["setextradata", :id, "VBoxInternal2/SharedFoldersEnableSymlinksCreate/v-root", "1"]
        end

        ubuntu.vm.network :private_network, ip: vm_ip
        ubuntu.vm.hostname = host_name

        # chef solo configuration
        ubuntu.vm.provision :chef_solo do |chef|

            chef.cookbooks_path = "./"
            # chef debug level, start vagrant like this to debug:
            # $ CHEF_LOG_LEVEL=debug vagrant <provision or up>
            chef.log_level = ENV['CHEF_LOG'] || "info"

            # chef recipes/roles
            chef.add_recipe("vagrant")

            host_ip = vm_ip[/(.*\.)\d+$/, 1] + "1"

            chef.json = {
              :host_ip => host_ip,
              :host_name => host_name,
              :host_aliases => host_aliases,
              :provcmd => provcmd,
              :xdebug_enabled => true,
              :xdebug_remote_enable => "1",
              :xdebug_remote_port => "9000",
              :xdebug_profiler_output_dir => "/vagrant/xdebug",
              :xdebug_trace_output_dir => "/vagrant/xdebug"
            }
        end
    end
end
