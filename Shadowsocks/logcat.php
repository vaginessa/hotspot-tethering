<?php
//show_source('shadowsocks.php'); 
//$a = popen('ping www.baidu.com', 'r'); 
$pid = file_get_contents('ss-deamon.pid');
$command = popen('su -c logcat --pid '.(int)$pid, 'r'); 
while($i = fgets($command, 2048)) { 
  echo $i."<br>\n"; 
  ob_flush();
  flush(); 
} 
pclose($command);
?>