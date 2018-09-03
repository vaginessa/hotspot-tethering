<?php 
session_start();
$stime = microtime(true);
$return = $_REQUEST['token'] === $_SESSION['token'] ? true : false;
unset($_SESSION['token']);
session_write_close();
echo <<< EOF
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="refresh" content="5;url=../Admin/" /> 
<title>执行结束</title>
</head>
<body>
EOF;
if(!$return)die('请勿重复提交表单');
require 'iptables.php';
require '../Admin/main.class.php';

//命令查找
$pkill = toolbox_check() [1] . ' pkill';
//移动模块文件
if (is_array($status_binary) || is_object($status_binary)) {
    foreach ($status_binary as $val) {
        $binary_file = sys_get_temp_dir() . "/$val";
        if (!is_executable($binary_file) && file_exists($val)) {
            copy($val, $binary_file);
            chmod($binary_file, 0755);
        }
    }
}
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $shadowsocks = test_input($_GET['shadowsocks']);
    $name = test_input($_GET['name']);
    $server = test_input($_GET['server']);
    $server_port = test_input($_GET['server_port']);
    $password = test_input($_GET['password']);
    $method = test_input($_GET['method']);
    $route = test_input($_GET['route']);
    $remote_dns = test_input($_GET['remote_dns']);
    $remote_dns_forward = test_input($_GET['remote_dns_forward']);
    $tcp_fast_open = test_input($_GET['tcp_fast_open']);
    $wifi = test_input($_GET['wifi']);
    $icmp = test_input($_GET['icmp']);
    $udp = test_input($_GET['udp']);
    $gost_server = test_input($_GET['gost_server']);
    $gost_server_port = test_input($_GET['gost_server_port']);
    $gost_username = test_input($_GET['gost_username']);
    $gost_password = test_input($_GET['gost_password']);
    $plugin = test_input($_GET['plugin']);
    $obfs = test_input($_GET['obfs']);
    $obfs_http_method = test_input($_GET['obfs_http_method']);
    $obfs_url = test_input($_GET['obfs_url']);
    $obfs_host = test_input($_GET['obfs_host']);
    $remotePort = test_input($_GET['remotePort']);
    $remoteHost = test_input($_GET['remoteHost']);
    $ServerName = test_input($_GET['ServerName']);
    $Key = test_input($_GET['Key']);
    $TicketTimeHint = test_input($_GET['TicketTimeHint']);
    $Browser = test_input($_GET['Browser']);
    $kcptun_remoteaddr = test_input($_GET['kcptun_remoteaddr']);
    $kcptun_key = test_input($_GET['kcptun_key']);
    $kcptun_crypt = test_input($_GET['kcptun_crypt']);
    $kcptun_mode = test_input($_GET['kcptun_mode']);
    $kcptun_conn = test_input($_GET['kcptun_conn']);
    $kcptun_autoexpire = test_input($_GET['kcptun_autoexpire']);
    $kcptun_scavengettl = test_input($_GET['kcptun_scavengettl']);
    $kcptun_mtu = test_input($_GET['kcptun_mtu']);
    $kcptun_sndwnd = test_input($_GET['kcptun_sndwnd']);
    $kcptun_rcvwnd = test_input($_GET['kcptun_rcvwnd']);
    $kcptun_datashard = test_input($_GET['kcptun_datashard']);
    $kcptun_parityshard = test_input($_GET['kcptun_parityshard']);
    $kcptun_dscp = test_input($_GET['kcptun_dscp']);   
    $kcptun_nocomp = test_input($_GET['kcptun_nocomp']);   
    $proxychains_type = test_input($_GET['proxychains_type']);
    $proxychains_address = test_input($_GET['proxychains_address']);
    $proxychains_port = test_input($_GET['proxychains_port']);
    $proxychains_username = test_input($_GET['proxychains_username']);
    $proxychains_password = test_input($_GET['proxychains_password']);
}
//服务器是否ss://链接然后解析
if (strpos($server, 'ss://') !== false) {
    if (stripos($server, '@') !== false) {
        list($name, $server, $server_port, $password, $method) = android_share_input($server);
    } else {
        list($name, $server, $server_port, $password, $method) = share_input($server);
    }
}
//shadowsocks配置写出
function config_json($server, $server_port, $local_port, $password, $method, $plugin, $plugin_opts) {
    $config = array(
            'server' => $server,
            'server_port' => (int)$server_port, //使用(int)将字符串转换成数字类型
            'local_port' => (int)$local_port,
            'password' => $password,
            'method' => $method,
            'user' => 3004,
            'mode' => 'tcp_and_udp',
            'local_address' => '0.0.0.0'
        );
    if ($plugin != '' && $plugin_opts != '') {
        $config['plugin'] = sys_get_temp_dir() . "/$plugin";
        $config['plugin_opts'] = $plugin_opts;
    }
    $arr = json_encode($config);
    file_put_contents('shadowsocks.conf', $arr);
}

$fs=@file_get_contents('/proc/sys/net/ipv4/tcp_fastopen');
if ($tcp_fast_open=='on'&&$fs<=0) {
  shell_exec('su -c sysctl -w net.ipv4.tcp_fastopen=3');
  echo "[TCP Fast Open]：√ <br />";
} elseif ($tcp_fast_open!='on'&&$fs>0) {
  shell_exec('su -c sysctl -w net.ipv4.tcp_fastopen=0');
  echo "[TCP Fast Open]：× <br />";
}

//关闭shadowsocks
if (empty($_REQUEST['shadowsocks']) && $server && $server_port && $password && $method) {
    //创建停止运行脚本
    $stop_file = sys_get_temp_dir() . '/stop.sh';
    @unlink($stop_file);
    foreach ($status_binary as $val) {
        file_put_contents($stop_file, "$pkill $val" . PHP_EOL, FILE_APPEND);
    }
    file_put_contents($stop_file, 'kill '.file_get_contents('daemon.pid').PHP_EOL, FILE_APPEND);
    @unlink(sys_get_temp_dir().'/daemon.sh');
    //执行关闭模块
    file_chmod($stop_file);
    //执行关闭iptables规则
    iptables_stop($stop_iptables);
    echo "关闭Shadowsocks<br />";
}
//启动shadowsocks
if ($shadowsocks == 'on' and $server and $server_port and $password and $method) {
    //创建开始运行脚本
    $start_file = sys_get_temp_dir() . '/start.sh';
    @unlink($start_file);
    //服务器是否为域名网址地址？
    function jx_server($server) {
        if (preg_match('/[a-z]+/i', $server) > 0) {
            $server2 = gethostbyname($server);
            if ($server == $server2) {
                die('域名解析失败!');
            } else {
                $server = $server2;
            }
        }
        return $server;
    }
    $server=jx_server($server);
    $gost_server=jx_server($gost_server);
    //写出记录配置
    $data = "shadowsocks=$shadowsocks" . PHP_EOL . "name=$name" . PHP_EOL . "server=$server" . PHP_EOL . "server_port=$server_port" . PHP_EOL . "password=$password" . PHP_EOL . "method=$method" . PHP_EOL . "route=$route" . PHP_EOL . "remote_dns=$remote_dns" . PHP_EOL . "remote_dns_forward=$remote_dns_forward" . PHP_EOL . "wifi=$wifi" . PHP_EOL . "icmp=$icmp" . PHP_EOL . "udp=$udp" . PHP_EOL . "gost_server=$gost_server" . PHP_EOL . "gost_server_port=$gost_server_port" . PHP_EOL . "gost_username=$gost_username" . PHP_EOL . "gost_password=$gost_password" . PHP_EOL . "plugin=$plugin" . PHP_EOL . "obfs=$obfs" . PHP_EOL . "obfs_http_method=$obfs_http_method" . PHP_EOL . "obfs_url=$obfs_url" . PHP_EOL . "obfs_host=$obfs_host" . PHP_EOL . "remotePort=$remotePort" . PHP_EOL . "remoteHost=$remoteHost" . PHP_EOL . "ServerName=$ServerName" . PHP_EOL . "Key=$Key" . PHP_EOL . "TicketTimeHint=$TicketTimeHint" . PHP_EOL . "Browser=$Browser" . PHP_EOL . "kcptun_remoteaddr=$kcptun_remoteaddr" . PHP_EOL . "kcptun_key=$kcptun_key" . PHP_EOL . "kcptun_crypt=$kcptun_crypt" . PHP_EOL . "kcptun_mode=$kcptun_mode" . PHP_EOL . "kcptun_conn=$kcptun_conn" . PHP_EOL . "kcptun_autoexpire=$kcptun_autoexpire" . PHP_EOL . "kcptun_scavengettl=$kcptun_scavengettl" . PHP_EOL . "kcptun_mtu=$kcptun_mtu" . PHP_EOL . "kcptun_sndwnd=$kcptun_sndwnd" . PHP_EOL . "kcptun_rcvwnd=$kcptun_rcvwnd" . PHP_EOL . "kcptun_datashard=$kcptun_datashard" . PHP_EOL . "kcptun_parityshard=$kcptun_parityshard" . PHP_EOL . "kcptun_dscp=$kcptun_dscp" . PHP_EOL . "kcptun_nocomp=$kcptun_nocomp" . PHP_EOL . "proxychains_type=$proxychains_type" . PHP_EOL . "proxychains_address=$proxychains_address" . PHP_EOL . "proxychains_port=$proxychains_port" . PHP_EOL . "proxychains_username=$proxychains_username" . PHP_EOL . "proxychains_password=$proxychains_password" . PHP_EOL;
  file_put_contents('config.ini', $data);
  if ($udp == 'udp_over_tcp') { 
    //redsocks配置运行
    $binary = sys_get_temp_dir() . '/redsocks';
    $config = __DIR__ . '/redsocks.conf';
    file_put_contents($start_file, "$binary -c $config > /dev/null 2>&1 &" . PHP_EOL, FILE_APPEND);
    //gost配置运行
    $binary = sys_get_temp_dir() . '/gost';
    if ($gost_username and $gost_password) {
      $config = "$gost_username:$gost_password@$gost_server:$gost_server_port";
    } else {
      $config = "$gost_server:$gost_server_port";
    }
    file_put_contents($start_file, "$binary -L socks5://127.0.0.1:1027 -F socks5://127.0.0.1:1025 -F socks5://$config > /dev/null 2>&1 &" . PHP_EOL, FILE_APPEND);
  }
    /*
    //overture配置
    $binary = sys_get_temp_dir() . '/overture';
    $config = __DIR__ . '/overture.json';
    $obj = json_decode(file_get_contents($config));
    $obj->HostsFile = __DIR__ . '/hosts';
    $obj = json_encode($obj);
    file_put_contents('overture.json', $obj, LOCK_EX);
    file_put_contents($start_file, "$binary -c $config > /dev/null 2>&1 &" . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    //pdnsd配置
    $binary = sys_get_temp_dir() . '/pdnsd';
    $config = __DIR__ . '/pdnsd.conf';
    $r_c=file_get_contents($config);
    if (@unlink($config)===true) {  
      foreach (explode(PHP_EOL,$r_c) as $key) {
          $val = explode('=', $key);
           if($val[0]==' cache_dir') {
             $val[1]='"'.sys_get_temp_dir().'";';
           }
           if($val[0]=='	file') {
             $val[1]='"'.__DIR__.'/hosts";';
           }
           if($val[0]&&$val[1]) {
             $x="$val[0]=$val[1]";
           } else { 
             $x=$val[0];
           }
           if($x!="") {
             file_put_contents($config, $x.PHP_EOL, FILE_APPEND);
           }
       }
    }
    file_put_contents($start_file, "$binary -c $config > /dev/null 2>&1 &" . PHP_EOL, FILE_APPEND);    
    */
    //dnsforwarder配置
   if ($remote_dns_forward != 'on') {      
    $binary = sys_get_temp_dir() . '/dnsforwarder';
    $config = __DIR__ . '/dnsforwarder.config';
    $r_c=file_get_contents($config);
    if (@unlink($config)===true) {  
      foreach (explode(PHP_EOL,$r_c) as $key) {
          $val = explode(' ', $key);
           if($val[0]=='TCPGroup') {
             $val[1]=$remote_dns.' * no';
           }
           if($val[0]=='GroupFile') {
             $val[1]=__DIR__.'/china.txt';
           }
           if($val[0]=='Hosts') {
             $val[1]='file://'.__DIR__.'/hosts';
           }
           if($val[0]=='DomainStatisticTempletFile') {
             $val[1]=__DIR__.'/StatisticTemplate.html';
           }
           if($val[0]&&$val[1]) {
             $x="$val[0] $val[1]";
           } else { 
             $x=$val[0];
           }
           if($x!='') {
             file_put_contents($config, $x.PHP_EOL, FILE_APPEND);
           }
       }
    }
    file_put_contents($start_file, "$binary -f $config -q -d > /dev/null 2>&1 &".PHP_EOL, FILE_APPEND); 
    unset($remote_dns);
  }
    //写出守护脚本
  if ($remote_dns_forward != 'on') {      
    $daemon_file = sys_get_temp_dir() . '/daemon.sh';
    $daemon_log = __DIR__ . '/daemon.log';
    $data=str_replace('dirn',sys_get_temp_dir(),file_get_contents('daemon.sh'));
    $data=str_replace('dirp',__DIR__.'/daemon.pid',$data);
    file_put_contents($daemon_file, $data);
    if (!is_executable($daemon_file)) {
      chmod($daemon_file, 0755);
    }
    @unlink($daemon_log);
    file_put_contents($start_file, "$daemon_file >> $daemon_log 2>&1 &".PHP_EOL, FILE_APPEND); 
  }
    //kcptun_tun插件
    if ($plugin == 'kcptun' and $kcptun_remoteaddr) {
        if (empty($kcptun_remoteaddr)) $kcptun_remoteaddr = "$server:29900";
        if (empty($kcptun_key)) $kcptun_key = "it's a secrect";
        if (empty($kcptun_crypt)) $kcptun_crypt = 'aes';
        if (empty($kcptun_mode)) $kcptun_mode = 'fast';
        if (empty($kcptun_conn)) $kcptun_conn = 1;
        if (empty($kcptun_autoexpire)) $kcptun_autoexpire = 0;
        if (empty($kcptun_scavengettl)) $kcptun_scavengettl = 600;
        if (empty($kcptun_mtu)) $kcptun_mtu = 1350;
        if (empty($kcptun_sndwnd)) $kcptun_sndwnd = 128;
        if (empty($kcptun_rcvwnd)) $kcptun_rcvwnd = 512;
        if (empty($kcptun_datashard)) $kcptun_datashard = 10;
        if (empty($kcptun_parityshard)) $autoexpire = 3;
        if (empty($kcptun_dscp)) $kcptun_dscp = 0;
        $binary = sys_get_temp_dir() . '/kcptun';
        $config = "-l 127.0.0.1:1026 -r $kcptun_remoteaddr --key $kcptun_key --crypt $kcptun_crypt -mode $kcptun_mode -conn $kcptun_conn -autoexpire $kcptun_autoexpire -scavengettl $kcptun_scavengettl -mtu $kcptun_mtu -sndwnd $kcptun_sndwnd -rcvwnd $kcptun_rcvwnd -datashard $kcptun_datashard -parityshard $kcptun_parityshard -dscp $kcptun_dscp $kcptun_nocomp --quiet";
        file_put_contents($start_file, "$binary $config > /dev/null 2>&1 &" . PHP_EOL, FILE_APPEND);
    }
    //GoQuiet插件
  if ($plugin == 'GoQuiet' and $ServerName and $Key and $TicketTimeHint and $Browser) {
    $binary = sys_get_temp_dir() . '/GoQuiet';
    $config = __DIR__ . '/GoQuiet.json';
    if (empty($remotePort)) $remotePort = 443;
    if (empty($remoteHost)) $remoteHost = $server;
    $obj = json_decode(file_get_contents($config));
    $obj->ServerName = $ServerName;
    $obj->Key = $Key;
    $obj->TicketTimeHint = $TicketTimeHint;
    $obj->Browser = $Browser;
    $obj = json_encode($obj, JSON_NUMERIC_CHECK); //检查数字类型防止变成字符型
    file_put_contents($config, $obj);
    file_put_contents($start_file, "$binary -s $remoteHost -p $remotePort -l 1026 -c $config > /dev/null 2>&1 &" . PHP_EOL, FILE_APPEND);
  }
    //shadowsocks+插件配置
    if ($udp == 'udp_over_tcp') {
      $binary = sys_get_temp_dir() . '/ss-local --acl '.__DIR__."/$route -f ".__DIR__ . '/ss-deamon.pid';
      $local_port = 1025;
    } else {
      $binary = sys_get_temp_dir() . '/ss-redir --acl '.__DIR__."/$route -f ".__DIR__ . '/ss-deamon.pid';
      $local_port = 1024;
    }
    $config = __DIR__ . '/shadowsocks.conf';
    $iserver=$server; //iptables使用的
    if ($plugin != 'off' && $plugin != 'proxychains') { //不关闭插件也不是代理链
      if ($plugin == 'obfs-local' && $obfs && $obfs_host) {
         if ($obfs == 'http') {
           $plugin_opts = "obfs=$obfs;http-method=$obfs_http_method;obfs-uri=$obfs_url;obfs-host=$obfs_host";
         } else { 
           $plugin_opts = "obfs=$obfs;obfs-host=$obfs_host";
         }
      } else {
         $server='127.0.0.1';
         $server_port=1026;
      }
    }    
    config_json($server, $server_port, $local_port, $password, $method, $plugin, $plugin_opts);
   if ($plugin == 'proxychains') { //配置代理链
      file_put_contents($start_file, 'env PROXYCHAINS_CONF_FILE='.__DIR__.'/proxychains.conf LD_PRELOAD='.sys_get_temp_dir().'/libproxychains4.so '."$binary -c $config > /dev/null 2>&1 &" . PHP_EOL, FILE_APPEND);
      file_put_contents('proxychains.conf', 'strict_chain'.PHP_EOL.'[ProxyList]'.PHP_EOL.$proxychains_type.' '.$proxychains_address.' '.$proxychains_port.' '.$proxychains_username.' '.$proxychains_password.PHP_EOL);
   $iserver=$proxychains_address;
   } else {
     file_put_contents($start_file, "$binary -c $config > /dev/null 2>&1 &" . PHP_EOL, FILE_APPEND);
   }
    //执行开启模块
    file_chmod($start_file);
    //执行开启iptables
    $tp=iptables_start($mangle, $nat, $filter, $iserver, $remote_dns, $wifi, $icmp, $udp);
    if ($tp===true) {
      echo "[TPROXY]：√ <br />";
    }
    echo "开启Shadowsocks<br />";
} //
$etime = microtime(true); //获取程序执行结束的时间
$total = $etime - $stime; //计算差值
echo "[页面执行时间]：{$total} 秒<br />";
echo <<< EOF
<a href="./">返回上页</a>&nbsp&nbsp&nbsp<a href="../Admin/">返回首页</a>
</body>
</html>
EOF;
?>