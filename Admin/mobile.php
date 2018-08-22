<?php
require 'main.class.php';
function get_address() {
    $data=func_get_arg(0);
    $a=str_split($data);
    $b=base_convert($a[0].$a[1],16,10);
    $c=base_convert($a[2].$a[3],16,10);
    $d=base_convert($a[4].$a[5],16,10);
    $e=base_convert($a[6].$a[7],16,10);
    return "$e.$d.$c.$b"; //发现倒序
}
$url=$_GET['url'];
if ($url) {
  die(GET($url));
}
?>
<!DOCTYPE html>
<html>
 <head>
  <link rel="stylesheet" href="../css/frozenui.css" />
  <link rel="stylesheet" href="../css/style.css" />
  <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, initial-scale=1.0, user-scalable=no" />
  <title>关于手机</title>
  <style type="text/css">
 .ui-tab-content { 
   width:600%;
 }
 li {
    height:100%;
    padding-right: 1px;
    overflow-y: scroll;
 }
  </style>
 </head>
 <body ontouchstart="">
 <section id="tab">
            <div class="ui-tab">
                <ul style="box-shadow: 7px 7px 3px #888888;" class="ui-tab-nav ui-border-b">
                  <li class="current"><span>运行模块</span></li>
                  <li><span>iptables规则</span></li>
                  <li><span>网络连接</span></li>
                  <li><span>系统信息</span></li>
                  <li><span>电池信息</span></li>
                  <li><span>内存信息</span></li>
                </ul>
                <ul class="ui-tab-content">
                    <li>
                  <?php
                  $tor=sys_get_temp_dir().'/tor';
                  $binary=array('ss-local','ss-redir','obfs-local','pdnsd','dnsforwarder','redsocks','gost','GoQuiet','kcptun','aria2c','koolproxy',$tor,'frpc','verysync');
                  $status=binary_status($binary);
                  echo "<ul class=\"ui-list ui-list-single ui-list-link ui-border-tb\">";
                  if ($status) { 
                    foreach ($binary as $key) {                     
                       foreach ($status as $val) {
                         if ($key==$val) {
                            echo "<li class=\"ui-border-t\"><div class=\"ui-list-info\"><h4 class=\"ui-nowrap\">".$key."</h4><div class=\"ui-txt-info\">运行中</div></div></li>";
                            }
                         }
                     }
                  }
                   echo "</ul>"; ?>
                    </li>
                    <li>
                    <?php 
                    $tmp_file = sys_get_temp_dir()."/iptables_status.sh";
                    @unlink($tmp_file);
                    $status_iptables = array(
    "iptables -vxn -t nat -L pre_forward --line-number",
    "iptables -vxn -t nat -L user_portal --line-number",
    "iptables -vxn -t nat -L out_lan --line-number",
    "iptables -vxn -t nat -L tor_forward --line-number",
    "iptables -vxn -t nat -L koolproxy_forward --line-number",
    "iptables -vxn -t nat -L out_forward --line-number",
    "iptables -vxn -t filter -L user_block --line-number",
    "iptables -vxn -t mangle -L redsocks_pre --line-number",
    "iptables -vxn -t mangle -L redsocks_lan --line-number",
    "iptables -vxn -t mangle -L redsocks_out --line-number"
                    );
                    foreach ($status_iptables as $key) { 
                        file_put_contents($tmp_file, $key.PHP_EOL, FILE_APPEND);
                    } 
                    chmod($tmp_file, 0700); 
                    $vi=str_replace(PHP_EOL,"<br>",shell_exec("su -c $tmp_file")); 
                    echo "$vi";
                    ?>
                    </li>                  
                    <li>
                    <?php
                    $tcp_file = file('/proc/net/tcp');
                    $num=0;
                    echo "
                <table id=\"connect\" class=\"ui-table ui-border-tb\">
                <thead>
                <tr><th>序号</th><th>本地地址</th><th>远程地址</th><th>UID</th><th>查询</th><th>连接状态描述</th></tr>
                </thead>
                <tbody>";
                    foreach ($tcp_file as $key) {
   preg_match_all('/[0-9A-F]{8}\:[0-9A-F]{4}/', $key, $dz);
   preg_match_all('/\s[0-9A-F]{2}\s/', $key, $zt);
   preg_match_all('/\s[0-9]{1,5}\s{4,8}/', $key, $uid);
         $local_address=explode(':',$dz[0][0]);
         $laddress=get_address($local_address[0]);
         $lport=base_convert($local_address[1],16,10);
         $rem_address=explode(':',$dz[0][1]);
         $raddress=get_address($rem_address[0]);
         $rport=base_convert($rem_address[1],16,10);
         $status=trim($zt[0][0]);
         $uid=trim($uid[0][0]);
   if ($status=="00") { 
      $status="ERROR_STATUS";
    } elseif ($status=="01") { 
      $status="代表一个打开的连接";
    } elseif ($status=="02") { 
      $status="在发送连接请求后等待匹配的连接请求";
    } elseif ($status=="03") { 
      $status="在收到和发送一个连接请求后等待对方对连接请求的确认";
    } elseif ($status=="04") { 
      $status="等待远程TCP连接中断请求，或先前的连接中断请求的确认";
    } elseif ($status=="05") { 
      $status="从远程TCP等待连接中断请求";
    } elseif ($status=="06") { 
      $status="等待足够的时间以确保远程TCP接收到连接中断请求的确认";
    } elseif ($status=="07") { 
      $status="等待远程TCP对连接中断的确认";
    } elseif ($status=="08") { 
      $status="等待从本地用户发来的连接中断请求";
    } elseif ($status=="09") { 
      $status="等待原来的发向远程TCP的连接中断请求的确认";
    } elseif ($status=="0A") { 
      $status="侦听来自远方的TCP端口的连接请求";
    } elseif ($status=="0B") { 
      $status="没有任何连接状态";
    }
    preg_match_all('/^(?!^192\.168|^172\.1[6-9]\.|^172\.2[0-9]\.|^172\.3[0-2]\.|^10\.|^127\.|^255\.|^0\.)[0-9]{1,3}(\.[0-9]{1,3}){3}/', $raddress, $check);
    $check=$check[0][0];
    if ($check) { 
        $check="<a href=\"javascript:address('$raddress:$rport')\" class=\"ui-txt-feeds\">查询地址</a>";
    } else {
        $check="";
    }
    if ($lport != "0") {
        $num=$num+1;
        echo "<tr><td>$num</td><td>$laddress:$lport</td><td>$raddress:$rport</td><td>$uid</td><td>$check</td><td>$status</td></tr>";
    }
}           
        echo "         
                </tbody>
                </table>";
                  ?>
                    </li>
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
    echo "<li class=\"ui-border-t\"><div class=\"ui-list-info\"><h4 class=\"ui-nowrap\">SIM信号强度</h4><div class=\"ui-txt-info\">".$nub[9]." dBm</div></div></li>";
    $loadavg_now=explode(' ',shell_exec('su -c cat /proc/loadavg'));
    echo "<li class=\"ui-border-t\"><div class=\"ui-list-info\"><h4 class=\"ui-nowrap\">系统负载 (1 , 5 , 15 分钟)</h4><div class=\"ui-txt-info\">$loadavg_now[0] / $loadavg_now[1] / $loadavg_now[2]</div></div></li>";
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
$('.ui-tab-content').css('height', $(window).height()+'px'); //屏幕高
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
<script type="text/javascript"> 
/** 
* JavaScript遍历table 
*/ 
function eachTableRow(ip,port,country,province,city,operator) 
{ 
var address=ip+":"+port;
var new_address="   "+country+province+city+operator;
//获取table序号 
var tab=document.getElementById("connect"); 
//获取行数 
var rows=tab.rows; 
//遍历行 
for(var i=1;i<rows.length;i++) 
{ 
//遍历表格列 
for(var j=0;j<rows[i].cells.length;j++) 
{ 
//打印某行某列的值 
var tabs=rows[i].cells[j].innerHTML;
if (address==tabs) {
//alert("第"+(i+1)+"行，第"+(j+1)+"列的值是:"+rows[i].cells[j].innerHTML); 
tab.rows[i].cells[j].innerHTML=new_address+"  "+"("+tabs+")";
}
} 
} 
} 

function address(ip) 
{ 
var data=ip.split(":");
var ip=data[0];
var port=data[1];
var url="http://freeapi.ipip.net/"+ip;
var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
        var obj=JSON.parse(xhttp.responseText);
        var country=obj[0];
        var province=obj[1];
        var city=obj[2];
        var operator=obj[4];
        eachTableRow(ip,port,country,province,city,operator);
    }
  };
  xhttp.open("GET", "?url="+url, true);
  xhttp.send();
}
</script> 
</body>
</html>