#!/bin/bash
# printf '%s ' 'Which fruit do you like most?'
# read -${BASH_VERSION+e}r fruitfruit=$1
#$1 is always the action. The args can change for any of the other commands as none will ever run simultaneously. 
action=$1
case $action in
    nic_cards)
        
        ;;
    net_rate)
        intervalo=1
        info="/sys/class/net/"
        interface=$2
        # cd $info
        rx1=`cat $info$interface/statistics/rx_bytes`
        tx1=`cat $info$interface/statistics/tx_bytes`
        `sleep $((intervalo))s`
        rx2=`cat $info$interface/statistics/rx_bytes`
        tx2=`cat $info$interface/statistics/tx_bytes`
        g=$((($rx2-$rx1)/($intervalo)))
        f=$((($tx2-$tx1)/($intervalo)))
        printf '{"iface":"%s","tx":%s,"rx":%s}\n' "$interface" "$f" "$g" 
        ;;
    nat_setup)
        WAN=$2
        LAN=$3
        LAN_IP_RANGE=$4

        iptables --flush
        iptables -A FORWARD -o $WAN -i $LAN -s LAN_IP_RANGE/24 -m conntrack --ctstate NEW -j ACCEPT
        iptables -A FORWARD -m conntrack --ctstate ESTABLISHED,RELATED -j ACCEPT
        iptables -t nat -F POSTROUTING
        iptables -t nat -A POSTROUTING -o $WAN -j MASQUERADE

        iptables-save | tee /etc/iptables/rules.v4
        current = pwd
        echo "$current/"
        ;;
    apache2_setup)
      # update sites-available and sites-available permissions
      # server to replace  001-router
      printf '%s ' 'Provide a directory for the web interfaces DocumentRoot'
      read -${BASH_VERSION+e}r directory
      printf '%s ' 'Provide a port for the web interface'
      read -${BASH_VERSION+e}r port
      apt-get install apache2
      touch /etc/apache2/sites-available/001-router.conf
      cat >/etc/apache2/sites-available/001-router.conf <<EOL
Listen $port
<VirtualHost *:$port>
  ServerName router
  ServerAlias router

  DocumentRoot $directory

  <Directory $directory/>
    Options -Indexes +FollowSymLinks +MultiViews
    AllowOverride All
    Require all granted
    Order allow,deny
    allow from 127.0.0.0/8
    allow from 10.0.1.0/8
    ErrorDocument 403 "ya right buddy i dont think so"
  </Directory>
</VirtualHost>
EOL
      # $config > /etc/apache2/sites-available/001-router.conf
      a2ensite 001-router.conf
      cd /etc/apache2
      chgrp -R www-data sites-available
      chmod -R g+w sites-available
      chgrp -R www-data sites-enabled
      chmod -R g+w sites-enabled
      ln -s /etc/apache2/mods-available/proxy.load /etc/apache2/mods-enabled
      ln -s /etc/apache2/mods-available/proxy_http.load /etc/apache2/mods-enabled
      /etc/init.d/apache2 restart
      ;;
    dnsmasq_setup)
      #copy
      apt-get install dnsmasq
      ;;
    *)
      echo "You need to provide an action"
      ;;  
esac