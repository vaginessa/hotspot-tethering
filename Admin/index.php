<?php
session_start();
require 'main.class.php';
$_SESSION['from']='admin';
session_write_close();
$tor=sys_get_temp_dir().'/tor';
$out=binary_status(array("aria2c","ss-local","koolproxy",$tor));
if ($out) {
  foreach ($out as $val) { 
    if ($val=='aria2c') {
      $aria2_status='<i class="ui-subscript ui-subscript-green">运行中</i>';
    }
    if ($val=='ss-local') {
      $ss_status='<i class="ui-subscript ui-subscript-green">运行中</i>';
    }
    if ($val=='koolproxy') {
      $kool_status='<i class="ui-subscript ui-subscript-green">运行中</i>';
    }
    if ($val==$tor) {
      $tor_status='<i class="ui-subscript ui-subscript-green">运行中</i>';
    }
  }
}
$receive=htmlspecialchars($_POST["receive"]);
if ($receive) { 
  Console($receive);
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
.progress {
  width: 100%;
  background: #ddd;
}
.curRate {
  width: 0%;
  background: #f30;
}
.round-conner {
  height: 10px;
  border-radius: 5px;
}
</style>
<section class="ui-container">
<div class="index-wrap">
<div style="background-color:#0F7884" class="header">
<a href="https://github.com/yiguihai/hotspot-tethering" target="_blank"><h1 style="color:white"><?php system("getprop ro.product.model"); ?></h1></a>
<h2 style="color:#eeeeee"><?php system("getprop gsm.network.type"); ?></h2>
</div>
</div>
</section>

<!--
<div style="display:none;background-color: white;border-style: dotted;width: 98%;padding: 1px;border-radius:25px;">
<img style="display: inline-block;vertical-align: middle;height:18px;" src="../img/notification.png">
<span style="display: inline-block;color: gray;font-size:14px;" id="notification"></span>
</div>
-->
<div class="ui-grid-icon">
<ul>
<li>
<div class="ui-img-icon">
<span style="background-image:url(../img/Shadowsocks.png)" onclick='window.location.href="../Shadowsocks/"'><?php echo $ss_status; ?></span></div>
<h5>Shadowsocks</h5>
<p>一種基於Socks5代理方式的加密傳輸協定</p>
</li>
<li>
<div class="ui-img-icon">
<span style="background-image:url(../img/welcom.png)" onclick='window.location.href="../Welcom/"'></span></div>
<h5>欢迎页</h5>
<p>热点欢迎页设置</p>
</li>
</ul>
</div>

<div class="ui-grid-icon">
<ul>
<li>
<div class="ui-img-icon">
<span style="background-image:url(../img/tor.png)" id='tor'><?php echo $tor_status; ?></span></div>
<h5>Tor</h5>
<p>请戴“套”翻墻</p>
</li>
<li>
<div class="ui-img-icon">
<span style="background-image:url(../img/koolproxy.png)" onclick='window.location.href="../KoolProxy/"'><?php echo $kool_status; ?></span></div>
<h5>KoolProxy</h5>
<p>用于去除网页静广告和视频广告，并且支持https！</p>
</li>
</ul>
</div>

<div class="ui-grid-icon">
<ul>
<li>
<div class="ui-img-icon">
<span style="background-image:url(../img/tileicon.png)" id="aria2"><?php echo $aria2_status; ?></span></div>
<h5>Aria2</h5>
<p>一个轻量级的多协议和多资源命令行下载工具</p>
</li>
<li>
<div class="ui-img-icon">
<span style="background-image:url(../img/Network_shutdown.png)" id="switch"></span></div>
<h5 class="ui-txt-warning">开关控制</h5>
<p>手机数据连接关闭和开启等</p>
</li>
</ul>
</div>

<div class="ui-grid-icon">
<ul>
<li>
<div class="ui-img-icon">
<span style="background-image:url(../img/mobile.png)" onclick='window.location.href="./mobile.php"'></span></div>
<h5>关于手机</h5>
<p>系统、电量、内存等详细信息</p>
</li>
<li>
<div class="ui-img-icon" onclick='if (confirm("要退出登录吗？")==true) Refresh("login.php","logout=logout","logout");'>
<span style="background-image:url(data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTYuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgd2lkdGg9IjUxMnB4IiBoZWlnaHQ9IjUxMnB4IiB2aWV3Qm94PSIwIDAgMTIyLjc3NSAxMjIuNzc2IiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IDAgMCAxMjIuNzc1IDEyMi43NzY7IiB4bWw6c3BhY2U9InByZXNlcnZlIj4KPGc+Cgk8cGF0aCBkPSJNODYsMjguMDc0di0yMC43YzAtMy4zLTIuNjk5LTYtNi02SDZjLTMuMywwLTYsMi43LTYsNnYzLjl2NzguMnYyLjcwMWMwLDIuMTk5LDEuMyw0LjI5OSwzLjIsNS4yOTlsNDUuNiwyMy42MDEgICBjMiwxLDQuNC0wLjM5OSw0LjQtMi43di0yM0g4MGMzLjMwMSwwLDYtMi42OTksNi02di0zMi44SDc0djIzLjhjMCwxLjctMS4zLDMtMywzSDUzLjN2LTMwLjh2LTE5LjV2LTAuNmMwLTIuMi0xLjMtNC4zLTMuMi01LjMgICBsLTI2LjktMTMuOEg3MWMxLjcsMCwzLDEuMywzLDN2MTEuOGgxMlYyOC4wNzR6IiBmaWxsPSIjMDAwMDAwIi8+Cgk8cGF0aCBkPSJNMTAxLjQsMTguMjczbDE5LjUsMTkuNWMyLjUsMi41LDIuNSw2LjIsMCw4LjdsLTE5LjUsMTkuNWMtMi41LDIuNS02LjMwMSwyLjYwMS04LjgwMSwwLjEwMSAgIGMtMi4zOTktMi4zOTktMi4xLTYuNCwwLjIwMS04LjhsOC43OTktOC43SDY3LjVjLTEuNjk5LDAtMy40LTAuNy00LjUtMmMtMi44LTMtMi4xLTguMywxLjUtMTAuM2MwLjktMC41LDItMC44LDMtMC44aDM0LjEgICBjMCwwLTguNjk5LTguNy04Ljc5OS04LjdjLTIuMzAxLTIuMy0yLjYwMS02LjQtMC4yMDEtOC43Qzk1LDE1LjY3NCw5OC45LDE1Ljc3MywxMDEuNCwxOC4yNzN6IiBmaWxsPSIjMDAwMDAwIi8+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPC9zdmc+Cg==)"></span></div>
<h5 class="admin"><?php echo U; ?></h5>
<p>注销登录</p>
</li>
</ul>
</div>

<div class="ui-actionsheet" id="actionsheet">
   <div class="ui-actionsheet-cnt am-actionsheet-down">
        <menu>
        </menu>
        <div class="ui-actionsheet-split-line"></div>
        <button id="cancel">取消</button>
      </div>
   </div>
</div>

<!-- 带标题文字消息 -->
			<div class="ui-dialog" id="dialog">
			    <div class="ui-dialog-cnt">
			        <div class="ui-dialog-bd">
			            <h3></h3>
			            <p></p>
			        </div>
			        <div class="ui-dialog-ft">
			            <button type="button" data-role="button" onclick='$("#dialog").removeClass("show")'>取消</button>
			            <button type="button" data-role="button" class="btn-recommand" onclick=''>确认</button>
			        </div>
			    </div>
			</div>
			
			<div class="ui-loading-block show" id="loading" style="display:none">
                <div class="ui-loading-cnt">
                    <i class="ui-loading-bright"></i>
                    <p></p>
                </div>
            </div>

<div style="background-color:#dec48f;width: 100%;height:25%;text-align:center;" onclick='Refresh("server.php","Refresh=refresh","refresh")'>
 <span id="traffic" style="color:white"></span>
</div>

<span class="demo-desc">CPU使用率: <b id="cpu1"></b></span>
<div class="progress round-conner">
    <div class="curRate round-conner" id="cpu2"></div>
</div>
<span class="demo-desc">剩余内存(RAM):  <b id="ram1">0</b> / <b id="ram2">0</b>&nbspMB</span>
<div class="progress round-conner">
    <div class="curRate round-conner" id="ram3"></div>
</div>
<span class="demo-desc">剩余存储:  <b id="storage1"></b></span>
<div class="progress round-conner">
    <div class="curRate round-conner" id="storage2"></div>
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
function loading(a) { 
if (a==""||a==null) {
    var a="请稍候…";
  }
$(".ui-loading-cnt p").text(a);
if ($("#loading").css("display")=="none"){
    $("#loading").show();
  } else {
    $("#loading").hide();  
  }
  setTimeout(function(){
  if ($("#loading").css("display")!="none"){
    $("#loading").hide();
    alert("时间超时!");
  }
  },10000);
}

//https://blog.csdn.net/lee_magnum/article/details/11555981
function isArrayFn(value){
	if (typeof Array.isArray === "function") {
		return Array.isArray(value);    
	}else{
		return Object.prototype.toString.call(value) === "[object Array]";    
	}
}

function Refresh(a,b,c) { 
var xhttp=new XMLHttpRequest();
xhttp.onreadystatechange=function() { 
if(this.readyState==4&&this.status==200) { 
  if (c=='mobile'||c=='aria2'||c=='tor') {
    $("#loading").hide();
  }
  if (c=='mobile') alert(xhttp.responseText);
  if (c=='aria2') alert(xhttp.responseText);
  if (c=='tor') alert(xhttp.responseText);
  if (c=='refresh') alert("已帮你刷新了网卡和IP地址!");
  if (c=='logout') window.location.href="";
}
};
xhttp.open("POST",a,true);
xhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
xhttp.send(b+"&number="+Math.random());
if(c=='aria2'){ 
    if(b=='receive=update') {
      var x="tracker更新中…";
    }
      loading(x);
}
if(c=='tor'){ 
    if(b=='receive=start') {
      var x="tor启动中…";
    }
      loading(x);
}
if(c=='mobile'){ 
    if(b=='receive=on') {
      var x="正在打开数据网络，请稍候…";
    }
      loading(x);
}

}
</script>
<script type="text/javascript">
function select(content) { 
  $("menu").empty();
  $("menu").html(content);
  $('.ui-actionsheet').addClass('show');
}
$("#actionsheet").click(function(){
  $(".ui-actionsheet").removeClass("show");
});

var content="<button onclick='Refresh(\"\",\"receive=on\",\"mobile\")'>打开数据网络</button>\n<button onclick='Refresh(\"\",\"receive=off\",\"mobile\")'>关闭数据连接</button>\n<button onclick='Toast(\"重启\",\"确认重启手机？\",\"restart\")' class=\"ui-actionsheet-del\">重启</button>\n<button onclick='Toast(\"关机\",\"确认关机？\",\"shutdown\")' class=\"ui-actionsheet-del\">关机</button>\n";
$("#switch").click(function(){
  select(content);
});
var content1="\n<h4>可自行添加更多的webui前端界面</h4>\n<button onclick='Refresh(\"../Aria2/\",\"receive=start\",\"aria2\")'>启动aria2</button>\n<button onclick='Refresh(\"../Aria2/\",\"receive=stop\",\"aria2\")' class=\"ui-actionsheet-del\">关闭aria2</button>\n<button onclick='Refresh(\"../Aria2/\",\"receive=update\",\"aria2\")'>更新tracker</button>\n<button onclick='window.location.href=\"../Aria2/AriaNg/\"'>Aria2Ng</button>\n<button onclick='window.location.href=\"../Aria2/webui-aria2/\"'>webui-aria2</button>\n";
$("#aria2").click(function(){
  select(content1);
});

var content2="\n<button onclick='Refresh(\"../Orbot/\",\"receive=start\",\"tor\")'>启动tor</button>\n<button onclick='Refresh(\"../Orbot/\",\"receive=stop\",\"tor\")' class=\"ui-actionsheet-del\">关闭tor</button>\n<hr>\n<button onclick='window.open(\"https://check.torproject.org/?lang=zh_CN\")'>网络检测</button>\n";
$("#tor").click(function(){
  select(content2);
});

function Toast(a,b,c) { 
  $("#dialog .ui-dialog-bd h3").text(a);
  $("#dialog .ui-dialog-bd p").text(b);
  $("#dialog").addClass("show");  
  $("#dialog .ui-dialog-ft .btn-recommand").attr("onclick",'Refresh(\"\",\"receive='+c+'\",\"mobile\")');
}
</script>

<script type="text/javascript">
if(typeof(EventSource) !== "undefined") {
    var source = new EventSource("server.php");
    source.addEventListener("traffic", function (traffic) {
        document.getElementById("traffic").innerHTML = traffic.data;
   });
    source.addEventListener("memory", function (ram) {
        obj = JSON.parse(ram.data);
        document.getElementById("ram1").innerHTML = obj.RAM;
        document.getElementById("ram2").innerHTML = obj.MemTotal;
        document.getElementById("ram3").style.width = obj.RAM2+"%";
        if (obj.RAM2>75)  {
            color="blue";
        }
        else if (obj.RAM2>50)  {
            color="green";
        }
        else if (obj.RAM2>25)  {
            color="yellow";
        }
        else if (obj.RAM2>0)  {
            color="red";
        }
        document.getElementById("ram3").style.background = color;
   });
   source.addEventListener("cpu", function (cpu) {
        document.getElementById("cpu1").innerHTML = cpu.data + " %";
        document.getElementById("cpu2").style.width = cpu.data + "%";
        if (cpu.data>75)  {
            color="red";
        }
        else if (cpu.data>50)  {
            color="yellow";
        }
        else if (cpu.data>25)  {
            color="green";
        }
        else if (cpu.data>0)  {
            color="blue";
        }
        document.getElementById("cpu2").style.background = color;
   });
   source.addEventListener("storage", function (storage) {
        obj = JSON.parse(storage.data);
        document.getElementById("storage1").innerHTML = obj.storage_free+" / "+obj.storage_total;
        document.getElementById("storage2").style.width = obj.storage_rate+"%";
        if (obj.storage_rate>75)  {
            color="blue";
        }
        else if (obj.storage_rate>50)  {
            color="green";
        }
        else if (obj.storage_rate>25)  {
            color="yellow";
        }
        else if (obj.storage_rate>0)  {
            color="red";
        }
        document.getElementById("storage2").style.background = color;
   });
} else {
    alert("Sorry, your browser does not support server-sent events...");
}
</script>

</body>
</html>