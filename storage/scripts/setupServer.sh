#!/bin/bash
# first thing is to install the apache server and goto the web page to finish the setup.
apt-get install apache2

# determine wan and lan and enable port forwarding and lan cofiguration

# add iptable rules and apply

# install and configure dnsmasq
sudo apt-get install dnsmasq
#copy my defualt conf to the dnsmasq /etc/

# reverse proxy
## enable on apache
sudo ln -s /etc/apache2/mods-available/proxy.load /etc/apache2/mods-enabled
sudo ln -s /etc/apache2/mods-available/proxy_http.load /etc/apache2/mods-enabled
sudo /etc/init.d/apache2 restart

## update sites-available and sites-available permissions
cd /etc/apache2
sudo chgrp -R www-data sites-available
sudo chmod -R g+w sites-available
sudo chgrp -R www-data sites-enabled
sudo chmod -R g+w sites-enabled


auto lo                           
iface lo inet loopback            
                                  
# The primary network interface wan connected to your modem
auto eth3                         
iface eth3 inet dhcp              
 
auto eth2                         
iface eth2 inet static            
    address 10.0.1.1              
    network 10.0.1.0              
    netmask 255.255.255.0         
    broadcast 10.0.1.255          
#    bridge-ports eth3 eth2       