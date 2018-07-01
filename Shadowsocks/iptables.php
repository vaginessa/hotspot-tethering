<?php
require '../tools/Certified.php';
require "../tools/busybox.php";

$mangle = array(
    //mangle表
    "iptables -t mangle -N redsocks2_pre",
    "iptables -t mangle -N redsocks2_lan",
    "iptables -t mangle -N redsocks2_out",
    "iptables -t mangle -A redsocks2_lan -d 0/8 -j ACCEPT",
    "iptables -t mangle -A redsocks2_lan -d 10/8 -j ACCEPT",
    "iptables -t mangle -A redsocks2_lan -d 127/8 -j ACCEPT",
    "iptables -t mangle -A redsocks2_lan -d 169.254/16 -j ACCEPT",
    "iptables -t mangle -A redsocks2_lan -d 172.168/12 -j ACCEPT",
    "iptables -t mangle -A redsocks2_lan -d 192.168/16 -j ACCEPT",
    "iptables -t mangle -A redsocks2_lan -d 224/4 -j ACCEPT",
    "iptables -t mangle -A redsocks2_lan -d 240/4 -j ACCEPT",
    "iptables -t mangle -A redsocks2_pre -j redsocks2_lan",
    "iptables -t mangle -A redsocks2_pre -p udp -j TPROXY --on-port 1027 --on-ip 0.0.0.0 --tproxy-mark 0x2333/0x2333",
    // 新建路由表 123，将所有数据包发往 loopback 网卡
    "ip route add local 0/0 dev lo table 123",
    // 添加路由策略，让所有经 TPROXY 标记的 0x2333/0x2333 udp 数据包使用路由表 123
    "ip rule add fwmark 0x2333/0x2333 table 123",
    "iptables -t mangle -A redsocks2_out -j redsocks2_lan",
    "iptables -t mangle -A redsocks2_out -m owner --uid-owner 3004 -j ACCEPT",
    "iptables -t mangle -A redsocks2_out -p udp -j MARK --set-mark 0x2333/0x2333",
    "iptables -t mangle -A PREROUTING -j redsocks2_pre",
    "iptables -t mangle -A OUTPUT -j redsocks2_out"
);
$nat = array(
    //nat表
    "iptables -t nat -N pre_forward",
    "iptables -t nat -N user_portal",
    "iptables -t nat -N out_lan",
    "iptables -t nat -N out_forward",
    "iptables -t nat -N koolproxy_forward",
    //本机发出同意
    "iptables -t nat -A out_lan -d 127/8 -j ACCEPT",
    "iptables -t nat -A out_lan -p tcp -d 192.168/16 -j ACCEPT",
    "iptables -t nat -A out_lan -m owner --uid-owner 3004 -j ACCEPT",
    //"iptables -t nat -A out_lan -p tcp -m owner ! --uid-owner $(id -u) -j koolproxy_forward",
    "iptables -t nat -A out_lan -p tcp -m owner ! --uid-owner 0 -j koolproxy_forward",
    "iptables -t nat -A out_lan -j out_forward",
    //流量重定向
    "iptables -t nat -A out_forward -p tcp -j REDIRECT --to-ports 1024",
    "iptables -t nat -A out_forward -p udp --dport 53 -j REDIRECT --to-ports 1053",
    "iptables -t nat -A OUTPUT -j out_lan",
    //路由前的流量
    "iptables -t nat -A pre_forward -j user_portal",
    "iptables -t nat -A pre_forward -j koolproxy_forward",
    "iptables -t nat -A pre_forward -j out_forward",
    "iptables -t nat -A PREROUTING -s 192.168/16 -j pre_forward"
);
$filter = array(
    //filter表
    "iptables -t filter -N user_block",
    //流量流入
    "iptables -t filter -A INPUT -j user_block"
    //限制规则
    //"iptables -t filter -A INPUT -p tcp -m time --timestart 12:00:00 --timestop 07:00:00 -j REJECT --reject-with icmp-port-unreachable"
    
);
$stop_iptables = array(
    "ip rule del fwmark 0x2333/0x2333 table 123",
    "ip route del local 0/0 dev lo table 123",
    "iptables -t mangle -D PREROUTING -j redsocks2_pre",
    "iptables -t mangle -D OUTPUT -j redsocks2_out",
    "iptables -t mangle -F redsocks2_pre",
    "iptables -t mangle -X redsocks2_pre",
    "iptables -t mangle -F redsocks2_out",
    "iptables -t mangle -X redsocks2_out",
    "iptables -t mangle -F redsocks2_lan",
    "iptables -t mangle -X redsocks2_lan",
    "iptables -t nat -D PREROUTING -s 192.168/16 -j pre_forward",
    "iptables -t nat -D OUTPUT -j out_lan",
    "iptables -t nat -F pre_forward",
    "iptables -t nat -X pre_forward",
    "iptables -t nat -F user_portal",
    "iptables -t nat -X user_portal",
    "iptables -t nat -F out_lan",
    "iptables -t nat -X out_lan",
    "iptables -t nat -F koolproxy_forward",
    "iptables -t nat -X koolproxy_forward",
    "iptables -t nat -F out_forward",
    "iptables -t nat -X out_forward",
    "iptables -t filter -D INPUT -j user_block",
    "iptables -t filter -F user_block",
    "iptables -t filter -X user_block"
);
$status_iptables = array(
    //echo -e "nat表pre_forward链:"
    "iptables -vxn -t nat -L pre_forward --line-number",
    //echo -e "nat表user_portal链:"
    "iptables -vxn -t nat -L user_portal --line-number",
    //echo -e "nat表out_lan链:"
    "iptables -vxn -t nat -L out_lan --line-number",
    //echo -e "nat表koolproxy_forward链:"
    "iptables -vxn -t nat -L koolproxy_forward --line-number",
    //echo -e "nat表out_forward链:"
    "iptables -vxn -t nat -L out_forward --line-number",
    //echo -e "filter表user_block链:"
    "iptables -vxn -t filter -L user_block --line-number",
    //echo -e "mangle表redsocks2_pre链:"
    "iptables -vxn -t mangle -L redsocks2_pre --line-number",
    //echo -e "mangle表redsocks2_lan链:"
    "iptables -vxn -t mangle -L redsocks2_lan --line-number",
    //echo -e "mangle表redsocks2_out链:"
    "iptables -vxn -t mangle -L redsocks2_out --line-number"
);
$status_binary = array(
    "ss-local",
    "obfs-local",
    "overture",
    "gost",
    "redsocks2",
    "tproxy",
    "GoQuiet",
    "kcptun"
);

clearstatcache();

function iptables_start($mangle, $nat, $filter, $stop_iptables, $status_binary, $server, $udp) {
    $tmp_file = sys_get_temp_dir()."/iptables_add.sh";
    if (is_file($tmp_file)) { 
    unlink("$tmp_file");
    //支持tproxy吗？
    }
    if (stripos(shell_exec('su -c cat /proc/net/ip_tables_targets') , 'TPROXY')) { 
    $tproxy = true;
    //开启转发了吗？
    }
    if (stripos(shell_exec('su -c cat /proc/sys/net/ipv4/ip_forward') , '0')) { 
    shell_exec('su -c echo 1 > /proc/sys/net/ipv4/ip_forward');
    //sysctl -w net.ipv4.ip_forward=1
    }
    //直接在脚本前面追加清除规则和关闭模块
    iptables_stop($stop_iptables, $status_binary, false);    
    //支持tproxy与开启udp
    if ($tproxy and $udp) {
        if (is_array($mangle) || is_object($mangle)) {
            foreach ($mangle as $value) {
                file_put_contents($tmp_file, $value . PHP_EOL, FILE_APPEND | LOCK_EX);
            }
            if ($server) {
             file_put_contents($tmp_file, "iptables -t mangle -A redsocks2_lan -d $server -j ACCEPT".PHP_EOL, FILE_APPEND | LOCK_EX);
            }
        }
    }
    if (is_array($nat) || is_object($nat)) {
        foreach ($nat as $value) {
        /*
            if (stripos($value, '$(id -u)')) {
                //这里执行shell (id -u)有换行符，浏览器输出正常，就是iptables异常，差点气死人！！！
                $value = str_replace('$(id -u)', shell_exec('id -u') , $value);
                //这里去除换行符，就正常回来了。
                $value = str_replace(PHP_EOL, '', $value);
            }
            */
            file_put_contents($tmp_file, $value . PHP_EOL, FILE_APPEND | LOCK_EX);
        }
        if (empty($udp) or empty($tproxy)) {
        file_put_contents($tmp_file, "iptables -t nat -A out_lan -p udp ! --dport 53 -j DNAT --to-destination 127.0.0.1".PHP_EOL, FILE_APPEND | LOCK_EX); 
        }
        if ($server) {
             file_put_contents($tmp_file, "iptables -t nat -I out_lan 3 -d $server -j ACCEPT".PHP_EOL, FILE_APPEND | LOCK_EX);
        }
    }
    foreach ($filter as $value) {
        file_put_contents($tmp_file, $value . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
        chmod($tmp_file, 0700);
        shell_exec("su -c $tmp_file");
} //


//停止规则和模块
function iptables_stop($stop_iptables, $status_binary, $file_name) {
    $pkill = busybox_check("pkill");
    $file_name ? $file='iptables_del.sh' : $file='iptables_add.sh';
    $tmp_file = sys_get_temp_dir()."/$file";
    if (is_file($tmp_file) and $file_name === true) unlink("$tmp_file");
    foreach ($status_binary as $value) {
        file_put_contents($tmp_file, "$pkill $value". PHP_EOL, FILE_APPEND | LOCK_EX);
    }
    foreach ($stop_iptables as $value) {
        file_put_contents($tmp_file, $value . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
    if (!is_executable($tmp_file) or file_exists($tmp_file) and $file_name === true)
    {
    chmod($tmp_file, 0700);
    shell_exec("su -c $tmp_file");
    } else {
    die("没有写出删除脚本!");
    }
}

?>

