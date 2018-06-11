<?php
function user_mac($ip) {
$arp=file('/proc/net/arp');
 for($i=1;$i<count($arp);$i++) {//逐行读取文件内容
  $ip = explode(" ", $arp[$i]);
  if ($ip == $ip[0]) return $ip[22];
  }
}
/*
$ip = preg_match_all('/[0-9]{1,3}(\.[0-9]{1,3}){3}/', "192.168.43.22", $matchs);
$mac = preg_match_all('/[0-9a-fA-F]{2}(:[0-9a-fA-F]{2}){5}/', "00:0C:29:88:83:1A", $matchs2);
$ip= $matchs[0];
echo $ip[0];
*/
?>