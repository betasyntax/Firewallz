#!/bin/sh
#Set default vars

/bin/echo "0" > /proc/sys/net/ipv4/ip_forward
clean
$IPTABLES -A INPUT -i lo -j ACCEPT
$IPTABLES -A OUTPUT -o lo -j ACCEPT