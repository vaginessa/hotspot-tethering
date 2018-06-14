<?php
session_start();
if (file_exists('./admin.php')) { 
require './admin.php';
}
if ($_COOKIE["user_name"] != U || $_COOKIE["pass_word"] != P) { 
header("Location: ./login.php");
die("需要登录认证才能访问!");
}
require "../tools/busybox.php";
$ps=busybox_check("ps");
$run_list=shell_exec("su -c $ps -A");
if (stripos("$run_list", 'aria2c') !== false) {
    $aria2_status='<i class="ui-subscript ui-subscript-green">运行中</i>';
}
if (stripos("$run_list", 'ss-local') !== false) {
    $ss_status='<i class="ui-subscript ui-subscript-green">运行中</i>';
}
if (stripos("$run_list", 'koolproxy') !== false) {
    $kool_status='<i class="ui-subscript ui-subscript-green">运行中</i>';
}
if (stripos("$run_list", " tor".PHP_EOL) !== false) {
    $tor_status='<i class="ui-subscript ui-subscript-green">运行中</i>';
}
?>
<!DOCTYPE html>
<html>
 <head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, initial-scale=1.0, user-scalable=no" />
  <meta name="format-detection" content="telephone=no, email=no">
  <meta name="HandheldFriendly" content="true">
  <title>热点后台管理</title>
  <link rel="shortcut icon" href="../favicon.ico" />
  <link rel="bookmark" href="../favicon.ico" />
  <link rel="stylesheet" href="../css/frozenui.css" />
  <link rel="stylesheet" href="../css/style.css" />
  <style type="text/css">
    .admin{
    font-style:italic; 
    background-image: -webkit-linear-gradient(left,blue,#66ffff 10%,#cc00ff 20%,#CC00CC 30%, #CCCCFF 40%, #00FFFF 50%,#CCCCFF 60%,#CC00CC 70%,#CC00FF 80%,#66FFFF 90%,blue 100%);
    -webkit-text-fill-color: transparent;
    -webkit-background-clip: text;
    -webkit-background-size: 200% 100%; 
    -webkit-animation: masked-animation 4s linear infinite;
}
@keyframes masked-animation {
    0% {
        background-position: 0  0;
    }
    100% {
        background-position: -100%  0;
    }
}
  </style>    
 </head>
 <body ontouchstart="">
  <section class="ui-container">
   <div class="index-wrap">
    <div class="header">
     <a href="https://github.com/yiguihai/hotspot-tethering" target="_blank"><h1><?php system("getprop ro.product.model"); ?></h1></a>
     <h2><?php system("getprop ro.build.version.release"); ?>&nbsp( <?php system("getprop ro.build.version.sdk");?> )</h2>
    </div>
   </div>
  </section>
  <br>
  <div class="ui-grid-icon ">
   <ul>
    <li>
     <div class="ui-img-icon">
      <span style="background-image:url(../img/fileadmin.png)"></span>
     </div><a href="../Fileadmin/"><h5>爱特文件管理器</h5></a><p>致力于提供简单、快捷的网站文件管理方案</p></li>
    <li>
     <div class="ui-img-icon">
      <span style="background-image:url(../img/tileicon.png)"><?php echo $aria2_status; ?></span>
     </div><a href="../Aria2/"><h5>AriaNg</h5></a><p>一个让 aria2 更容易使用的现代 Web 前端</p></li>
   </ul>
  </div>
  <div class="ui-grid-icon ">
   <ul>
    <li>
     <div class="ui-img-icon">
      <span style="background-image:url(../img/Shadowsocks.png)"><?php echo $ss_status; ?></span>
     </div><h5><a href="../Shadowsocks/"><h5>Shadowsocks</h5></a><p>一種基於Socks5代理方式的加密傳輸協定</p></li>
     <li>
     <div class="ui-img-icon">
      <span style="background-image:url(../img/koolproxy.png)"><?php echo $kool_status; ?></span>
     </div><h5><a href="../KoolProxy/"><h5>KoolProxy</h5></a><p>用于去除网页静广告和视频广告，并且支持https！</p></li>
   </ul>
  </div>
  <div class="ui-grid-icon ">
   <ul>
    <li>
     <div class="ui-img-icon">
      <span style="background-image:url(../img/fulisearch.png)"></span>
     </div><a href="../Search/"><h5>福利搜</h5></a><p>使用Google CSE定制的专用搜索(早期作品)</p></li>
     <li>
     <div class="ui-img-icon">
      <span style="background-image:url(../img/Network_shutdown.png)"></span>
     </div><h5 class="ui-txt-warning" id="btn1">关闭网络</h5><p>手机数据连接关闭和开启</p></li>
   </ul>
  </div>
  <div class="ui-grid-icon">
   <ul>
    <li>
     <div class="ui-img-icon">
      <span style="background-image:url(../img/tor.png)"><?php echo $tor_status; ?></span>
     </div><a href="../Orbot/"><h5>Tor</h5></a><p>请戴“套”翻墻</p></li>
     <li>
     <div class="ui-img-icon">
      <span style="background-image:url(../img/welcom.png)"></span>
     </div><a href="../Welcom/"><h5>欢迎页</h5></a><p>热点欢迎页设置</p></li>
   </ul>
  </div>
    <div class="ui-grid-icon">
   <ul>
   <li>
     <div class="ui-img-icon">
      <span style="background-image:url(../img/mobile.png)"></span>
     </div><a href="#"><h5>关于手机</h5></a><p>电量等状态信息</p></li>
    <li>
     <div class="ui-img-icon" onclick="logout()">
      <span style="background-image:url(data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTYuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgd2lkdGg9IjUxMnB4IiBoZWlnaHQ9IjUxMnB4IiB2aWV3Qm94PSIwIDAgMTIyLjc3NSAxMjIuNzc2IiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IDAgMCAxMjIuNzc1IDEyMi43NzY7IiB4bWw6c3BhY2U9InByZXNlcnZlIj4KPGc+Cgk8cGF0aCBkPSJNODYsMjguMDc0di0yMC43YzAtMy4zLTIuNjk5LTYtNi02SDZjLTMuMywwLTYsMi43LTYsNnYzLjl2NzguMnYyLjcwMWMwLDIuMTk5LDEuMyw0LjI5OSwzLjIsNS4yOTlsNDUuNiwyMy42MDEgICBjMiwxLDQuNC0wLjM5OSw0LjQtMi43di0yM0g4MGMzLjMwMSwwLDYtMi42OTksNi02di0zMi44SDc0djIzLjhjMCwxLjctMS4zLDMtMywzSDUzLjN2LTMwLjh2LTE5LjV2LTAuNmMwLTIuMi0xLjMtNC4zLTMuMi01LjMgICBsLTI2LjktMTMuOEg3MWMxLjcsMCwzLDEuMywzLDN2MTEuOGgxMlYyOC4wNzR6IiBmaWxsPSIjMDAwMDAwIi8+Cgk8cGF0aCBkPSJNMTAxLjQsMTguMjczbDE5LjUsMTkuNWMyLjUsMi41LDIuNSw2LjIsMCw4LjdsLTE5LjUsMTkuNWMtMi41LDIuNS02LjMwMSwyLjYwMS04LjgwMSwwLjEwMSAgIGMtMi4zOTktMi4zOTktMi4xLTYuNCwwLjIwMS04LjhsOC43OTktOC43SDY3LjVjLTEuNjk5LDAtMy40LTAuNy00LjUtMmMtMi44LTMtMi4xLTguMywxLjUtMTAuM2MwLjktMC41LDItMC44LDMtMC44aDM0LjEgICBjMCwwLTguNjk5LTguNy04Ljc5OS04LjdjLTIuMzAxLTIuMy0yLjYwMS02LjQtMC4yMDEtOC43Qzk1LDE1LjY3NCw5OC45LDE1Ljc3MywxMDEuNCwxOC4yNzN6IiBmaWxsPSIjMDAwMDAwIi8+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPC9zdmc+Cg==)"></span>
     </div><h5 class="admin"><?php echo U; ?></h5><p>注销登录</p></li>
 </div>
		<div class="ui-actionsheet" id="actionsheet1">
				<div class="ui-actionsheet-cnt am-actionsheet-down">
					<h4>这将会关闭或者开启手机热点的数据网络连接</h4>
					<button id="ktwl">开启数据网络</button>
					<button id="gbwl" class="ui-actionsheet-del">关闭数据连接</button>
					<div class="ui-actionsheet-split-line"></div>
					<button id="cancel">取消</button>
				</div>
			</div>
		</div>
		<br>
	<div style="background-color:#854b40;width:100%;height:100%;text-align:center;line-height:25px;">
　<span id="ll" style="color: white"></span>
</div>
	<table class="ui-table ui-border-tb">
	</table>
  <section class="ui-container">
   <div class="index-wrap">
    <div class="footer">
    <a href="mailto:yiguihai@gmail.com" id="footer"></a>
    </div>
   </div>
  </section>
<script src="../js/zepto.min.js"></script>
<script src="../js/index.js"></script>
<script type="text/javascript">		
function net(of) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var at = xhttp.responseText;
            alert(at);
        }
    };
    xhttp.open("POST", "../tools/Connections.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send(encodeURI("sjwl=" + of));
}
  $("#ktwl").click(function(){
  $(".ui-actionsheet").removeClass("show");
  net("on");
  });
  $("#gbwl").click(function(){
  $(".ui-actionsheet").removeClass("show");
  net("off");
  });
  </script>
  
  <script type="text/javascript">
function logout() {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            window.location.href="";
        }
    };
    xhttp.open("POST", "./login.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("logout="+Math.random());
}
  </script>

  <script type="text/javascript">
  var date = new Date();
  var year = date.getFullYear();
  document.getElementById("footer").innerHTML="Copyright © 2018-"+year+" 爱翻墙的红杏 All Rights Reserved";
</script> 
<script type="text/javascript">
setInterval(
function(){ 
var xmlhttp;
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    document.getElementById("ll").innerHTML=xmlhttp.responseText;
    }
  }
xmlhttp.open("GET","ll.php?t="+Math.random(),true);
xmlhttp.send();
}
, 1000); //1秒查一次
</script> 
</body>
</html>