<!DOCTYPE html>
<html>
 <head>
  <link rel="stylesheet" href="../css/frozenui.css" />
  <link rel="stylesheet" href="../css/style.css" />
  <title>关于手机</title>
 </head>
 <body ontouchstart="">
 <section id="tab">
    <div class="demo-item">
        <p class="demo-desc">系统状态</p>
        <div class="demo-block">
            <div class="ui-tab">
                <ul style="box-shadow: 7px 7px 3px #888888;" class="ui-tab-nav ui-border-b">
                  <li class="current"><span>系统信息</span></li>
                  <li><span>电池信息</span></li>
                  <li><span>内存信息</span></li>
                </ul>
                <ul class="ui-tab-content" style="width:300%">
                    <li>
                    <?php 
    echo "<ul class=\"ui-list ui-list-single ui-list-link ui-border-tb\">";
    echo "<li class=\"ui-border-t\"><div class=\"ui-list-info\"><h4 class=\"ui-nowrap\">型号</h4><div class=\"ui-txt-info\">".shell_exec('getprop ro.product.model')."</div></div></li>";
    echo "<li class=\"ui-border-t\"><div class=\"ui-list-info\"><h4 class=\"ui-nowrap\">正式名称</h4><div class=\"ui-txt-info\">".shell_exec('getprop ro.product.name')."</div></div></li>";
    echo "<li class=\"ui-border-t\"><div class=\"ui-list-info\"><h4 class=\"ui-nowrap\">Android版本</h4><div class=\"ui-txt-info\">".shell_exec('getprop ro.build.version.release')."</div></div></li>";
    echo "<li class=\"ui-border-t\"><div class=\"ui-list-info\"><h4 class=\"ui-nowrap\">Android安全补丁程序级别</h4><div class=\"ui-txt-info\">".shell_exec('getprop ro.build.version.security_patch')."</div></div></li>";
    echo "<li class=\"ui-border-t\"><div class=\"ui-list-info\"><h4 class=\"ui-nowrap\">SDK版本</h4><div class=\"ui-txt-info\">".shell_exec('getprop ro.build.version.sdk')."</div></div></li>";
    echo "<li class=\"ui-border-t\"><div class=\"ui-list-info\"><h4 class=\"ui-nowrap\">品牌</h4><div class=\"ui-txt-info\">".shell_exec('getprop ro.product.brand')."</div></div></li>";
    echo "<li class=\"ui-border-t\"><div class=\"ui-list-info\"><h4 class=\"ui-nowrap\">主板平台</h4><div class=\"ui-txt-info\">".shell_exec('getprop ro.board.platform')."</div></div></li>";
    echo "<li class=\"ui-border-t\"><div class=\"ui-list-info\"><h4 class=\"ui-nowrap\">CPU版本</h4><div class=\"ui-txt-info\">".shell_exec('getprop ro.product.cpu.abi')."</div></div></li>";
    echo "<li class=\"ui-border-t\"><div class=\"ui-list-info\"><h4 class=\"ui-nowrap\">CPU品牌</h4><div class=\"ui-txt-info\">".shell_exec('getprop ro.product.cpu.abi2')."</div></div></li>";
    echo "<li class=\"ui-border-t\"><div class=\"ui-list-info\"><h4 class=\"ui-nowrap\">生产厂家</h4><div class=\"ui-txt-info\">".shell_exec('getprop ro.product.manufacturer')."</div></div></li>";
    $uptime_file=explode(' ', shell_exec("su -c cat /proc/uptime"));
    echo "<li class=\"ui-border-t\"><div class=\"ui-list-info\"><h4 class=\"ui-nowrap\">已开机时间</h4><div class=\"ui-txt-info\">".round($uptime_file[0]/60/60, 2)." 小时</div></div></li>";
    $signal=shell_exec('su -c dumpsys telephony.registry');
    $signal_text=strpos($signal, 'SignalStrength: ');
    $nub=explode(' ', substr($signal,$signal_text,100));
    echo "<li class=\"ui-border-t\"><div class=\"ui-list-info\"><h4 class=\"ui-nowrap\">SIM信号强度</h4><div class=\"ui-txt-info\">".$nub[9]."</div></div></li>";
    echo "</ul>"; ?>
                    </li>
                    <li>
                    <?php 
    echo "<ul class=\"ui-list ui-list-single ui-list-link ui-border-tb\">";
    $battery_file=shell_exec('su -c cat /sys/class/power_supply/battery/uevent');
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
    echo "<li class=\"ui-border-t\"><div class=\"ui-list-info\"><h4 class=\"ui-nowrap\">".$key."</h4><div class=\"ui-txt-info\">".$value."</div></div></li>";
    }
    echo "</ul>"; ?>
                    </li>
                    <li>
                    <?php 
    echo "<ul class=\"ui-list ui-list-single ui-list-link ui-border-tb\">";
    foreach (explode(PHP_EOL, file_get_contents('/proc/meminfo')) as $key) {
    $key=explode(':', $key);
    foreach (explode('kB', $key[1]) as $value) {
    $value=trim($value);
        if ($value > 0) { 
    echo "<li class=\"ui-border-t\"><div class=\"ui-list-info\"><h4 class=\"ui-nowrap\">".$key[0]."</h4><div class=\"ui-txt-info\">".round($value/1024,2)." MB</div></div></li>";
        }
    }
}
    echo "</ul>"; ?>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>
<script src="../js/zepto.min.js"></script>
<script type="text/javascript">
(function() {
    var record = 0;
    var origin_l;
    $('.ui-tab-nav').eq(0).find('li').on('click',function() {
                  $(this).parent().find('li').removeClass('current');
                  $(this).addClass('current');
                  $('.ui-tab-content').eq(0).css({
                    'transform':'translate3d(-'+($(this).index()*$('.ui-tab-content li').offset().width)+'px,0,0)',
                    'transition':'transform 0.5s linear'
                })
    });
})(window, undefined)
</script>
</body>
</html>