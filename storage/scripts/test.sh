#!/bin/bash
packages="apache2 dnsmasq iptables-persistent libapache2-mod-php php php-mcrypt php-mysql php-cli php-xml zip unzip hddtemp mysql-server mysql-client"
# echo $packages
for package in "${packages[@]}"
do
  PKG_OK=$(dpkg-query -W --showformat='${Status}\n' $package|grep "install ok installed")
  if [ "" == "$PKG_OK" ]; then
    # sudo apt-get --force-yes --yes install "$package"
    echo "$package"
  fi
done
