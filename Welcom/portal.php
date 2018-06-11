<?php
require '../tools/Certified.php';
$user_file="user.json";
$json_string=file_get_contents($user_file);
$data=json_decode($json_string, true);
$command_file=sys_get_temp_dir()."/portal.sh";
$command_gb="iptables -t nat -F user_portal";
$command_kq="iptables -t nat -A user_portal -p tcp -m multiport --dports 80,8080 -j REDIRECT --to-ports 8080".PHP_EOL."iptables -t nat -A user_portal -p tcp --dport 443 -j REDIRECT --to-ports 4433".PHP_EOL."iptables -t nat -A user_portal -s 192.168/16 -j DNAT --to-destination 127.0.0.1";

if (file_exists($command_file) or is_executable($command_file)) { 
unlink($command_file);
}

function iptables_writ($command_file, $command) {
        file_put_contents($command_file, $command, LOCK_EX);
        chmod($command_file, 0700);
}

function iptables_sz_writ($data, $command_file) {
foreach ($data as $key => $value) {
    foreach ($value as $user => $info) {
        $ipaddress = $info['ip_address'];
        $macaddress = $info['mac_address'];
        $status = $info['status'];
          if ($ipaddress != "" and $macaddress != "" and $status != "Block") { 
           file_put_contents($command_file, "iptables -t nat -I user_portal -s $ipaddress -m mac --mac-source $macaddress -j RETURN".PHP_EOL, FILE_APPEND | LOCK_EX);
          }
      }
   }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $command = $_POST['command'];
    $number = $_POST['number'];
}


if ($command and $number) {
    if ($command == "kq") {
$command_run="$command_gb".PHP_EOL."$command_kq";
    iptables_writ($command_file, $command_run);
    echo "已经执行开启,你可能需要加载用户表规则。";
    //shell_exec("su -c $command_file");
    }
    if ($command == "gb") {
    iptables_writ($command_file, $command_gb);
    echo "已经执行了关闭。";
    //shell_exec("su -c $command_file");
    }
    if ($command == "cz") {
$command_run="$command_gb".PHP_EOL."$command_kq".PHP_EOL;
    file_put_contents($command_file, $command_run, LOCK_EX);
    iptables_sz_writ($data, $command_file);
    echo "已经加载用户表规则。";
    //shell_exec("su -c $command_file");
    }
}
?>