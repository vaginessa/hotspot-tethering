<?php

$mangle = array(
    //mangle表
    'iptables -t mangle -N redsocks_pre',
    'iptables -t mangle -N redsocks_lan',
    'iptables -t mangle -N redsocks_out',
    'iptables -t mangle -A redsocks_lan -d 0/8 -j ACCEPT',
    'iptables -t mangle -A redsocks_lan -d 10/8 -j ACCEPT',
    'iptables -t mangle -A redsocks_lan -d 127/8 -j ACCEPT',
    'iptables -t mangle -A redsocks_lan -d 169.254/16 -j ACCEPT',
    'iptables -t mangle -A redsocks_lan -d 172.168/12 -j ACCEPT',
    'iptables -t mangle -A redsocks_lan -d 192.168/16 -j ACCEPT',
    'iptables -t mangle -A redsocks_lan -d 224/4 -j ACCEPT',
    'iptables -t mangle -A redsocks_lan -d 240/4 -j ACCEPT',
    'iptables -t mangle -A redsocks_pre -j redsocks_lan',
    'iptables -t mangle -A redsocks_pre -p udp -j TPROXY --on-port 1024 --on-ip 0.0.0.0 --tproxy-mark 0x2333/0x2333',
    // 新建路由表 123，将所有数据包发往 loopback 网卡
    'ip route add local 0/0 dev lo table 123',
    // 添加路由策略，让所有经 TPROXY 标记的 0x2333/0x2333 udp 数据包使用路由表 123
    'ip rule add fwmark 0x2333/0x2333 table 123',
    'iptables -t mangle -A redsocks_out -j redsocks_lan',
    'iptables -t mangle -A redsocks_out -m owner --uid-owner 3004 -j ACCEPT',
    'iptables -t mangle -A redsocks_out -p udp -j MARK --set-mark 0x2333/0x2333',
    'iptables -t mangle -A PREROUTING -j redsocks_pre',
    'iptables -t mangle -A OUTPUT -j redsocks_out'
);
$nat = array(
    //nat表
    'iptables -t nat -N pre_forward',
    'iptables -t nat -N user_portal',
    'iptables -t nat -N out_lan',
    'iptables -t nat -N out_forward',
    'iptables -t nat -N koolproxy_forward',
    //本机发出同意
    'iptables -t nat -A out_lan -d 127/8 -j ACCEPT',
    'iptables -t nat -A out_lan -m owner --uid-owner 3004 -j ACCEPT',
    //'iptables -t nat -A out_lan -p tcp -m owner ! --uid-owner $(id -u) -j koolproxy_forward',
    'iptables -t nat -A out_lan -p tcp -m owner ! --uid-owner 0 -j koolproxy_forward',
    'iptables -t nat -A out_lan -j out_forward',
    //流量重定向
    'iptables -t nat -A out_forward -p tcp -j REDIRECT --to-ports 1024',
    'iptables -t nat -A out_forward -p udp --dport 53 -j REDIRECT --to-ports 1053',
    'iptables -t nat -A OUTPUT -j out_lan',
    //路由前的流量
    'iptables -t nat -A pre_forward -j user_portal',
    'iptables -t nat -A pre_forward -j koolproxy_forward',
    'iptables -t nat -A pre_forward -j out_forward',
    'iptables -t nat -A PREROUTING -s 192.168/16 -j pre_forward'
);
$filter = array(
    //filter表
    'iptables -t filter -N user_block',
    //流量流入
    'iptables -t filter -A INPUT -j user_block'
    //限制规则
    //'iptables -t filter -A INPUT -p tcp -m time --timestart 12:00:00 --timestop 07:00:00 -j REJECT --reject-with icmp-port-unreachable'
    
);
$stop_iptables = array(
    'ip rule del fwmark 0x2333/0x2333 table 123',
    'ip route del local 0/0 dev lo table 123',
    'iptables -t mangle -D PREROUTING -j redsocks_pre',
    'iptables -t mangle -D OUTPUT -j redsocks_out',
    'iptables -t nat -D PREROUTING -s 192.168/16 -j pre_forward',
    'iptables -t nat -D OUTPUT -j out_lan',
    'iptables -t filter -D INPUT -j user_block',
    'iptables -t filter -D OUTPUT -p icmp -j DROP',
    'iptables -t mangle -F redsocks_pre',
    'iptables -t mangle -F redsocks_out',
    'iptables -t mangle -F redsocks_lan',
    'iptables -t mangle -X redsocks_pre',
    'iptables -t mangle -X redsocks_out',
    'iptables -t mangle -X redsocks_lan',
    'iptables -t nat -F pre_forward',
    'iptables -t nat -F user_portal',
    'iptables -t nat -F out_lan',
    'iptables -t nat -F koolproxy_forward',
    'iptables -t nat -F out_forward',
    'iptables -t nat -X pre_forward',
    'iptables -t nat -X user_portal',
    'iptables -t nat -X out_lan',
    'iptables -t nat -X koolproxy_forward',
    'iptables -t nat -X out_forward',
    'iptables -t filter -F user_block',
    'iptables -t filter -X user_block'
);

$status_iptables = array(
    //echo -e 'nat表pre_forward链:'
    'iptables -vxn -t nat -L pre_forward --line-number',
    //echo -e 'nat表user_portal链:'
    'iptables -vxn -t nat -L user_portal --line-number',
    //echo -e 'nat表out_lan链:'
    'iptables -vxn -t nat -L out_lan --line-number',
    //echo -e 'nat表koolproxy_forward链:'
    'iptables -vxn -t nat -L koolproxy_forward --line-number',
    //echo -e 'nat表out_forward链:'
    'iptables -vxn -t nat -L out_forward --line-number',
    //echo -e 'filter表user_block链:'
    'iptables -vxn -t filter -L user_block --line-number',
    //echo -e 'mangle表redsocks_pre链:'
    'iptables -vxn -t mangle -L redsocks_pre --line-number',
    //echo -e 'mangle表redsocks_lan链:'
    'iptables -vxn -t mangle -L redsocks_lan --line-number',
    //echo -e 'mangle表redsocks_out链:'
    'iptables -vxn -t mangle -L redsocks_out --line-number'
);

$status_binary = array(
    'dnsforwarder',
    'gost',
    'redsocks',
    'tproxy',
    'GoQuiet',
    'kcptun',
    'obfs-local',
    'ss-redir',
    'ss-local',
    'libproxychains4.so'
);

function file_chmod($tmp_file) { 
  if (chmod($tmp_file, 0700)) { 
      exec("su -c $tmp_file", $output, $return_val);
      foreach ($output as $val) {
          echo "$val<br>";
      }
  } else {
    die('设置文件权限失败！');
  }
}

function iptables_start($mangle, $nat, $filter, $server, $wifi, $icmp, $udp) {
    //写出执行脚本
    $tmp_file=sys_get_temp_dir().'/iptables_add.sh';
    @unlink($tmp_file);
    
    //检测tproxy与udp转发设置
    if ($udp=='forward'||$udp=='udp_over_tcp') { 
      if (stripos(shell_exec('su -c cat /proc/net/ip_tables_targets'),'TPROXY')) { 
        $tproxy=true;
      }
    }
    
    //开启转发了吗？
    if (file_get_contents('/proc/sys/net/ipv4/ip_forward') <= 0) { 
      shell_exec('su -c sysctl -w net.ipv4.ip_forward=1');
    }
    
    //先修改压入数据
    for ($i = 0; $i < count($nat); $i++) { 
        $natr[]=$nat[$i];
        if ($i==5) { //从第五个开始吧
           $natr[]="iptables -t nat -A out_lan -d $server -j ACCEPT";
           if ($udp=='drop') { 
             $natr[]='iptables -t nat -A out_lan -p udp ! --dport 53 -j DNAT --to-destination 127.0.0.1';
           }
           if ($wifi=='on') {
             $natr[]='iptables -t nat -A out_lan -s 192.168.0.0/16 -j ACCEPT';
             $natr[]='iptables -t nat -A out_lan -d 192.168.0.0/16 -j ACCEPT';
           } else { 
             $natr[]='iptables -t nat -A out_lan -p tcp -d 192.168.0.0/16 -j ACCEPT';
           }
        }
    }
    //写入nat表
    foreach ($natr as $val) { 
      file_put_contents($tmp_file, $val.PHP_EOL, FILE_APPEND | LOCK_EX);
    }
    
    if ($udp=='drop') { 
      file_put_contents($tmp_file, 'iptables -t nat -A pre_forward -p udp -j DNAT --to-destination 127.0.0.1'.PHP_EOL, FILE_APPEND | LOCK_EX);
    }
    
    //限制icmp
    if ($icmp!='on') {
      file_put_contents($tmp_file, 'iptables -t filter -A OUTPUT -p icmp -j DROP'.PHP_EOL, FILE_APPEND | LOCK_EX);
    }
    
    //支持tproxy与开启了udp转发
   if ($tproxy===true) {
      if ($wifi=='on') {
        $mangle[]='iptables -t mangle -I redsocks_lan 6 -s 192.168.0.0/16 -j ACCEPT';
      }
      $mangle[]="iptables -t mangle -I redsocks_lan 4 -d $server -j ACCEPT";
      foreach ($mangle as $val) {
        file_put_contents($tmp_file, $val . PHP_EOL, FILE_APPEND | LOCK_EX);
      }
   }
    
    //写入filter表
    foreach ($filter as $val) {
      file_put_contents($tmp_file, $val . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
    file_chmod($tmp_file);
    return $tproxy;
} //


//停止规则和模块
function iptables_stop($stop_iptables) { 
    $tmp_file = sys_get_temp_dir()."/iptables_del.sh";
    @unlink($tmp_file);
    foreach ($stop_iptables as $val) {
        file_put_contents($tmp_file, $val.PHP_EOL, FILE_APPEND | LOCK_EX);
    }
    file_chmod($tmp_file);
}

?>

