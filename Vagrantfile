# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant::Config.run do |config|
  config.vm.box = "precise32"

  # The url from where the 'config.vm.box' box will be fetched if it
  # doesn't already exist on the user's system.
  config.vm.box_url = "http://files.vagrantup.com/precise32.box"

  # Use host-only networking.
  # Install vagrant-hostmaster to automatically manage host's hosts file.
  # Change IP address if you need to run more than one project at a time, or if
  # chosen netmask conflicts another network.
  config.vm.host_name = "dev.scenarioed.org"
  config.vm.network :hostonly, "192.168.87.11"

  # Enable NFS shares when possible
  nfs_flag = (RUBY_PLATFORM =~ /linux/ or RUBY_PLATFORM =~ /darwin/)
  config.vm.share_folder("v-root", "/vagrant", ".", :nfs => true)

  # Enable provisioning with Puppet.
  config.vm.provision :puppet do |puppet|
    puppet.manifests_path = "manifests"
    puppet.manifest_file  = "default.pp"
  end
end
