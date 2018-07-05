<?php
session_start();
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

function network_traffic($interface) {
    foreach (explode(PHP_EOL, file_get_contents('/proc/net/dev')) as $key) {
        $dev = explode(':', $key);
        preg_match_all('/[0-9]{3,}/', $dev[1], $matchs);
        if ($dev[0] == $interface) {
            return array(
                $matchs[0][0],
                $matchs[0][1],
                $matchs[0][2],
                $matchs[0][3]
            );
        }
    }
}

function network_interface_card() {
    $data = explode(PHP_EOL, shell_exec('ip address'));
    foreach ($data as $key) {
        preg_match_all('/10(\.[0-9]{1,3}){3}/', $key, $ip);
        $data = explode(' ', $key);
        if ($ip[0][0] && $data[8]) {
            return array(
                $data[8],
                $ip[0][0]
            );
        }
    }
}

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
  $RAM=round($free/1024,2); //剩余内存(MB)
  $RAM2=round(($free/$MemTotal)*100,2); //剩余内存百分比
  $MemTotal2=round($MemTotal/1024, 2); //可用总内存(MB)
  return array($RAM,$RAM2,$MemTotal2);
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

function storage()
{
    $size = trim(func_get_arg(0));
    if ($size < 1024) {
        return $size . " B";
    } elseif ($size < 1024 * 1024) {
        return number_format($size / 1024, 3) . " KB";
    } elseif ($size < 1024 * 1024 * 1024) {
        return number_format($size / 1024 / 1024, 3) . " MB";
    } elseif ($size < 1024 * 1024 * 1024 * 1024) {
        return number_format($size / 1024 / 1024 / 1024, 3) . " GB";
    } else {
        return number_format($size / 1024 / 1024 / 1024 / 1024, 3) . " TB";
    }
}


//刷新信息
if ($_POST['Refresh']=='refresh') {
session_destroy();
}

if (!isset($_SESSION['interface_name'])) {
    list($interface_name, $ip_address) = network_interface_card();
    $_SESSION['interface_name'] = $interface_name;
    $_SESSION['ip_address'] = $ip_address;
}

list($Receive_bytes, $Receive_packets, $Transmit_bytes, $Transmit_packets) = network_traffic($_SESSION['interface_name']);

list($RAM, $RAM2, $MemTotal) = memory();

//cpu采样
list($Total_1, $SYS_IDLE_1) = cpu_activity();
sleep(1);
list($Total_2, $SYS_IDLE_2) = cpu_activity();
$SYS_IDLE=($SYS_IDLE_2-$SYS_IDLE_1);
$Total=($Total_2-$Total_1);
$SYS_USAGE=($SYS_IDLE/$Total) * 100;
$SYS_Rate=round(100-$SYS_USAGE,2);
//

$tcp_conntrack=count(file('/proc/net/tcp'))-1;

$storage_name=__DIR__;
$storage_total=storage(disk_total_space($storage_name));
$storage_free=storage(disk_free_space($storage_name));
$storage_rate=round(disk_free_space($storage_name)/disk_total_space($storage_name) * 100, 2);

echo "retry: 1000\n"; //1秒(发送频率)

echo "event: traffic\n";
$data = "网卡: <b style=\"color:#8558ef;\">" . $_SESSION['interface_name'] . "</b> 内网: <b style=\"color:#8558ef;\">" . $_SESSION['ip_address'] . "</b> 连接数: <b style=\"color:#8558ef;\">" . $tcp_conntrack . "</b><br>接收的字节数: <b style=\"font-size: 20px;color:#ee82ee;\">" . round($Receive_bytes / 1024 / 1024, 2) . " MB</b> 收到的数据包数量: <b>$Receive_packets </b><br>传输的字节数: <b style=\"font-size: 20px;color:#66ccff;\">" . round($Transmit_bytes / 1024 / 1024, 2) . " MB</b> 传输的数据包数量: <b>$Transmit_packets</b>";
echo "data: $data\n\n";

echo "event: memory\n";
echo "data: {\"RAM\": \"$RAM\",\"RAM2\": \"$RAM2\",\"MemTotal\": \"$MemTotal\"}\n\n";

echo "event: cpu\n";
echo "data: $SYS_Rate\n\n";

echo "event: storage\n";
echo "data: {\"storage_name\": \"$storage_name\",\"storage_total\": \"$storage_total\",\"storage_free\": \"$storage_free\",\"storage_rate\": \"$storage_rate\"}\n\n";

session_write_close();
flush();
?>