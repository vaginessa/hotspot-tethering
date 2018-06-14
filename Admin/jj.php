<?php
    $arp_file = explode(PHP_EOL, file_get_contents("/proc/net/dev"));
    foreach ($arp_file as $arp) {
        //print_r($arp."<br>");
    }
    $battery_file=shell_exec("su -c cat /sys/class/power_supply/battery/uevent");
    $battery_arr = parse_ini_string($battery_file);
    foreach ($battery_arr as $key => $value) {
        print_r($key."=".$value."<br>");
    }
//<!--setInterval(function(){ alert("Hello"); }, 3000);-->
?>