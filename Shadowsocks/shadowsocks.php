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
require '../Admin/main.class.php';
$binary = array(
    'gost',
    'redsocks',
    'GoQuiet',
    'kcptun',
    'obfs-local',
    'ss-redir',
    'ss-local',
    'dnsforwarder',
    'libproxychains4.so',
    'iptables.sh'
);
//移动模块文件
foreach ($binary as $val) {
  $binary_file = sys_get_temp_dir() . "/$val";
    if (!is_executable($binary_file) && file_exists($val)) {
      copy($val, $binary_file);
      chmod($binary_file, 0755);
    }
}
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $shadowsocks = $_GET['shadowsocks'];
    $server_uid = (int)posix_getuid();
    $name = $_GET['name'];
    $server = $_GET['server'];
    $server_port = $_GET['server_port'];
    $password = $_GET['password'];
    $method = $_GET['method'];
    $route = $_GET['route'];
    $remote_dns = $_GET['remote_dns'];
    $remote_dns_forward = $_GET['remote_dns_forward'];
    $tcp_fast_open = $_GET['tcp_fast_open'];
    $icmp = $_GET['icmp'];
    $udp = $_GET['udp'];
    $gost_server = $_GET['gost_server'];
    $gost_server_port = $_GET['gost_server_port'];
    $gost_username = $_GET['gost_username'];
    $gost_password = $_GET['gost_password'];
    $plugin = $_GET['plugin'];
    $obfs = $_GET['obfs'];
    $obfs_http_method = $_GET['obfs_http_method'];
    $obfs_url = $_GET['obfs_url'];
    $obfs_host = $_GET['obfs_host'];
    $remotePort = $_GET['remotePort'];
    $remoteHost = $_GET['remoteHost'];
    $ServerName = $_GET['ServerName'];
    $Key = $_GET['Key'];
    $TicketTimeHint = $_GET['TicketTimeHint'];
    $Browser = $_GET['Browser'];
    $kcptun_remoteaddr = $_GET['kcptun_remoteaddr'];
    $kcptun_key = $_GET['kcptun_key'];
    $kcptun_crypt = $_GET['kcptun_crypt'];
    $kcptun_mode = $_GET['kcptun_mode'];
    $kcptun_conn = $_GET['kcptun_conn'];
    $kcptun_autoexpire = $_GET['kcptun_autoexpire'];
    $kcptun_scavengettl = $_GET['kcptun_scavengettl'];
    $kcptun_mtu = $_GET['kcptun_mtu'];
    $kcptun_sndwnd = $_GET['kcptun_sndwnd'];
    $kcptun_rcvwnd = $_GET['kcptun_rcvwnd'];
    $kcptun_datashard = $_GET['kcptun_datashard'];
    $kcptun_parityshard = $_GET['kcptun_parityshard'];
    $kcptun_dscp = $_GET['kcptun_dscp'];   
    $kcptun_nocomp = $_GET['kcptun_nocomp'];   
    $proxychains_type = $_GET['proxychains_type'];
    $proxychains_address = $_GET['proxychains_address'];
    $proxychains_port = $_GET['proxychains_port'];
    $proxychains_username = $_GET['proxychains_username'];
    $proxychains_password = $_GET['proxychains_password'];
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
function ss_config($server, $server_port, $local_port, $password, $method, $plugin, $plugin_opts) {
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

//关闭shadowsocks
if (empty($_REQUEST['shadowsocks'])) {
    //创建停止运行脚本
    $stop_file = sys_get_temp_dir() . '/stop.sh';
    @unlink($stop_file);
    foreach ($binary as $val) {
        file_put_contents($stop_file, "pkill $val" . PHP_EOL, FILE_APPEND);
    }
    file_put_contents($stop_file, 'kill '.file_get_contents('daemon.pid').PHP_EOL, FILE_APPEND);
    //执行关闭模块
    chmod($stop_file, 0700);
    system('su -c '.$stop_file);
    @unlink(sys_get_temp_dir().'/daemon.sh');
    //执行关闭iptables规则
    system('su -c '.sys_get_temp_dir().'/iptables.sh stop');
}
//启动shadowsocks
if ($shadowsocks == 'on') {
    echo "开启Shadowsocks<br />";
    //创建开始运行脚本
    $start_file = sys_get_temp_dir() . '/start.sh';
    @unlink($start_file);
    //服务器是否为域名网址？
    $server=check_domain($server);
    $gost_server=check_domain($gost_server);
  if ($udp == 'udp_over_tcp') { 
    //redsocks配置运行
    $binary = sys_get_temp_dir() . '/redsocks';
    $config = __DIR__ . '/redsocks.conf';
    file_put_contents($start_file, "$binary -c $config > /dev/null 2>&1 &" . PHP_EOL, FILE_APPEND);
    //gost配置运行
    $binary = sys_get_temp_dir() . '/gost';
    if ($gost_username && $gost_password) {
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
    //dnsforwarder配置运行
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
    //kcptun插件
    if ($plugin == 'kcptun' && $kcptun_remoteaddr) {
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
  if ($plugin == 'GoQuiet' && $ServerName && $Key && $TicketTimeHint && $Browser) {
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
  if ($route != 'all') {
    $acl='--acl '.__DIR__."/$route";
  }
    //shadowsocks+插件配置
    if ($udp == 'udp_over_tcp') {
      $binary = sys_get_temp_dir() . '/ss-local '.$acl.' -f '.__DIR__ . '/ss-deamon.pid';
      $local_port = 1025;
    } else {
      $binary = sys_get_temp_dir() . '/ss-redir '.$acl.' -f '.__DIR__ . '/ss-deamon.pid';
      $local_port = 1024;
    }
    $config = __DIR__ . '/shadowsocks.conf';
    $iserver=$server;
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
   ss_config($server, $server_port, $local_port, $password, $method, $plugin, $plugin_opts);
   $server=$iserver;
   if ($plugin == 'proxychains') { //配置代理链
      file_put_contents($start_file, 'env PROXYCHAINS_CONF_FILE='.__DIR__.'/proxychains.conf LD_PRELOAD='.sys_get_temp_dir().'/libproxychains4.so '."$binary -c $config > /dev/null 2>&1 &" . PHP_EOL, FILE_APPEND);
      file_put_contents('proxychains.conf', 'strict_chain'.PHP_EOL.'[ProxyList]'.PHP_EOL.$proxychains_type.' '.$proxychains_address.' '.$proxychains_port.' '.$proxychains_username.' '.$proxychains_password.PHP_EOL);
     $server=$proxychains_address;
   } else {
     file_put_contents($start_file, "$binary -c $config > /dev/null 2>&1 &" . PHP_EOL, FILE_APPEND);
   }    
   //写出记录配置
    $data = <<< EOF
shadowsocks={$shadowsocks}
server_uid={$server_uid}
name={$name}
server={$server}
server_port={$server_port}
password={$password}
method={$method}
route={$route}
remote_dns={$remote_dns}
remote_dns_forward={$remote_dns_forward}
tcp_fast_open={$tcp_fast_open}
icmp={$icmp}
udp={$udp}
gost_server={$gost_server}
gost_server_port={$gost_server_port}
gost_username={$gost_username}
gost_password={$gost_password}
plugin={$plugin}
obfs={$obfs}
obfs_http_method={$obfs_http_method}
obfs_url={$obfs_url}
obfs_host={$obfs_host}
remotePort={$remotePort}
remoteHost={$remoteHost}
ServerName={$ServerName}
Key={$Key}
TicketTimeHint={$TicketTimeHint}
Browser={$Browser}
kcptun_remoteaddr={$kcptun_remoteaddr}
kcptun_key={$kcptun_key}
kcptun_crypt={$kcptun_crypt}
kcptun_mode={$kcptun_mode}
kcptun_conn={$kcptun_conn}
kcptun_autoexpire={$kcptun_autoexpire}
kcptun_scavengettl={$kcptun_scavengettl}
kcptun_mtu={$kcptun_mtu}
kcptun_sndwnd={$kcptun_sndwnd}
kcptun_rcvwnd={$kcptun_rcvwnd}
kcptun_datashard={$kcptun_datashard}
kcptun_parityshard={$kcptun_parityshard}
kcptun_dscp={$kcptun_dscp}   
kcptun_nocomp={$kcptun_nocomp}   
proxychains_type={$proxychains_type}
proxychains_address={$proxychains_address}
proxychains_port={$proxychains_port}
proxychains_username={$proxychains_username}
proxychains_password={$proxychains_password}
EOF;
    file_put_contents('config.ini', $data);
    //执行开启模块
    chmod($start_file, 0700);
    system('su -c '.$start_file);
    //执行开启iptables
    system('su -c '.sys_get_temp_dir().'/iptables.sh start '.__DIR__.'/config.ini');
} //
$etime = microtime(true); //获取程序执行结束的时间
$total = $etime - $stime; //计算差值
echo "<br />[页面执行时间]：{$total} 秒<br />";
echo <<< EOF
<a href="./">返回上页</a>&nbsp&nbsp&nbsp<a href="../Admin/">返回首页</a>
</body>
</html>
EOF;
?>