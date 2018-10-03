#!/system/bin/sh

ss_switch=$1
ss_config=$2

lan_list=(
0.0.0.0/8
10.0.0.0/8
100.64.0.0/10
127.0.0.0/8
169.254.0.0/16
172.16.0.0/12
192.0.0.0/29
192.0.2.0/24
192.88.99.0/24
192.168.0.0/16
198.18.0.0/15
198.51.100.0/24
203.0.113.0/24
224.0.0.0/3
255.255.255.255/32
)

ss_nat()
(
iptables -t nat -N shadowsocks_pre
iptables -t nat -N shadowsocks_lan
iptables -t nat -N shadowsocks_out
iptables -t nat -N user_portal
iptables -t nat -N adblock_forward
iptables -t nat -N tor_forward
iptables -t nat -N proxy_forward
for i in ${lan_list[@]}
do
  iptables -t nat -A shadowsocks_lan -d $i -j ACCEPT
done
iptables -t nat -A shadowsocks_lan -m owner --uid-owner 0 -d $server -j ACCEPT
iptables -t nat -A shadowsocks_lan -m owner --uid-owner 3004 -j ACCEPT
for i in ${lan_list[@]}
do
  iptables -t nat -A shadowsocks_pre -d $i -j ACCEPT
done
iptables -t nat -A shadowsocks_pre -j user_portal
iptables -t nat -A shadowsocks_pre -j adblock_forward
iptables -t nat -A shadowsocks_pre -j tor_forward
iptables -t nat -A shadowsocks_pre -j proxy_forward
iptables -t nat -A proxy_forward -p tcp -j REDIRECT --to-ports 1024
if [[ $remote_dns_forward = 'on' && $remote_dns ]]; then
  iptables -t nat -A proxy_forward -p udp --dport 53 -j DNAT --to-destination $remote_dns
else
  iptables -t nat -A proxy_forward -p udp --dport 53 -j REDIRECT --to-ports 1053
fi
if [ $udp = 'drop' ]; then
  iptables -t nat -A proxy_forward -p udp ! --dport 53 -j DNAT --to-destination 127.0.0.1
fi
iptables -t nat -A PREROUTING -j shadowsocks_pre
iptables -t nat -A shadowsocks_out -j shadowsocks_lan
iptables -t nat -A shadowsocks_out -p tcp -m owner ! --uid-owner 0 -j adblock_forward
iptables -t nat -A shadowsocks_out -m owner ! --uid-owner $server_uid -j tor_forward
iptables -t nat -A shadowsocks_out -j proxy_forward
iptables -t nat -A OUTPUT -j shadowsocks_out
)

ss_mangle()
(
iptables -t mangle -N redsocks_pre
iptables -t mangle -N redsocks_lan
iptables -t mangle -N redsocks_out
for i in ${lan_list[@]}
do
  iptables -t mangle -A redsocks_lan -d $i -j ACCEPT
done
#iptables -t mangle -A redsocks_lan -p udp -m multiport --dport 67:69 -j ACCEPT
iptables -t mangle -A redsocks_pre -j redsocks_lan
iptables -t mangle -A redsocks_pre -p udp -j TPROXY --on-port 1024 --on-ip 0.0.0.0 --tproxy-mark 0x2333/0x2333
iptables -t mangle -A PREROUTING -j redsocks_pre
iptables -t mangle -A redsocks_out -j redsocks_lan
iptables -t mangle -A redsocks_out -m owner --uid-owner 0 -d $server -j ACCEPT
iptables -t mangle -A redsocks_out -m owner --uid-owner 3004 -j ACCEPT
iptables -t mangle -A OUTPUT -j redsocks_out

ip route add local 0/0 dev lo table 123
ip rule add fwmark 0x2333/0x2333 table 123
)

ss_filter()
(
iptables -t filter -N user_block
iptables -t filter -A INPUT -j user_block
if [ -z $icmp ]; then
  iptables -t filter -A INPUT -p icmp -j DROP
fi
)

ss_status()
(
iptables -vxn -t nat -L shadowsocks_pre --line-number
iptables -vxn -t nat -L shadowsocks_lan --line-number
iptables -vxn -t nat -L user_portal --line-number
iptables -vxn -t nat -L adblock_forward --line-number
iptables -vxn -t nat -L tor_forward --line-number
iptables -vxn -t nat -L proxy_forward --line-number
iptables -vxn -t mangle -L redsocks_pre --line-number
iptables -vxn -t mangle -L redsocks_lan --line-number
iptables -vxn -t mangle -L redsocks_out --line-number
iptables -vxn -t filter -L user_block --line-number
)

ss_stop()
(
ip rule del fwmark 0x2333/0x2333 table 123
ip route del local 0/0 dev lo table 123
#
iptables -t mangle -D PREROUTING -j redsocks_pre
iptables -t mangle -D OUTPUT -j redsocks_out
iptables -t nat -D PREROUTING -j shadowsocks_pre
iptables -t nat -D OUTPUT -j shadowsocks_out
iptables -t filter -D INPUT -j user_block
#
iptables -t mangle -F redsocks_pre
iptables -t mangle -F redsocks_lan
iptables -t mangle -F redsocks_out
iptables -t mangle -X redsocks_pre
iptables -t mangle -X redsocks_lan
iptables -t mangle -X redsocks_out
#
iptables -t nat -F shadowsocks_pre
iptables -t nat -F shadowsocks_lan
iptables -t nat -F shadowsocks_out
iptables -t nat -F user_portal
iptables -t nat -F adblock_forward
iptables -t nat -F tor_forward
iptables -t nat -F proxy_forward
iptables -t nat -X shadowsocks_pre
iptables -t nat -X shadowsocks_lan
iptables -t nat -X shadowsocks_out
iptables -t nat -X user_portal
iptables -t nat -X adblock_forward
iptables -t nat -X tor_forward
iptables -t nat -X proxy_forward
#
iptables -t filter -F user_block
iptables -t filter -X user_block
#
iptables -t filter -D INPUT -p icmp -j DROP 2> /dev/null
)

case $ss_switch in
start)
source $ss_config
if [ $? -ne 0 ]; then
  echo "读取脚本配置失败!"
  exit $?
fi
if [ $(cat /proc/sys/net/ipv4/ip_forward) -le 0 ]; then
  sysctl -w net.ipv4.ip_forward=1;
fi
fs=$(cat /proc/sys/net/ipv4/tcp_fastopen);
if [[ $tcp_fast_open = 'on' && $fs -le 0 ]]; then
  sysctl -w net.ipv4.tcp_fastopen=3
elif [[ -z $tcp_fast_open && $fs -gt 0 ]]; then
  sysctl -w net.ipv4.tcp_fastopen=0
fi
ss_nat
if [[ $udp = 'forward' || $udp = 'udp_over_tcp' ]]; then
  if [[ $(cat /proc/net/ip_tables_targets) = *"TPROXY"* ]]; then
    ss_mangle
  else
    echo "内核不支持TPROXY模块! 请留意UDP流量消耗情况，或者禁用UDP"
  fi
fi
ss_filter
;;
status)
ss_status
;;
stop)
ss_stop
;;
esac