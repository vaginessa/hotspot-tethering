<?php
session_start();
//ignore_user_abort(true);
//set_time_limit(0);
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
if (!isset($_SESSION['interface_name'])) {
    list($interface_name, $ip_address) = network_interface_card();
    $_SESSION['interface_name'] = $interface_name;
    $_SESSION['ip_address'] = $ip_address;
}
list($Receive_bytes, $Receive_packets, $Transmit_bytes, $Transmit_packets) = network_traffic($_SESSION['interface_name']);
echo "网卡: <b>" . $_SESSION['interface_name'] . "</b> 内网: <b>" . $_SESSION['ip_address'] . "</b><br>接收的字节数: <b style=\"font-size: 20px;color:#ee82ee;\">" . round($Receive_bytes / 1024 / 1024, 2) . " MB</b> 收到的数据包数量: <b>$Receive_packets </b><br>传输的字节数: <b style=\"font-size: 20px;color:#66ccff;\">" . round($Transmit_bytes / 1024 / 1024, 2) . " MB</b> 传输的数据包数量: <b>$Transmit_packets</b>";
flush();
//session_destroy();
die;
?>