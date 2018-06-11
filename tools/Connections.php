<?php
session_start();
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