<?php
session_start();
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

function network_traffic($interface) {
    foreach (explode(PHP_EOL, file_get_contents('/proc/net/dev')) as $key) {
         $dev = explode(':', $key);
         if ($dev[0] == $interface) {
            $flow = explode(' ', $dev[1]);
            return array(
                $flow[1],
                $flow[4],
                $flow[42],
                $flow[45]
            );
        }
    }
}

function network_interface_card() {
    $data = explode(PHP_EOL, shell_exec('ip address'));
    foreach ($data as $key) {
        $data = explode(' ', $key);
        if ($data[8] && $data[5]) {
          preg_match('/^(?!^127\.|^255\.|^0\.)10(\.[0-9]{1,3}){3}/', $data[5], $ip);
          if ($ip[0]) {
            return array(
                $data[8],
                $ip[0]
            );
          }
       }
    }
}
/*
function network_interface_card2() {
    $data = explode(PHP_EOL, shell_exec('ip address'));
    foreach ($data as $key) {
        preg_match('/10(\.[0-9]{1,3}){3}/', $key, $ip);
        $data = explode(' ', $key);
        if ($ip[0] && $data[8]) {
            return array(
                $data[8],
                $ip[0]
            );
        }
    }
}
*/

function memory() {
 foreach (explode(PHP_EOL, file_get_contents('/proc/meminfo')) as $key) {
     $key=explode(':', $key);
     foreach (explode('kB', $key[1]) as $value) {
     $value=trim($value);
         if ($value > 0) { 
            if ($key[0]=="MemTotal") {
            $MemTotal=$value;
            }
            if ($key[0]=="MemFree") {
            $MemFree=$value;
            }
            if ($key[0]=="Buffers") {
            $Buffers=$value;
            }
            if ($key[0]=="Cached") {
            $Cached=$value;
            }
         }
      }
   }
  $free=($MemFree+$Buffers+$Cached);
  $ram_free=round($free/1024,2); //剩余内存(MB)
  $ram_rate=round(($free/$MemTotal)*100,2); //剩余内存百分比
  $mem_total=round($MemTotal/1024, 2); //可用总内存(MB)
  return array($ram_free,$ram_rate,$mem_total);
} 

function cpu_activity() {
  $stat_file = file('/proc/stat');
  $cpu_data=explode(' ', $stat_file[0]);
  $totalCPUTime=0;
  for($i = 0; $i < count($cpu_data); $i++)
  {
     if ($cpu_data[$i]>0) {
     $totalCPUTime=$cpu_data[$i]+$totalCPUTime;
     $idle=$cpu_data[5];
     }
  }
  return array($totalCPUTime,$idle);
}

function size_unit()
{
    $size = trim(func_get_arg(0));
    if ($size < 1024) {
        return array($size,'B');
    } elseif ($size < 1024 * 1024) {
        $size=number_format($size / 1024, 3);
        return array($size,'KB');
    } elseif ($size < 1024 * 1024 * 1024) {
        $size=number_format($size / 1024 / 1024, 3);
        return array($size,'MB');
    } elseif ($size < 1024 * 1024 * 1024 * 1024) {
        $size=number_format($size / 1024 / 1024 / 1024, 3);
        return array($size,'GB');
    } else {
        $size=number_format($size / 1024 / 1024 / 1024 / 1024, 3);
        return array($size,'TB');
    }
}


//刷新流量信息
if ($_POST['Refresh']=='refresh') {
  session_destroy();
}

//读取网卡信息
if (!isset($_SESSION['interface_name'])) {
    list($interface_name, $local_address) = network_interface_card();
    $_SESSION['interface_name'] = $interface_name;
    $_SESSION['local_address'] = $local_address;
}

//返回流量信息
list($Receive_bytes, $Receive_packets, $Transmit_bytes, $Transmit_packets) = network_traffic($_SESSION['interface_name']);

//流量速率采样
if (isset($_SESSION['download_speed']) && isset($_SESSION['upload_speed'])) {
    $New_Rb=$Receive_bytes-$_SESSION['download_speed'];
    $New_Tb=$Transmit_bytes-$_SESSION['upload_speed'];
    //刷新流量数据
    $_SESSION['download_speed'] = $Receive_bytes;
    $_SESSION['upload_speed'] = $Transmit_bytes;
    if ($New_Rb > 0) {
        $Download=size_unit($New_Rb);
    } else {
        unset($Download);
    }
    if ($New_Tb > 0) {
        $Upload=size_unit($New_Tb);
    } else {
        unset($Upload);
    }
} else { 
    $_SESSION['download_speed'] = $Receive_bytes;
    $_SESSION['upload_speed'] = $Transmit_bytes;
}

//流量统计
$Rb_Size=size_unit($Receive_bytes);
$Rb_All=$Rb_Size[0].$Rb_Size[1];
$Tb_Size=size_unit($Transmit_bytes);
$Tb_All=$Tb_Size[0].$Tb_Size[1];


//返回RAM信息
list($ram_free, $ram_rate, $mem_total) = memory();

//CPU信息采样
list($Total_1, $SYS_IDLE_1) = cpu_activity();
sleep(1);
list($Total_2, $SYS_IDLE_2) = cpu_activity();
$SYS_IDLE=($SYS_IDLE_2-$SYS_IDLE_1);
$Total=($Total_2-$Total_1);
$SYS_USAGE=($SYS_IDLE/$Total) * 100;
$SYS_Rate=round(100-$SYS_USAGE,2);

//TCP连接数统计
$tcp_conntrack=count(file('/proc/net/tcp'))-1;

//剩余存储空间信息
$storage_dir=__DIR__;
$st=size_unit(disk_total_space($storage_dir));
$storage_total="$st[0] $st[1]";
$sf=size_unit(disk_free_space($storage_dir));
$storage_free="$sf[0] $sf[1]";
$storage_rate=round($sf[0]/$st[0] * 100, 2);

echo "retry: 1000\n"; //1秒(发送频率)

echo "event: traffic\n";
echo 'data: {"interface_name": "'.$_SESSION['interface_name'].'", "local_address": "'.$_SESSION['local_address'].'", "tcp_conntrack": "'.$tcp_conntrack.'", "download_speed": "'.$Download[0].$Download[1].'", "upload_speed": "'.$Upload[0].$Upload[1].'", "download_format": "'.$Rb_All.'", "upload_format": "'.$Tb_All.'","Receive_bytes": "'.$Receive_bytes.'", "Receive_packets": "'.$Receive_packets.'", "Transmit_bytes": "'.$Transmit_bytes.'", "Transmit_packets": "'.$Transmit_packets.'"}'."\n\n";

echo "event: memory\n";
echo "data: {\"ram_free\": \"$ram_free\",\"ram_rate\": \"$ram_rate\",\"mem_total\": \"$mem_total\"}\n\n";

echo "event: cpu\n";
echo "data: $SYS_Rate\n\n";

echo "event: storage\n";
echo "data: {\"storage_dir\": \"$storage_dir\",\"storage_total\": \"$storage_total\",\"storage_free\": \"$storage_free\",\"storage_rate\": \"$storage_rate\"}\n\n";

session_write_close();
?>