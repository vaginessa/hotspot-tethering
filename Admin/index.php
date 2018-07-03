<?php
if (!file_exists('./admin.php')) die('管理员密码配置文件遗失');
require './admin.php';
if ($_COOKIE["user_name"] != hash('sha512',U) || $_COOKIE["pass_word"] != hash('sha512',P)) { 
header("Location: ./login.php");
die("需要登录认证才能访问!");
}
session_start();
$_SESSION['from']='admin';
session_write_close();
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
<title>热点后台管理</title>
<link rel="shortcut icon" href="../favicon.ico" />
<link rel="stylesheet" href="../css/frozenui.css" />
<link rel="stylesheet" href="../css/style.css" />
</head>
<body ontouchstart="">
<style type="text/css">
.admin {
	font-style: italic;
	background-image: -webkit-linear-gradient(left,blue,#66ffff 10%,#cc00ff 20%,#CC00CC 30%,#CCCCFF 40%,#00FFFF 50%,#CCCCFF 60%,#CC00CC 70%,#CC00FF 80%,#66FFFF 90%,blue 100%);
	-webkit-text-fill-color: transparent;
	-webkit-background-clip: text;
	-webkit-background-size: 200% 100%;
	-webkit-animation: masked-animation 4s linear infinite
}

@keyframes masked-animation {
	0% {
		background-position: 0 0
	}

	100% {
		background-position: -100% 0
	}
}
</style>
<section class="ui-container">
<div class="index-wrap">
<div style="background-color:#0F7884" class="header">
<a href="https://github.com/yiguihai/hotspot-tethering" target="_blank"><h1 style=color:white><?php system("getprop ro.product.model"); ?></h1></a>
<h2 style="color:#eeeeee"><?php system("getprop gsm.network.type"); ?></h2>
</div>
</div>
</section>
<div style="display:none;background-color: white;border-style: dotted;width: 98%;padding: 1px;border-radius:25px;">
<img style="display: inline-block;vertical-align: middle;height:18px;" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAPASURBVGhD7dlJyI1RHMfx1zwTmWcZV6xQFrJSIrZiISVTyk4sFClKKcqQkpmUWYaFWFBiRRLJsGBhSEnIPHx/D+ft/5zO89znue89b93e91ef4nHOuf733nOe85zb0JoWkA7YjNe4hX6ou7TBUfwx1qHusgW2CDmEusoK+EXIMZTJBPT998fmz1z8RFML2Q31+QbNs+5otkzFZ/gFOEUL0SLhvxkvMQvRMwZvYV/cV+YTuQu//y+shxaSKNGy+gT+C/vKFDIQav8b/jjH0Q41TVfchv9iIWUKcZmOx/DHOoCafTJ6V87Df5Es1RSi9MIF+ONpEahJ3MpSVLWFKHr3D8OOp6/dTDQpa2EHLSKrkKG4CE3wJbqQEX0DzsGO+Qp9UFUWIjQJK8kqZD9su7PohlB64ils+21IZRpu4lkF32EHKiqrEG1d/LZ3oLkRyhRoKXZtv2AEGvMcdrBayypkJB7Cb38NujmGcgS2bepTsf8QQ95k1xbkJPw+GxDKKNi7/xu0RxI7QAx5hShamXbC9vmBcQjlCmzbOUhiL8ZQqRClI27A9tOyG8oC2HbbkcRejKFIIcpwfIXrp8kcmvjaGtnx9RSaxF6MoWghykHYvvMRil2gVHAyT2zHGMoUouca27fxa+NFN1Lbrj9SF2IoU8gQ2L66o4fif3J6qkxdiKFMIV1g+2oBCGUHbDvd1FMXYihTiD+RLyGUfbDtJiJ1IYYyhcyA7bsXoZyBbTcYqQsxlClkK2zfZQjlHlwb7b86I9UxhqKF9IC2HK6f/oOpTeH/aFtjtykPkMRdiKVoIbtg+2nzGIoeqmw7zZck9mIMRQpZDL+f5kso/vZ/EZLYizFUKmQldCBn+5xAKHrA+gjXTudpupbkPewgtZZViDaK/o1NdMTUG6Fsgm2rA/PGLIXdrNVaViH+TU30LK5Dv1C0xH6Ca6vHbp1ypqJ3R+9CnrKnJ05WIZrMtt0j6MEplLa4Ctv+FKpK2fMsJ6uQ2dA3QWcBe5B18KDoyNSOqTk1HlWnzAmjkzfZ9Z/Xp52XVfDHXI0mp+iZr5NXSF70CKxzNP8ISqePNTs2LXIK71RTiH7oOQ1/rLzjoqpT6XcRp0whetJbjnfwx7mPqk8XKyXvlyqnaCFama4jNMZlVJpLTU7Wb4dO0ULGwu+rYyHdBFVksyT0a65TtJBOeAHXT0+Hk9Cs0Sri/77ulJkjw7AG81CzlalstDvw79ai/VTdRUujVhdbyEbUZQbB3f0/YDTqNlplJmNA8rfWtLg0NPwFp2O/VyComU0AAAAASUVORK5CYII=">
<span style="display: inline-block;color: gray;font-size:14px;" id="notification"></span>
</div>
<br>
<div class="ui-grid-icon">
<ul>
<li>
<div class="ui-img-icon">
<span style="background-image:url(../img/fileadmin.png)" onclick='window.location.href="../Fileadmin/"'></span>
</div><h5>爱特文件管理器</h5><p>致力于提供简单、快捷的网站文件管理方案</p></li>
<li>
<div class="ui-img-icon">
<span style="background-image:url(../img/tileicon.png)" onclick='window.location.href="../Aria2/"'><?php echo $aria2_status; ?></span>
</div><h5>AriaNg</h5><p>一个让 aria2 更容易使用的现代 Web 前端</p></li>
</ul>
</div>
<div class="ui-grid-icon">
<ul>
<li>
<div class="ui-img-icon">
<span style="background-image:url(../img/Shadowsocks.png)" onclick='window.location.href="../Shadowsocks/"'><?php echo $ss_status; ?></span>
</div><h5>Shadowsocks</h5><p>一種基於Socks5代理方式的加密傳輸協定</p></li>
<li>
<div class=ui-img-icon>
<span style="background-image:url(../img/koolproxy.png)" onclick='window.location.href="../KoolProxy/"'><?php echo $kool_status; ?></span>
</div><h5>KoolProxy</h5><p>用于去除网页静广告和视频广告，并且支持https！</p></li>
</ul>
</div>
<div class="ui-grid-icon">
<ul>
<li>
<div class="ui-img-icon">
<span style="background-image:url(../img/fulisearch.png)" onclick='window.location.href="../Search/"'></span>
</div><h5>福利搜</h5><p>使用Google CSE定制的专用搜索(早期作品)</p></li>
<li>
<div class="ui-img-icon">
<span style="background-image:url(../img/Network_shutdown.png)" id="network"></span>
</div><h5 class="ui-txt-warning">关闭网络</h5><p>手机数据连接关闭和开启</p></li>
</ul>
</div>
<div class="ui-grid-icon">
<ul>
<li>
<div class="ui-img-icon">
<span style="background-image:url(../img/tor.png)" onclick='window.location.href="../Orbot/"'><?php echo $tor_status; ?></span>
</div><h5>Tor</h5><p>请戴“套”翻墻</p></li>
<li>
<div class="ui-img-icon">
<span style="background-image:url(../img/welcom.png)" onclick='window.location.href="../Welcom/"'></span>
</div><h5>欢迎页</h5><p>热点欢迎页设置</p></li>
</ul>
</div>
<div class="ui-grid-icon">
<ul>
<li>
<div class="ui-img-icon">
<span style="background-image:url(../img/mobile.png)" onclick='window.location.href="./mobile.php"'></span>
</div><h5>关于手机</h5><p>系统、电量、内存等状态信息</p></li>
<li>
<div class="ui-img-icon" onclick='Refresh("login.php","logout=logout","logout")'>
<span style="background-image:url(data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTYuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgd2lkdGg9IjUxMnB4IiBoZWlnaHQ9IjUxMnB4IiB2aWV3Qm94PSIwIDAgMTIyLjc3NSAxMjIuNzc2IiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IDAgMCAxMjIuNzc1IDEyMi43NzY7IiB4bWw6c3BhY2U9InByZXNlcnZlIj4KPGc+Cgk8cGF0aCBkPSJNODYsMjguMDc0di0yMC43YzAtMy4zLTIuNjk5LTYtNi02SDZjLTMuMywwLTYsMi43LTYsNnYzLjl2NzguMnYyLjcwMWMwLDIuMTk5LDEuMyw0LjI5OSwzLjIsNS4yOTlsNDUuNiwyMy42MDEgICBjMiwxLDQuNC0wLjM5OSw0LjQtMi43di0yM0g4MGMzLjMwMSwwLDYtMi42OTksNi02di0zMi44SDc0djIzLjhjMCwxLjctMS4zLDMtMywzSDUzLjN2LTMwLjh2LTE5LjV2LTAuNmMwLTIuMi0xLjMtNC4zLTMuMi01LjMgICBsLTI2LjktMTMuOEg3MWMxLjcsMCwzLDEuMywzLDN2MTEuOGgxMlYyOC4wNzR6IiBmaWxsPSIjMDAwMDAwIi8+Cgk8cGF0aCBkPSJNMTAxLjQsMTguMjczbDE5LjUsMTkuNWMyLjUsMi41LDIuNSw2LjIsMCw4LjdsLTE5LjUsMTkuNWMtMi41LDIuNS02LjMwMSwyLjYwMS04LjgwMSwwLjEwMSAgIGMtMi4zOTktMi4zOTktMi4xLTYuNCwwLjIwMS04LjhsOC43OTktOC43SDY3LjVjLTEuNjk5LDAtMy40LTAuNy00LjUtMmMtMi44LTMtMi4xLTguMywxLjUtMTAuM2MwLjktMC41LDItMC44LDMtMC44aDM0LjEgICBjMCwwLTguNjk5LTguNy04Ljc5OS04LjdjLTIuMzAxLTIuMy0yLjYwMS02LjQtMC4yMDEtOC43Qzk1LDE1LjY3NCw5OC45LDE1Ljc3MywxMDEuNCwxOC4yNzN6IiBmaWxsPSIjMDAwMDAwIi8+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPC9zdmc+Cg==)"></span>
</div><h5 class="admin"><?php echo U; ?></h5><p>注销登录</p></li>
</div>
<div class="ui-actionsheet" id="actionsheet1">
<div class="ui-actionsheet-cnt am-actionsheet-down">
<h4>这将会关闭或者开启手机热点的数据网络连接</h4>
<button onclick='Refresh("../tools/Connections.php","sjwl=on","data")'>开启数据网络</button>
<button onclick='Refresh("../tools/Connections.php","sjwl=off","data")' class="ui-actionsheet-del">关闭数据连接</button>
<div class="ui-actionsheet-split-line"></div>
<button id="cancel">取消</button>
</div>
</div>
</div>
<br>
<div style="background-color:#dec48f;width: 100%;height:25%;text-align:center;" onclick='Refresh("server.php","Refresh=refresh","refresh")'>
 <span id="traffic" style="color:white"></span>
</div>
<section class="ui-container">
<div class="index-wrap">
<div class="footer">
<a href="" id="footer"></a>
</div>
</div>
</section>

<script src="../js/zepto.min.js"></script>
<script src="../js/footer.js"></script>
<script type="text/javascript">
function Refresh(a,b,c) { 
var xhttp=new XMLHttpRequest();
xhttp.onreadystatechange=function() { 
if(this.readyState==4&&this.status==200) { 
if(c=='data')alert(xhttp.responseText);
if(c=='refresh')alert("已帮你刷新了网卡和IP地址!");
if(c=='logout')window.location.href="";
}
};
xhttp.open("POST",a,true);
xhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
xhttp.send(b+"&number="+Math.random());
}
</script>
<script type="text/javascript">
$("#network").click(function(){
$('.ui-actionsheet').addClass('show');
});
$("#actionsheet1").click(function(){
$(".ui-actionsheet").removeClass("show");
});
</script>
<!--
<script type="text/javascript">
setInterval(function() { 
xmlhttp=new XMLHttpRequest();
xmlhttp.onreadystatechange=function() {
if(xmlhttp.readyState==4&&xmlhttp.status==200) { 
document.getElementById("traffic").innerHTML=xmlhttp.responseText;
}
}
xmlhttp.open("GET","server.php?t="+Math.random(),true);
xmlhttp.send();
},1000);
</script>
-->
<script type="text/javascript">
if(typeof(EventSource) !== "undefined") {
    var source = new EventSource("server.php");
    source.addEventListener("traffic", function (traffic) {
        document.getElementById("traffic").innerHTML = traffic.data;
   });
    source.addEventListener("notification", function (toast) {
        document.getElementById("notification").innerHTML = toast.data;
   });
} else {
    alert("Sorry, your browser does not support server-sent events...");
}
</script>
</body>
</html>