<?php 
/*
执行菜单选项动作的命令文件
*/
require '../Admin/main.class.php';
$user_file='user.json';
$json_string=file_get_contents($user_file);
$data=json_decode($json_string, true); //解码json用户数据文件
$script_file=sys_get_temp_dir().'/portal.sh';
$command_stop='iptables -t nat -F user_portal'.PHP_EOL.'iptables -t filter -F user_block';
$command_start='iptables -t nat -A user_portal -p tcp -m multiport --dports 80,8080 -j REDIRECT --to-ports 8080'.PHP_EOL.'iptables -t nat -A user_portal -p tcp --dport 443 -j REDIRECT --to-ports 4433';
@unlink($script_file);

function iptables_write($script_file, $command) {
    file_put_contents($script_file, $command, FILE_APPEND | LOCK_EX);
    chmod($script_file, 0700);
    exec("su -c $script_file", $output, $return_val);
    if ($return_val != 0) {
        die('执行命令失败！返回值: ' . $return_val);
    } else { 
        die('成功');
    }
}

//更新和写入用户设置规则
function user_write($data, $script_file) {
  foreach ($data as $key => $value) {
     foreach ($value as $user => $info) {
         $ipaddress = $info['ip_address'];
         $macaddress = $info['mac_address'];
         $status = $info['status'];
           if ($ipaddress && $macaddress && $status) { 
             switch ($status) {
             case "OK":
               $add_rule="iptables -t nat -I user_portal -s $ipaddress -m mac --mac-source $macaddress -j RETURN".PHP_EOL;
               break;
             case "Block":
               $add_rule="iptables -t filter -I user_block -m mac --mac-source $macaddress -j DROP".PHP_EOL;
               break;
             }
             if ($add_rule){
               file_put_contents($script_file, $add_rule, FILE_APPEND | LOCK_EX);
             }
           }
        }
     }
  chmod($script_file, 0700);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $command = $_POST['command'];
    $number = $_POST['number'];
}

if ($command and $number) { 
    if ($command == 'start') { 
      $command_run=$command_stop.PHP_EOL.$command_start;
      iptables_write($script_file, $command_run);
    }
   if ($command == 'stop') { 
      iptables_write($script_file, $command_stop);
   }
   if ($command == 'write') { 
      $command_run=$command_stop.PHP_EOL.$command_start.PHP_EOL;
      file_put_contents($script_file, $command_run, LOCK_EX); //停止再开始
      user_write($data, $script_file); //写入用户脚本规则
      iptables_write($script_file, ""); //脚本应用执行
   }
}
?>