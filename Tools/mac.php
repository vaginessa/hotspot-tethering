<?php
function user_mac($ip) {
$arp=file('/proc/net/arp');
 for($i=1;$i<count($arp);$i++) {//逐行读取文件内容
  $ip = explode(" ", $arp[$i]);
  if ($ip == $ip[0]) return $ip[22];
  }
}
?>