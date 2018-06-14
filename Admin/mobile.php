<!DOCTYPE html>
<html>
 <head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, initial-scale=1.0, user-scalable=no" />
  <meta name="format-detection" content="telephone=no, email=no">
  <meta name="HandheldFriendly" content="true">
  <link rel="stylesheet" href="../css/frozenui.css" />
  <link rel="stylesheet" href="../css/style.css" />
  <title>关于手机</title>
 </head>
 <body ontouchstart="">
<div class="demo-item"><p class="demo-desc">系统信息</p><div class="demo-block">
 <ul class="ui-list ui-list-single ui-list-link ui-border-tb">       
<?php
    echo "<li class=\"ui-border-t\"><div class=\"ui-list-info\"><h4 class=\"ui-nowrap\">型号</h4><div class=\"ui-txt-info\">".shell_exec('getprop ro.product.model')."</div></div></li>";
    echo "<li class=\"ui-border-t\"><div class=\"ui-list-info\"><h4 class=\"ui-nowrap\">正式名称</h4><div class=\"ui-txt-info\">".shell_exec('getprop ro.product.name')."</div></div></li>";
    echo "<li class=\"ui-border-t\"><div class=\"ui-list-info\"><h4 class=\"ui-nowrap\">Android版本</h4><div class=\"ui-txt-info\">".shell_exec('getprop ro.build.version.release')."</div></div></li>";
    echo "<li class=\"ui-border-t\"><div class=\"ui-list-info\"><h4 class=\"ui-nowrap\">品牌</h4><div class=\"ui-txt-info\">".shell_exec('getprop ro.product.brand')."</div></div></li>";
    echo "<li class=\"ui-border-t\"><div class=\"ui-list-info\"><h4 class=\"ui-nowrap\">主板平台</h4><div class=\"ui-txt-info\">".shell_exec('getprop ro.board.platform')."</div></div></li>";
    echo "<li class=\"ui-border-t\"><div class=\"ui-list-info\"><h4 class=\"ui-nowrap\">CPU版本</h4><div class=\"ui-txt-info\">".shell_exec('getprop ro.product.cpu.abi')."</div></div></li>";
    echo "<li class=\"ui-border-t\"><div class=\"ui-list-info\"><h4 class=\"ui-nowrap\">CPU品牌</h4><div class=\"ui-txt-info\">".shell_exec('getprop ro.product.cpu.abi2')."</div></div></li>";
    echo "<li class=\"ui-border-t\"><div class=\"ui-list-info\"><h4 class=\"ui-nowrap\">生产厂家</h4><div class=\"ui-txt-info\">".shell_exec('getprop ro.product.manufacturer')."</div></div></li>";
    echo "<li class=\"ui-border-t\"><div class=\"ui-list-info\"><h4 class=\"ui-nowrap\">SDK版本</h4><div class=\"ui-txt-info\">".shell_exec('getprop ro.build.version.sdk')."</div></div></li>";
    $uptime_file=explode(' ', shell_exec("su -c cat /proc/uptime"));
     echo "<li class=\"ui-border-t\"><div class=\"ui-list-info\"><h4 class=\"ui-nowrap\">已开机时间</h4><div class=\"ui-txt-info\">".round($uptime_file[0]/60/60, 2)." 小时</div></div></li>";
    $signal=shell_exec("su -c dumpsys telephony.registry");
    $signal_text=strpos($signal, 'SignalStrength: ');
    $nub=explode(' ', substr($signal,$signal_text,100));
    echo "<li class=\"ui-border-t\"><div class=\"ui-list-info\"><h4 class=\"ui-nowrap\">SIM信号强度</h4><div class=\"ui-txt-info\">".$nub[9]."</div></div></li>";
    echo "</div></div>";
    echo "<div class=\"demo-item\"><p class=\"demo-desc\">电池信息</p><div class=\"demo-block\"><ul class=\"ui-list ui-list-single ui-list-link ui-border-tb\">";
    $battery_file=shell_exec("su -c cat /sys/class/power_supply/battery/uevent");
    $battery_arr = parse_ini_string($battery_file);
    foreach ($battery_arr as $key => $value) {
        if($key=="POWER_SUPPLY_CAPACITY") {
          $value="(电池电量 $value %)";
        }
        if($key=="POWER_SUPPLY_STATUS"&&$value=="Discharging") {
          $value="(放电状态)";
        }
        if($key=="POWER_SUPPLY_STATUS"&&$value=="Charging") {
          $value="(充电状态)";
        }
        if($key=="POWER_SUPPLY_TEMP") {
          $value="(电池温度 ".($value/10)." °C)";
        }
        //print_r($key."=".$value."<br>");
        echo "<li class=\"ui-border-t\"><div class=\"ui-list-info\"><h4 class=\"ui-nowrap\">".$key."</h4><div class=\"ui-txt-info\">".$value."</div></div></li>";
    }
?>
</ul>
</div>
</div>
</body>
</html>