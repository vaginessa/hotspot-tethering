<?
date_default_timezone_set('Asia/Shanghai');
header("Content-Type: text/html;charset=utf-8");
session_start(); 
require "iptables.php";
require "ssurl.php";

//命令查找
$pkill = busybox_check("pkill");

//移动模块文件
foreach ($status_binary as $value) {
    $binary_file = sys_get_temp_dir() . "/$value";
    if (!is_executable($binary_file) and file_exists($value)) {
        copy("$value", $binary_file);
        chmod($binary_file, 0700);
    }
}



function set_token() { 
     $_SESSION['token'] = md5(microtime(true)); 
 } 
 
function valid_token() { 
     $return = $_REQUEST['token'] === $_SESSION['token'] ? true : false; 
     set_token(); 
     return $return; 
 } 

if (!valid_token()) { 
    header('Location: ../');
    die("请勿重复提交表单");
 }

if ($_SERVER["REQUEST_METHOD"] == "GET") {
   $shadowsocks = test_input($_GET['shadowsocks']);
   $name = test_input($_GET['name']);
   $server = test_input($_GET['server']);
   $server_port = test_input($_GET['server_port']);
   $password = test_input($_GET['password']);
   $method = test_input($_GET['method']);
   $route = test_input($_GET['route']);
   $udp = test_input($_GET['udp']);
   $gost_server = test_input($_GET['gost_server']);
   $gost_server_port = test_input($_GET['gost_server_port']);
   $gost_username = test_input($_GET['gost_username']);
   $gost_password = test_input($_GET['gost_password']);
   $plugin = test_input($_GET['plugin']);
   $obfs = test_input($_GET['obfs']);
   $obfs_host = test_input($_GET['obfs_host']);
   $token = test_input($_GET['token']);
   $remotePort = test_input($_GET['remotePort']);
   $remoteHost = test_input($_GET['remoteHost']);
   $ServerName = test_input($_GET['ServerName']);
   $Key = test_input($_GET['Key']);
   $TicketTimeHint = test_input($_GET['TicketTimeHint']);
   $Browser = test_input($_GET['Browser']);
   $kcpremoteaddr = test_input($_GET['kcpremoteaddr']);
   $kcpkey = test_input($_GET['kcpkey']);
   $kcpcrypt = test_input($_GET['kcpcrypt']);
   $kcpmode = test_input($_GET['kcpmode']);
   $kcpconn = test_input($_GET['kcpconn']);
   $kcpautoexpire = test_input($_GET['kcpautoexpire']);
   $kcpscavengettl = test_input($_GET['kcpscavengettl']);
   $kcpmtu = test_input($_GET['kcpmtu']);
   $kcpsndwnd = test_input($_GET['kcpsndwnd']);
   $kcprcvwnd = test_input($_GET['kcprcvwnd']);
   $kcpdatashard = test_input($_GET['kcpdatashard']);
   $kcpparityshard = test_input($_GET['kcpparityshard']);
   $kcpdscp = test_input($_GET['kcpdscp']);
}

function test_input($data) {
   $data = trim($data);
   $data = stripslashes($data);
   $data = htmlspecialchars($data);
   return $data;
}

//服务器是否ss://链接然后解析
if(strpos("$server",'ss://') !== false) {
   if(stripos("$server",'@') !== false) {
       list ($name, $server, $server_port, $password, $method) = android_share_input($server);
  } else {
       list ($name, $server, $server_port, $password, $method) = share_input($server);
  }
}



if (empty($_REQUEST['shadowsocks']) and $server and $server_port and $password and $method) {
//如果开关按钮关闭
iptables_stop($stop_iptables, $status_binary, true);
sleep(1);
header('Location: ../');
//header('Refresh:2,Url=./');
//echo 'shadowsocks已经关闭！2s 后跳转';
//由于只是普通页面展示，提示的样式容易定制
die();
}

if ($shadowsocks == 'on' and $server and $server_port and $password and $method) {

function jx_server($server) {
   //服务器是否为域名网址地址？
  if (preg_match('/[a-z]+/i', $server)>0) {
    $server = gethostbyname($server);
    }
  return $server;
}

$jx = array("server"=>"$server", "gost_server"=>"$gost_server");
  foreach ($jx as $key => $value) {
     $value = jx_server($value);
      if ($key == "server") {
      $server = "$value";
      } elseif ($key == "gost_server") {
      $gost_server = "$value";
      } 
}

//如果gost服务器空替换为ss服务器
if (empty($gost_server)) $gost_server = $server;

if ($server) {
//传送参数执行iptables规则
   iptables_start($mangle, $nat, $filter, $stop_iptables, $status_binary, $server, $udp);
} else {
   die("没有获取到服务器信息!");
}

//写出配置
   $data = "shadowsocks=$shadowsocks".PHP_EOL."name=$name".PHP_EOL."server=$server".PHP_EOL."server_port=$server_port".PHP_EOL."password=$password".PHP_EOL."method=$method".PHP_EOL."route=$route".PHP_EOL."udp=$udp".PHP_EOL."gost_server=$gost_server".PHP_EOL."gost_server_port=$gost_server_port".PHP_EOL."gost_username=$gost_username".PHP_EOL."gost_password=$gost_password".PHP_EOL."plugin=$plugin".PHP_EOL."obfs=$obfs".PHP_EOL."obfs_host=$obfs_host".PHP_EOL."remotePort=$remotePort".PHP_EOL."remoteHost=$remoteHost".PHP_EOL."ServerName=$ServerName".PHP_EOL."Key=$Key".PHP_EOL."TicketTimeHint=$TicketTimeHint".PHP_EOL."Browser=$Browser".PHP_EOL."kcpremoteaddr=$kcpremoteaddr".PHP_EOL."kcpkey=$kcpkey".PHP_EOL."kcpcrypt=$kcpcrypt".PHP_EOL."kcpmode=$kcpmode".PHP_EOL."kcpconn=$kcpconn".PHP_EOL."kcpautoexpire=$kcpautoexpire".PHP_EOL."kcpscavengettl=$kcpscavengettl".PHP_EOL."kcpmtu=$kcpmtu".PHP_EOL."kcpsndwnd=$kcpsndwnd".PHP_EOL."kcprcvwnd=$kcprcvwnd".PHP_EOL."kcpdatashard=$kcpdatashard".PHP_EOL."kcpparityshard=$kcpparityshard".PHP_EOL."kcpdscp=$kcpdscp";
   file_put_contents('ss-local.ini', $data, LOCK_EX);   
   
//tproxy配置运行
   $binary = sys_get_temp_dir()."/tproxy";
   $peizhi = dirname(__FILE__)."/tproxy.ini";
   shell_exec("$pkill tproxy".PHP_EOL."$binary $peizhi > /dev/null 2>&1 &");

//overture配置运行
   $binary = sys_get_temp_dir()."/overture";
   $peizhi = dirname(__FILE__)."/overture.json";
   $obj = json_decode(file_get_contents($peizhi));
   $obj->HostsFile=dirname(__FILE__)."/hosts";
   $obj = json_encode($obj);
   file_put_contents('overture.json', $obj, LOCK_EX);
   shell_exec("$pkill overture".PHP_EOL."$binary -c $peizhi > /dev/null 2>&1 &");
   
//gost配置运行
   $binary = sys_get_temp_dir()."/gost";
   if ($gost_username and $gost_password) {
   $my_gost = "socks5://$gost_username:$gost_password@$gost_server:$gost_server_port";
   } else { 
   $my_gost = "socks5://$gost_server:$gost_server_port";
   }
   if ($udp == 'on') shell_exec("$pkill gost".PHP_EOL."$binary -L socks5://127.0.0.1:1028 -F socks5://127.0.0.1:1025 -F socks5://$my_gost > /dev/null 2>&1 &");


//shadowsocks+插件配置
   $binary = sys_get_temp_dir()."/ss-local";
   $peizhi = dirname(__FILE__)."/$route";
   $pid = dirname(__FILE__)."/ss-local.pid";   
if ($plugin == 'off' or empty($plugin)) {
   $my_shadowsocks = "$binary -s $server -p $server_port -k $password -m $method -b 127.0.0.1 -l 1025 --acl $peizhi -f $pid -a 3004";
   shell_exec("su -c $my_shadowsocks > /dev/null 2>&1 &");
} else { 
   $my_shadowsocks = "$binary -s 127.0.0.1 -p 1026 -k $password -m $method -b 127.0.0.1 -l 1025 --acl $peizhi -f $pid -a 3004";
   shell_exec("su -c $my_shadowsocks > /dev/null 2>&1 &");
}

//obfs混淆插件
    $binary2 = sys_get_temp_dir()."/obfs-local";
    $pid2 = dirname(__FILE__)."/obfs-local.pid";
if ($plugin == 'obfs-local' and $obfs and $obfs_host) {
   $my_obfs = "$binary2 -s $server -p $server_port -b 127.0.0.1 -l 1026 --obfs $obfs --obfs-host $obfs_host -f $pid2";
   shell_exec("$my_obfs > /dev/null 2>&1 &");
} 

//kcptun插件
   $binary3 = sys_get_temp_dir()."/kcptun";
if ($plugin == 'kcptun' and $kcpremoteaddr) {
   if (empty($kcpremoteaddr)) $kcpremoteaddr="$server:29900";
   if (empty($kcpkey)) $kcpkey="it's a secrect";
   if (empty($kcpcrypt)) $kcpcrypt="aes";
   if (empty($kcpmode)) $kcpmode="fast";
   if (empty($kcpconn)) $kcpconn=1;
   if (empty($kcpautoexpire)) $kcpautoexpire=0;
   if (empty($kcpscavengettl)) $kcpscavengettl=600;
   if (empty($kcpmtu)) $kcpmtu=1350;
   if (empty($kcpsndwnd)) $kcpsndwnd=128;
   if (empty($kcprcvwnd)) $kcprcvwnd=512;
   if (empty($kcpdatashard)) $kcpdatashard=10;
   if (empty($kcpparityshard)) $autoexpire=3;
   if (empty($kcpdscp)) $kcpdscp=0;
   $my_kcptun = "$binary3 -l 127.0.0.1:1026 -r $kcpremoteaddr --key $kcpkey --crypt $kcpcrypt -mode $kcpmode -conn $kcpconn -autoexpire $kcpautoexpire -scavengettl $kcpscavengettl -mtu $kcpmtu -sndwnd $kcpsndwnd -rcvwnd $kcprcvwnd -datashard $kcpdatashard -parityshard $kcpparityshard -dscp $kcpdscp";
   shell_exec("$my_kcptun > /dev/null 2>&1 &");
} 


//GoQuiet插件
   $binary4 = sys_get_temp_dir()."/GoQuiet";
   $peizhi4 = dirname(__FILE__)."/GoQuiet.json";
   $obj = json_decode(file_get_contents("$peizhi4"));
   $obj->ServerName=$ServerName;
   $obj->Key=$Key;
   $obj->TicketTimeHint=$TicketTimeHint;
   $obj->Browser=$Browser;
   $obj = json_encode($obj,JSON_NUMERIC_CHECK);
   file_put_contents($peizhi4, $obj, LOCK_EX);
if (empty($remotePort)) $remotePort=443;
if (empty($remoteHost)) $remoteHost=$server;
if ($plugin == 'GoQuiet' and $ServerName and $Key and $TicketTimeHint and $Browser) {
   $my_GoQuiet = "$binary4 -s $remoteHost -p $remotePort -l 1026 -c $peizhi4";
   shell_exec("$my_GoQuiet > /dev/null 2>&1 &");
} 

sleep(1);

//redsocks2配置运行
   $binary = sys_get_temp_dir()."/redsocks2";
   $peizhi = dirname(__FILE__)."/redsocks2.json";
if ($udp == 'on' and $gost_server and $gost_server_port) { 
   shell_exec("su -c $binary -c $peizhi > /dev/null 2>&1 &");
}
sleep(1);
header('Location: ../');

}//

?>