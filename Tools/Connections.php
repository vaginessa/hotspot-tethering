<?php
session_start();
if (empty($_SESSION['from'])) die("拒绝访问!");
function Data_network_connection($of) { 
if ($of == "on") { 
shell_exec("su -c svc data enable"); 
die("已经执行开启!");
}
if ($of == "off") { 
shell_exec("su -c svc data disable"); 
die("已经执行关闭!");
}
}
Data_network_connection($_POST["sjwl"]);
?>