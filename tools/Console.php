<?php
function Console($of) { 
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
if ($of == "restart") { 
exec('su -c reboot', $output, $return_var);
  if ($return_var == 0) {
    die('重启中…');
  } else {
    print_r($output);
  }
}
if ($of == "shut_down") { 
exec('su -c reboot -p', $output, $return_var);
  if ($return_var == 0) {
    die('关机中…');
  } else {
    print_r($output);
  }
}
}
Console($_POST["sjkz"]);
?>