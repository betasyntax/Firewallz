#!/bin/bash

# part of firewallz (https://www.firewallz.com)
# Copyright (c) 2017 Donovan Cameron
# All rights reserved.
#
# Licensed under the Apache License, Version 2.0 (the "License");
# you may not use this file except in compliance with the License.
# You may obtain a copy of the License at
#
# http://www.apache.org/licenses/LICENSE-2.0
#
# Unless required by applicable law or agreed to in writing, software
# distributed under the License is distributed on an "AS IS" BASIS,
# WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
# See the License for the specific language governing permissions and
# limitations under the License.

printf "  __ _                        _ _     \n"
printf " / _(_)_ __ _____      ____ _| | |____\n"
printf "| |_| |  __/ _ \ \ /\ / / _  | | |_  /\t\tversion: \t0.1\n"
printf "|  _| | | |  __/\ V  V / (_| | | |/ / \t\tauthor: \tDonovan Cameron\n"
printf "|_| |_|_|  \___| \_/\_/ \__,_|_|_/___|\t\tlicense: \tApache License Version 2.0\n\n"

. /etc/lsb-release
if [ ! $DISTRIB_ID = 'Ubuntu' ];then
  echo 'Sorry but this script currently only supports Ubuntu.'
  exit 1
fi

if [ $DISTRIB_RELEASE = '16.04' ];then
  mysql_release='5.7'
elif [ $DISTRIB_RELEASE = '14.04'];then
  mysql_release='5.5'
else
  echo 'Sorry but this script currently only supports Ubuntu 14.04 LTS and above.'
  exit 1  
fi

if [ ! -x /usr/bin/wget ] ; then
    # some extra check if wget is not installed at the usual place                                                                           
    command -v wget >/dev/null 2>&1 || { echo >&2 "Please install wget or set it in your path. Aborting."; exit 1; }
fi

setupMysql() {
  printf "Configuring Mysql...\n"

  echo mysql-server-"$mysql_release" mysql-server/root_password password change | debconf-set-selections
  echo mysql-server-"$mysql_release" mysql-server/root_password_again password change | debconf-set-selections
  # mysqladmin -u root password change
  mysql -u root -p < $mysqlimport
  mysql -u root -p <<MYSQL_SCRIPT
    USE router;
    UPDATE settings SET value = '$wan' WHERE key_name = 'external_iface';
    UPDATE settings SET value = '$lan' WHERE key_name = 'internal_iface';
    UPDATE settings SET value = '$domain' WHERE key_name = 'internal_iface_domain';
    UPDATE settings SET value = '$wan_ip' WHERE key_name = 'external_iface_ip';
MYSQL_SCRIPT
  echo "MySQL user created."
  echo "Username:   $user"
  echo "Password:   $pass"
}
prep() {
if [ -n "$BADMODULES" ]; then
  /bin/echo "   Removing modules : "
  for BADMODULE in $BADMODULES; do
    /bin/echo -en "$BADMODULE, "
    if [ -z "` $LSMOD | $GREP $BADMODULE | $AWK {'print $1'} `" ]; then
      $RMMOD $BADMODULE
    fi
  done
fi
/bin/echo -n "Re-loading kernel modules: "
if [ -n "$MODULES" ]; then
  for MODULE in $MODULES; do
    /bin/echo -en "$MODULE "
    if [ -z "` $LSMOD | $GREP $MODULE | $AWK {'print $1'} `" ]; then
      $RMMOD $MODULE; $MODPROBE $MODULE
    fi
  done
fi
/bin/echo " "
tc qdisc del dev $HOTIN root    2> /dev/null > /dev/null
tc qdisc del dev $HOTIN ingress 2> /dev/null > /dev/null
}
clean() {
  $IPTABLES --flush
  $IPTABLES -F INPUT
  $IPTABLES -P INPUT DROP
  $IPTABLES -F OUTPUT
  $IPTABLES -P OUTPUT DROP
  $IPTABLES -F FORWARD
  $IPTABLES -P FORWARD DROP
  $IPTABLES -t nat -F
  if [ -n "`$IPTABLES -L | $GREP drop-and-log-it`" ]; then
    $IPTABLES -F drop-and-log-it
  fi
  if [ -n "`$IPTABLES -L | $GREP luser-drop-and-log-it`" ]; then
    $IPTABLES -F luser-drop-and-log-it
  fi
  if [ -n "`$IPTABLES -L | $GREP base-policy`" ]; then
    $IPTABLES -F base-policy
  fi
  $IPTABLES -X
  $IPTABLES -Z
}
services() {
  if [ -n "$TCPPORTS" ]; then
    /bin/echo -en "Allowing in TCP ports: "
    for PORT in $TCPPORTS; do
      /bin/echo -en "$PORT, "
      $IPTABLES -A INPUT -i $HOTIN -d $HOTIP -p tcp --dport $PORT -j ACCEPT
    done
    /bin/echo " "
  fi
  if [ -n "$UDPPORTS" ]; then
    /bin/echo -en "Allowing in UDP ports: "
    for PORT in $UDPPORTS; do
      /bin/echo -en "$PORT, "
      $IPTABLES -A INPUT -i $HOTIN -d $HOTIP -p udp --dport $PORT -j ACCEPT
    done
    /bin/echo " "
  fi
  /bin/echo "Loading Forwarding Rules:"
  for ((i=1;i<=$NHOST;i++)); do
    if [ ! "${FWHOST[tcp$i]}" == 0 ]; then
      /bin/echo -en "forwarding TCP ports to ${FWHOST[ip$i]} : "
      for PORT in ${FWHOST[tcp$i]}; do
        /bin/echo -en "$PORT, "
        $IPTABLES -t nat -A PREROUTING -p tcp --dport $PORT -i $HOTIN -j DNAT --to-destination ${FWHOST[ip$i]}
        $IPTABLES -A FORWARD -p tcp --dport $PORT -i $HOTIN -o $CLDIN -d ${FWHOST[ip$i]} -j ACCEPT
      done
      /bin/echo " "
    fi
    if [ ! "${FWHOST[udp$i]}" == 0 ]; then
      /bin/echo -en "forwarding UDP ports to ${FWHOST[ip$i]} : "
      for PORT in ${FWHOST[udp$i]}; do
        /bin/echo -en "$PORT, "
        $IPTABLES -t nat -A PREROUTING -p udp --dport $PORT -i $HOTIN -j DNAT --to-destination ${FWHOST[ip$i]}
        $IPTABLES -A FORWARD -p udp --dport $PORT -i $HOTIN -o $CLDIN -d ${FWHOST[ip$i]} -j ACCEPT
      done
      /bin/echo " "
    fi
  done
}
rules() {
  $IPTABLES -N drop-and-log-it
  $IPTABLES -A drop-and-log-it -j LOG --log-level info --log-prefix="DROP TRAFFIC: "
  $IPTABLES -A drop-and-log-it -j DROP
  $IPTABLES -N luser-drop-and-log-it
  $IPTABLES -A luser-drop-and-log-it -j LOG --log-level info --log-prefix="LUSER TRAFFIC: "
  $IPTABLES -A luser-drop-and-log-it -j DROP
  $IPTABLES -N base-policy
  $IPTABLES -F base-policy
  $IPTABLES -A base-policy -f -m limit --limit 6/minute -j luser-drop-and-log-it
  $IPTABLES -A base-policy -m state --state INVALID -m limit --limit 6/minute -j luser-drop-and-log-it
  $IPTABLES -A base-policy -p ALL -s $UNIVERSE -d $BROADCAST -m limit --limit 6/minute -j luser-drop-and-log-it
  $IPTABLES -A base-policy -p tcp --tcp-flags ALL FIN,URG,PSH -m limit --limit 6/minute -j luser-drop-and-log-it
  $IPTABLES -A base-policy -p tcp --tcp-flags ALL ALL -m limit --limit 6/minute -j luser-drop-and-log-it
  $IPTABLES -A base-policy -p tcp --tcp-flags ALL SYN,RST,ACK,FIN,URG -m limit --limit 6/minute -j luser-drop-and-log-it
  $IPTABLES -A base-policy -p tcp --tcp-flags ALL NONE -m limit --limit 6/minute -j luser-drop-and-log-it
  $IPTABLES -A base-policy -p tcp --tcp-flags SYN,RST SYN,RST -m limit --limit 6/minute -j luser-drop-and-log-it
  $IPTABLES -A base-policy -p tcp --tcp-flags SYN,FIN SYN,FIN -m limit --limit 6/minute -j luser-drop-and-log-it
  $IPTABLES -A base-policy -p tcp --dport 7 -m limit --limit 6/minute -j luser-drop-and-log-it
  $IPTABLES -A base-policy -p udp --dport 7 -m limit --limit 6/minute -j luser-drop-and-log-it
  $IPTABLES -A base-policy -p tcp --dport 11 -m limit --limit 6/minute -j luser-drop-and-log-it
  $IPTABLES -A base-policy -p tcp --dport 15 -m limit --limit 6/minute -j luser-drop-and-log-it
  $IPTABLES -A base-policy -p tcp --dport 19 -m limit --limit 6/minute -j luser-drop-and-log-it
  $IPTABLES -A base-policy -p udp --dport 19 -m limit --limit 6/minute -j luser-drop-and-log-it
  $IPTABLES -A base-policy -p tcp --dport 23 -m limit --limit 6/minute -j luser-drop-and-log-it
  $IPTABLES -A base-policy -p tcp --dport 69 -m limit --limit 6/minute -j luser-drop-and-log-it
  $IPTABLES -A base-policy -p tcp --dport 79 -m limit --limit 6/minute -j luser-drop-and-log-it
  $IPTABLES -A base-policy -p tcp --dport 87 -m limit --limit 6/minute -j luser-drop-and-log-it
  $IPTABLES -A base-policy -p tcp --dport 98 -m limit --limit 6/minute -j luser-drop-and-log-it
  $IPTABLES -A base-policy -p tcp --dport 111 -m limit --limit 6/minute -j luser-drop-and-log-it
  $IPTABLES -A base-policy -p tcp --dport 520 -m limit --limit 6/minute -j luser-drop-and-log-it
  $IPTABLES -A base-policy -p tcp --dport 540 -m limit --limit 6/minute -j luser-drop-and-log-it
  $IPTABLES -A base-policy -p tcp --dport 1080 -m limit --limit 6/minute -j luser-drop-and-log-it
  $IPTABLES -A base-policy -p tcp --dport 1114 -m limit --limit 6/minute -j luser-drop-and-log-it
  $IPTABLES -A base-policy -p tcp --dport 2000 -m limit --limit 6/minute -j luser-drop-and-log-it
  $IPTABLES -A base-policy -p tcp --dport 10000 -m limit --limit 6/minute -j luser-drop-and-log-it
  $IPTABLES -A base-policy -p tcp --dport 6000:6063 -m limit --limit 6/minute -j luser-drop-and-log-it
  $IPTABLES -A base-policy -p udp --dport 33434:33523 -m limit --limit 6/minute -j luser-drop-and-log-it
  $IPTABLES -A base-policy -p tcp --dport 6670 -m limit --limit 6/minute -j luser-drop-and-log-it
  $IPTABLES -A base-policy -p tcp --dport 1243 -m limit --limit 6/minute -j luser-drop-and-log-it
  $IPTABLES -A base-policy -p udp --dport 1243 -m limit --limit 6/minute -j luser-drop-and-log-it
  $IPTABLES -A base-policy -p tcp --dport 6711:6713 -m limit --limit 6/minute -j luser-drop-and-log-it
  $IPTABLES -A base-policy -p udp --dport 6711:6713 -m limit --limit 6/minute -j luser-drop-and-log-it
  $IPTABLES -A base-policy -p tcp --dport 27374 -m limit --limit 6/minute -j luser-drop-and-log-it
  $IPTABLES -A base-policy -p udp --dport 27374 -m limit --limit 6/minute -j luser-drop-and-log-it
  $IPTABLES -A base-policy -p tcp --dport 12345:12346 -m limit --limit 6/minute -j luser-drop-and-log-it
  $IPTABLES -A base-policy -p tcp --dport 20034 -m limit --limit 6/minute -j luser-drop-and-log-it
  $IPTABLES -A base-policy -p tcp --dport 31337:31338 -m limit --limit 6/minute -j luser-drop-and-log-it
  $IPTABLES -A base-policy -p udp --dport 28431 -m limit --limit 6/minute -j luser-drop-and-log-it
  # wifi
  # vpn
  $IPTABLES -A INPUT -i $HOTIN -j base-policy
  $IPTABLES -A INPUT -i lo -s $UNIVERSE -d $UNIVERSE -j ACCEPT
  $IPTABLES -A INPUT -i $HOTIN -s $CLDNET -d $UNIVERSE -j luser-drop-and-log-it
  $IPTABLES -A INPUT -i $HOTIN -p udp --dport 67 -s $UNIVERSE -j ACCEPT
  $IPTABLES -A INPUT -i $HOTIN -p udp --dport 68 -s $UNIVERSE -j DROP
  $IPTABLES -I INPUT -i $CLDIN -p udp --dport 67:68 --sport 67:68 -j ACCEPT
  $IPTABLES -A INPUT -i $CLDIN -s $CLDNET -d $UNIVERSE -j ACCEPT
  $IPTABLES -A INPUT -i $HOTIN -s $UNIVERSE -d $HOTIP -m state --state ESTABLISHED,RELATED -j ACCEPT
  services
  $IPTABLES -A INPUT -s $UNIVERSE -d $UNIVERSE -j drop-and-log-it
  $IPTABLES -A OUTPUT -o $HOTIN -j base-policy
  $IPTABLES -A OUTPUT -o lo -s $UNIVERSE -d $UNIVERSE -j ACCEPT
  $IPTABLES -A OUTPUT -o $HOTIN -p udp --dport 68 -j ACCEPT
  $IPTABLES -A OUTPUT -o $HOTIN -s $UNIVERSE -d $CLDNET -j luser-drop-and-log-it
  $IPTABLES -A OUTPUT -o $CLDIN -s $UNIVERSE -d $CLDNET -j ACCEPT
  $IPTABLES -A OUTPUT -o $HOTIN -s $HOTIP -d $UNIVERSE -j ACCEPT
  $IPTABLES -A OUTPUT -s $UNIVERSE -d $UNIVERSE -j drop-and-log-it
  $IPTABLES -A FORWARD -o $HOTIN -j base-policy
  $IPTABLES -A FORWARD -i $CLDIN -s $CLDNET -o $HOTIN -d $UNIVERSE -j ACCEPT
  $IPTABLES -A FORWARD -i $HOTIN -m state --state ESTABLISHED,RELATED -j ACCEPT
  $IPTABLES -t nat -A POSTROUTING -o $HOTIN -j MASQUERADE
  $IPTABLES -A FORWARD -j drop-and-log-it
}
shaper() {
  tc qdisc add dev $HOTIN root pfifo_fast
}
vpn() {
  $IPTABLES -A INPUT -i $VPNIN -j ACCEPT
  $IPTABLES -A OUTPUT -o $VPNIN -j ACCEPT
  $IPTABLES -A FORWARD -i $VPNIN -j ACCEPT
  # $IPTABLES -t nat -A POSTROUTING -o $VPNIN -j MASQUERADE
}
wifi() {
  $IPTABLES -A INPUT -i $WIFIIN -j ACCEPT
  $IPTABLES -A OUTPUT -o $WIFIIN -j ACCEPT
  $IPTABLES -A FORWARD -i $WIFIIN -o $HOTIN -j ACCEPT
  $IPTABLES -A FORWARD -i $WIFIIN -o $CLDIN -j ACCEPT
  $IPTABLES -t nat -A POSTROUTING -o $WIFIIN -j MASQUERADE
}
start() {
  /bin/echo "1" > /proc/sys/net/ipv4/ip_dynaddr
  /bin/echo "0" > /proc/sys/net/ipv4/icmp_echo_ignore_all
  /bin/echo "0" > /proc/sys/net/ipv4/icmp_echo_ignore_broadcasts
  /bin/echo "0" > /proc/sys/net/ipv4/conf/all/accept_source_route
  /bin/echo "0" > /proc/sys/net/ipv4/conf/all/accept_redirects
  /bin/echo "1" > /proc/sys/net/ipv4/icmp_ignore_bogus_error_responses
  for i in /proc/sys/net/ipv4/conf/*; do
    /bin/echo "1" > $i/rp_filter
  done
  prep
  clean
  shaper
  rules
  /bin/echo "1" > /proc/sys/net/ipv4/ip_forward
}
apacheConf() {
  echo "This will take a few minutes: "
  openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout /etc/ssl/private/apache-selfsigned.key -out /etc/ssl/certs/apache-selfsigned.crt -subj '/CN=www.change.com/O=My Company Name LTD./C=US'
  openssl dhparam -out /etc/ssl/certs/dhparam.pem 2048

  rm /etc/apache2/sites-available/default-ssl.conf
  cat >/etc/apache2/sites-available/default-ssl.conf <<EOL
  # from https://cipherli.st/
  # and https://raymii.org/s/tutorials/Strong_SSL_Security_On_Apache2.html

  SSLCipherSuite EECDH+AESGCM:EDH+AESGCM:AES256+EECDH:AES256+EDH
  SSLProtocol All -SSLv2 -SSLv3
  SSLHonorCipherOrder On
  # Disable preloading HSTS for now.  You can use the commented out header line that includes
  # the "preload" directive if you understand the implications.
  #Header always set Strict-Transport-Security "max-age=63072000; includeSubdomains; preload"
  Header always set Strict-Transport-Security "max-age=63072000; includeSubdomains"
  Header always set X-Frame-Options DENY
  Header always set X-Content-Type-Options nosniff
  # Requires Apache >= 2.4
  SSLCompression off 
  SSLSessionTickets Off
  SSLUseStapling on 
  SSLStaplingCache "shmcb:logs/stapling-cache(150000)"

  SSLOpenSSLConfCmd DHParameters "/etc/ssl/certs/dhparam.pem"
EOL
  rm /etc/apache2/sites-available/000-default.conf
  cat >/etc/apache2/sites-available/000-default.conf <<EOL
    <VirtualHost 10.0.1.1:80>
      ServerName example.com
      RewriteEngine On
      RewriteCond %{REQUEST_URI} !^/press/.*
      RewriteRule ^.*$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301,NC]
    </VirtualHost>
EOL
  rm /etc/apache2/sites-available/default-ssl.conf
  touch /etc/apache2/sites-available/default-ssl.conf
  cat >/etc/apache2/sites-available/default-ssl.conf <<EOL
  <IfModule mod_ssl.c>
    <VirtualHost _default_:443>
      ServerAdmin your_email@example.com
      ServerName router.battleverse.tv

      DocumentRoot $document_root
      ErrorLog ${APACHE_LOG_DIR}/error.log
      CustomLog ${APACHE_LOG_DIR}/access.log combined
      SSLEngine on
      SSLCertificateFile      /etc/ssl/certs/apache-selfsigned.crt
      SSLCertificateKeyFile /etc/ssl/private/apache-selfsigned.key
      <FilesMatch "\.(cgi|shtml|phtml|php)$">
        SSLOptions +StdEnvVars
      </FilesMatch>
      <Directory /usr/lib/cgi-bin>
        SSLOptions +StdEnvVars
      </Directory>

      <Directory $document_root/>
        Options -Indexes +FollowSymLinks +MultiViews
        AllowOverride All
        Require all granted
        Order allow,deny
        Allow from 127.0.0.1 ::1
        Allow from localhost
        Allow from 10
        ErrorDocument 403 "ya right buddy i dont think so"
      </Directory>
      Protocols h2 h2c http/1.1
      Alias "/debug" "$document_root/../vendor/maximebf/debugbar/src/DebugBar/Resources/"
      <Directory $document_root/../vendor/maximebf/debugbar/src/DebugBar/Resources/>
        AllowOverride All
        Require all granted
        Order allow,deny
        Allow from 127.0.0.1 ::1
        Allow from localhost
        Allow from 10
        ErrorDocument 403 "ya right buddy i dont think so"
      </Directory>
      BrowserMatch "MSIE [2-6]" nokeepalive ssl-unclean-shutdown downgrade-1.0 force-response-1.0
    </VirtualHost>
  </IfModule>
EOL
  a2enmod ssl
  a2enmod headers
  a2ensite default-ssl
  a2enconf ssl-params
  apache2ctl configtest
  a2ensite 000-default.conf
}

#set some vars
cards=(`ls /sys/class/net`)
port=8080
document_root=`pwd`"/../../../public"
mysqlimport=`pwd`"/sql/init.sql"
storage_dir=`pwd`"/../../"
apache2="/etc/apache2/"
pass="router"
user="router"
host_file=$storage_dir"hosts.conf"
conf_file=$storage_dir"dhcp-hosts.conf"
host=dhostname
wan_ip=`wget -qO- ipinfo.io/ip`
nic_names=(`lspci | awk '/net/ {print $1}'`)
nic_device_id=(`ls -la /sys/class/net | grep eth`)
if [ "" == "$nic_device_id" ];then
  nic_device_id=(`ls -la /sys/class/net | grep enp`)
fi
#install neccessary packages
packages=(apache2 dnsmasq iptables-persistent smartmontools php7.0-fpm php-mcrypt php-mysql php-cli php-xml zip unzip hddtemp mysql-server mysql-client bridge-utils)

printf "Installing neccessary packages...\n"


for package in "${packages[@]}"
do
  printf "$package "
done
echo 
while true
do
  read  -e -p "(C)ontinue or E(x)it: " answer
  case $answer in
   [cC]* )            
           status='proceed'
           break;;
   [xX]* ) exit;;

   * )     ;;
  esac
done

if [ $status = "proceed" ];then

  x=0
  # add-apt-repository -y ppa:ondrej/apache2
  # apt update
  # apt-get update
  for package in "${packages[@]}"
  do
    # printf "$package "
    PKG_OK=$(dpkg -s $package|grep "install ok installed")
    
    if [ "" == "$PKG_OK" ]; then
      echo $package
      apt-get -y install $package
      dpkg --configure -a
    fi

    ((x++))
  done

fi
printf "\n\e[PPackage installation complete!\e[37m\n\n"

for i in ${!cards[@]} ; do # iterate over array indexes
  if [ ${cards[$i]} = 'lo' ] ; then # if it's the value you want to delete
    cards[$i]='' # set the string as empty at this specific index
  fi
done

# first we are going to try and guess the ip address and wan lan combos for now there will be no bridging involved.
# get wan ip

for j in ${cards[@]} ; do
  ifc=`ifconfig $j | awk '/inet addr/{print substr($2,6)}'`
  # private_ip=`ifconfig | grep 'inet addr' | cut -d ':' -f 2 | awk '{ print $1 }' | grep -E '^(192\.168|10\.|172\.1[6789]\.|172\.2[0-9]\.|172\.3[01]\.)'`
  private_ip=`ifconfig | grep 'inet addr' | cut -d ':' -f 2 | awk '{ print $1 }' | grep -E '^(192\.168|10\.)'`
  if [ $ifc = "$wan_ip" ];then
    wan=$j
  elif [ ! "$private_ip" = '' ];then
    lan=$j
  fi
done

if [ ! $wan = "" ] || [ ! $lan = "" ];then
  printf "This script is going make changes to your network configuration. "
  printf "It is expected that you have two network cards, one connected to your ISP and the other to your LAN. \n\n"
  printf "I have found some suitable defaults for your network configuration. "
  printf "Please review them below and select whether you want continue, change the "
  printf "configuration or cancel the installation. Please note at this point in "
  printf "time this script assumes that your WAN network card connects via dhcp.\n"

  cnt=10
  for x in ${!nic_names[@]} ; do
      if grep -q "${nic_names[$x]}" <<<"${nic_device_id[$cnt]}" ;then
        if grep -q $wan  <<<"${nic_device_id[$cnt]}" ;then
          wan_info="Wan: "$wan" Internet -> "`lspci | grep ${nic_names[$x]}`
        elif grep -q $lan <<<"${nic_device_id[$cnt]}"; then
          lan_info="Lan: "$lan" Local Network -> "`lspci | grep ${nic_names[$x]}`
        fi
      fi
    ((cnt=cnt+11))
  done
fi
echo $wan
echo $lan

echo
printf "WAN Ip Address: \t$wan_ip\n"
echo $wan_info
echo 
printf "LAN Ip Address: \t$private_ip\n"
echo $lan_info
printf "Subnet: \t\t255.255.255.0\n"
printf "Hostname: \t\trouter\n"
printf "Domain Name: \t\tN/A\n"
x=`pwd`
# ( exec /bin/bash "$x/firewall.sh" -hotin $wan -hotip $wan_ip -cldin $lan -cldip $private_ip restart )

# exit

echo 
while true
do
  read  -e -p "What would you like to do (C)hange, (P)roceed or E(x)it: " answer
  case $answer in
   [pP]* )            
           status='proceed'
           break;;
   [cC]* )
           status='change'
           break;;
   [xX]* ) exit;;

   * )     ;;
  esac
done
echo $status

if [ $status = "change" ];then
  printf "Which network card is conected to your internet: "
  echo "${cards[@]}"
  breaker=0
  while true
  do
    read -e -p '' wan
    x=0
    for j in ${cards[@]} ; do # iterate over array indexes
      if [ "$j" == "$wan" ] ; then # if it's the value you want to delete
        ((breaker++))
        cards[$x]='' # set the string as empty at this specific index
        #loop to set this cards connect info. dhcp static bridged etc
        break
      fi
      ((x++))
    done
    if [ $breaker == 1 ];then
        break
    fi
    # read  -p "Proceed with installation? Defult [Y/n] " answer
    printf "Please pic one of the these interfaces: "
    echo "${cards[@]}"
  done


  printf "Which network card is conected to your local network: "
  echo "${cards[@]}"
  while true
  do
    read -e lan
    tput cuu1
    y=0
    for k in ${cards[@]} ; do # iterate over array indexes
      if [ "$k" == "$lan" ] ; then # if it's the value you want to delete
        ((breaker++))
        cards[$y]='' # set the string as empty at this specific index
        break
      fi
      ((y++))
    done
    if [ "$breaker" == "2" ];then
      break
    fi
    printf "Please pic one of the these interfaces: "
    echo "${cards[@]}"
    # read  -p "Proceed with installation? Defult [Y/n] " answer
  done
  status="proceed"
  echo
fi

if [ $status = "proceed" ];then

  # echo $cnt
  # if [ "$USCOUNTER" == 2 ];then
  printf  "What is your domain name: "
  read -e domain


  # exit 1
  printf "Reconfiguring network...\n"
  cp /etc/network/interfaces /etc/network/interfaces.orig
  truncate -s 0 /etc/network/interfaces
  cat >/etc/network/interfaces <<EOL
  auto lo
  iface lo inet loopback

  auto $wan
  iface $wan inet dhcp

  auto $lan
  iface $lan inet static
      address 10.0.1.1
      network 10.0.1.0
      netmask 255.255.255.0
      broadcast 10.0.1.255
EOL

  # ip addr flush $wan
  # ip addr flush $lan
  ifdown $wan
  ifdown $lan
  ifup $wan
  ifup $lan
  printf "Network restarted...\n"


  printf "Setting up Apache\n"
  a2enmod proxy_fcgi setenvif
  a2enconf php7.0-fpm 
  a2dismod php7.0
  a2dismod mpm_prefork
  a2enmod mpm_event 
  printf "\e[39mEnabling the apache2 proxy module...\n"
  #enable the apache2 proxy module
  if [ ! -h /etc/apache2/mods-enabled/proxy.load ] || [ ! -h /etc/apache2/mods-enabled/proxy.load ]; then
    ln -s /etc/apache2/mods-available/proxy.load /etc/apache2/mods-enabled
    ln -s /etc/apache2/mods-available/proxy_http.load /etc/apache2/mods-enabled
  fi
  a2enmod rewrite
  #create the main apache virtual host
  printf "\e[32mDone!\n"
  printf "\e[39mCreating the main virtual host file...\n"
  apacheConf
  printf "\e[32mDone!\n"
  printf "\e[39mEnabling the site...\n"
  #enable this site
  a2ensite 001-router.conf
  printf "\e[32mDone!\n"
  printf "\e[39mChanging some permissions so we can create proxy sites...\n"
  #change permissions so we can write proxied virtual hosts to this directory
  chgrp -R www-data $apache2"sites-available"
  chmod -R g+w $apache2"sites-available"
  chgrp -R www-data $apache2"sites-enabled"
  chmod -R g+w $apache2"sites-enabled"
  printf "\e[32mDone!\n"
  printf "\e[39mRestarting Apache2...\n"
  /etc/init.d/apache2 restart
  # setupMysql
  printf "Configure DnsMasq...\n"
  cp /etc/dnsmasq.conf /etc/dnsmasq.conf.orig

  cat >/etc/dnsmasq.conf <<EOL
  #port=5353
  domain-needed
  bogus-priv
  filterwin2k
  server=/localnet/10.0.1.1
  server=8.8.8.8
  server=8.8.4.4
  server=/1.0.10.in-addr.arpa/10.0.1.1
  local=/localnet/
  interface=$lan
  no-hosts
  addn-hosts=/etc/dnsmasq.hosts
  expand-hosts
  domain=$domain
  dhcp-range=10.0.1.200,10.0.1.250,12h
  dhcp-option=23,50
  dhcp-option=27,1
  dhcp-option=19,0           # option ip-forwarding off
  dhcp-option=44,0.0.0.0     # set netbios-over-TCP/IP nameserver(s) aka WINS server(s)
  dhcp-option=45,0.0.0.0     # netbios datagram distribution server
  dhcp-option=46,8           # netbios node type
  dhcp-option=252,"\n"
  dhcp-lease-max=150
  dhcp-leasefile=/var/lib/misc/dnsmasq.leases
  # #dhcp-authoritative
  log-dhcp
  conf-file=/etc/dnsmasq.dhcp
EOL
  service dnsmasq restart
  printf "Setting up the Firewall...\n"
  # ./firewall.sh start
  # iptables --flush
  # iptables -A FORWARD -o $wan -i $lan -s 10.0.1.0/24 -m conntrack --ctstate NEW -j ACCEPT
  # iptables -A FORWARD -m conntrack --ctstate ESTABLISHED,RELATED -j ACCEPT
  # iptables -t nat -F POSTROUTING
  # iptables -t nat -A POSTROUTING -o $wan -j MASQUERADE

  #Set default vars

  IFCONFIG=`which ifconfig`
  IPTABLES=`which iptables`
  LSMOD=`which lsmod`
  GREP=`which grep`
  AWK=`which awk`
  SED=`which sed`
  CUT=`which cut`
  SED=`which sed`
  ROUTE=`which route`
  DEPMOD=`which depmod`
  INSMOD=`which insmod`
  MODPROBE=`which modprobe`
  RMMOD=`which rmmod`
  PING=`which ping`
  HOST=`which host`
  UNIVERSE="0.0.0.0/0"
  BROADCAST="255.255.255.255"

  #next we are going to try and set these vars from the passed arguments
  # while getopts hotin:hotip:cldin:cldip: option
  # do
  #  case "${option}"
  #  in
  #  hotin) HOTIN=${OPTARG};;
  #  hotip) HOTIP=${OPTARG};;
  #  cldin) CLDIN=${OPTARG};;
  #  cldip) CLDIP=$OPTARG;;
  #  esac
  # done

  # echo $HOTIN
  # echo $HOTIP
  # echo $CLDIN
  # echo $CLDIP

  # exit

  # change this to be dynamic
  HOTIN="enp4s0"
  HOTIP="`$IFCONFIG $HOTIN | $GREP 'inet addr' | $AWK '{print $2}' | sed -e 's/.*://'`"
  #HOTIP="`$IFCONFIG | $GREP P-t-P | $CUT -d ":" -f 2 | $CUT -d " " -f 1`"
  # change this to be dynamic
  CLDIN="enp6s0"
  # change this to be dynamic
  CLDIP="10.0.1.1"
  # change this to be dynamic
  CLDNET="10.0.1.0/24"
  #WIFIIN="ath0"
  #WIFIIP="192.168.12.1"
  #WIFINET="192.168.12.0/24"
  #VPNIN="tun+"
  #VPNIP="192.168.200.1"
  #VPNNET="192.168.200.0/24"
  #VPN2IN="tap+"
  #VPN2IP="10.0.1.1"
  #VPN2NET="10.0.1.0/24"
  FIREWALL="/etc/firewall.rules"
  # BADMODULES="ip_fwadm ip_chains"
  BADMODULES=""
  #MODULES="tun ipt_state ipt_limit ipt_LOG sch_ingress cls_fw sch_sfq sch_htb ipt_length ipt_MARK ipt_tos iptable_mangle iptable_filter ipt_MASQUERADE iptable_nat ip_conntrack ip_tables"
  MODULES="ip_tables"
  TCPPORTS=""
  #UDPPORTS="67 68"
  #UDPPORTS="9500"
  DOWNLINK=2900
  UPLINK=1500
  BURST=32
  #TOS10TCPPORTS="22"
  #TOS10TCPPORTS="53 80 443"
  #TOS20TCPPORTS="22"
  #TOS30TCPPORTS="21"
  #TOS10UDPPORTS="1:"
  #TOS20UDPPORTS=""
  #TOS30UDPPORTS=""
  declare -A FWHOST
  NHOST=1
  # Add your hosts here along with any tcp/udp ports.

  FWHOST[ip1]=""
  FWHOST[tcp1]=""
  FWHOST[udp1]=""
  # FWHOST[ip2]="10.0.1.200"
  # FWHOST[tcp2]="5222 9988 17502 20000:20100 22990 42127 25358:25360"
  # FWHOST[udp2]="3659 14000:14016 22990:23006 25200:25300 25358:25360"
  # /bin/echo " Variables Set,"


  start


  # iptables-save | tee /etc/iptables/rules.v4
  #echo 1 > /proc/sys/net/ipv4/ip_forward
  sed -i 's/#net.ipv4.ip_forward=0/#net.ipv4.ip_forward=1/g' /etc/sysctl.conf
  sysctl -p 
  printf "Done\n"
  printf "Installation almost complete. Proceed to connect to https://10.0.1.1/ in your browser to continue with the setup of the router.\n"


fi