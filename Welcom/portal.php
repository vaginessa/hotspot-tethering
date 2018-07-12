<?php
require '../Admin/main.class.php';
$user_file='user.json';
$json_string=file_get_contents($user_file);
$data=json_decode($json_string, true);
$command_file=sys_get_temp_dir().'/portal.sh';
$command_stop='iptables -t nat -F user_portal';
$command_start='iptables -t nat -A user_portal -p tcp -m multiport --dports 80,8080 -j REDIRECT --to-ports 8080'.PHP_EOL.'iptables -t nat -A user_portal -p tcp --dport 443 -j REDIRECT --to-ports 4433'.PHP_EOL.'iptables -t nat -A user_portal -j DNAT --to-destination 127.0.0.1';

if (file_exists($command_file) or is_executable($command_file)) { 
  unlink($command_file);
}

function iptables_write($command_file, $command) {
    file_put_contents($command_file, $command, FILE_APPEND | LOCK_EX);
    chmod($command_file, 0700);
    exec("su -c $command_file", $output, $return_val);
    if ($return_val != 0) {
        die('执行命令失败！返回值: ' . $return_val);
    } else { 
        die('成功');
    }
}

function user_write($data, $command_file) {
  foreach ($data as $key => $value) {
     foreach ($value as $user => $info) {
         $ipaddress = $info['ip_address'];
         $macaddress = $info['mac_address'];
         $status = $info['status'];
           if ($ipaddress != '' and $macaddress != '' and $status != 'Block') { 
             file_put_contents($command_file, "iptables -t nat -I user_portal -s $ipaddress -m mac --mac-source $macaddress -j RETURN".PHP_EOL, FILE_APPEND | LOCK_EX);
           }
        }
     }
  chmod($command_file, 0700);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $command = $_POST['command'];
    $number = $_POST['number'];
}

if ($command and $number) { 
    if ($command == 'start') { 
      $command_run=$command_stop.PHP_EOL.$command_start;
      iptables_write($command_file, $command_run);
    }
   if ($command == 'stop') { 
      iptables_write($command_file, $command_stop);
    }
   if ($command == 'write') { 
      $command_load=$command_stop.PHP_EOL.$command_start.PHP_EOL;
      file_put_contents($command_file, $command_load, LOCK_EX);
      user_write($data, $command_file);
      iptables_write($command_file, "");
   }
}
?>