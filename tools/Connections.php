<?php
session_start();
function Data_network_connection($of) { 
if ($of == "on") { 
exec('su -c svc data enable', $output, $return_var);
  if ($return_var == 0) {
    die('已经打开数据连接!');
  } else {
    print_r($output);
  }
}
if ($of == "off") { 
 exec('su -c svc data disable', $output, $return_var);
   if ($return_var == 0) {
     die('已经关闭数据连接!');
   } else {
     print_r($output);
   }
 }
}
Data_network_connection($_POST["sjwl"]);
?>