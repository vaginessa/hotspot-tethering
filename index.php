<?php
header("Content-Type: text/html;charset=utf-8");
require "./Tool/busybox.php";
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
  <title>首页</title>
  <link rel="shortcut icon" href="./favicon.ico" />
  <link rel="bookmark" href="./favicon.ico" />
  <link rel="stylesheet" href="./css/frozenui.css" />
  <link rel="stylesheet" href="./css/style.css" />
 </head>
 <body ontouchstart="">
  <section class="ui-container">
   <div class="index-wrap">
    <div class="header">
     <a href="http://frozenui.github.io" target="_blank"><h1>热点远程管理</h1></a>
     <h2>体验更自然 触达更高效</h2>
    </div>
   </div>
  </section>
  <br>
  <div class="ui-grid-icon " id="tools">
   <ul>
    <li>
     <div class="ui-img-icon">
      <span style="background-image:url(./img/fileadmin.png)"></span>
     </div><a href="./fileadmin/"><h5>爱特文件管理器</h5></a><p>致力于提供简单、快捷的网站文件管理方案</p></li>
    <li>
     <div class="ui-img-icon">
      <span style="background-image:url(./img/tileicon.png)"><?php echo $aria2_status; ?></span>
     </div><a href="./Aria2/"><h5>AriaNg</h5></a><p>一个让 aria2 更容易使用的现代 Web 前端</p></li>
   </ul>
  </div>
  <br>
  <div class="ui-grid-icon " id="tools">
   <ul>
    <li>
     <div class="ui-img-icon">
      <span style="background-image:url(./img/Shadowsocks.png)"><?php echo $ss_status; ?></span>
     </div><h5><a href="./Shadowsocks/"><h5>Shadowsocks</h5></a><p>一種基於Socks5代理方式的加密傳輸協定</p></li>
     <li>
     <div class="ui-img-icon">
      <span style="background-image:url(./img/koolproxy.png)"><?php echo $kool_status; ?></span>
     </div><h5><a href="./KoolProxy/"><h5>KoolProxy</h5></a><p>用于去除网页静广告和视频广告，并且支持https！</p></li>
   </ul>
  </div>
  <br>
  <div class="ui-grid-icon " id="tools">
   <ul>
    <li>
     <div class="ui-img-icon">
      <span style="background-image:url(./img/fulisearch.png)"></span>
     </div><a href="./Search/"><h5>福利搜</h5></a><p>使用Google CSE定制的专用搜索</p></li>
     <li>
     <div class="ui-img-icon">
      <span style="background-image:url(./img/Network_shutdown.png)"></span>
     </div><h5 class="ui-txt-warning" id="btn1">关闭网络</h5><p>手机数据连接关闭和开启</p></li>
   </ul>
  </div>
  <br>
  <div class="ui-grid-icon " id="tools">
   <ul>
    <li>
     <div class="ui-img-icon">
      <span style="background-image:url(./img/tor.png)"><?php echo $tor_status; ?></span>
     </div><a href="./Orbot/"><h5>Tor</h5></a><p>请戴“套”翻墻</p></li>
   </ul>
  </div>
		<br>
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
  <section class="ui-container">
   <div class="index-wrap">
    <div class="footer">
    <a href="mailto:yiguihai@gmail.com" id="footer"></a>
    </div>
   </div>
  </section>
<script src="./js/zepto.min.js"></script>
<script src="./js/index.js"></script>
<script type="text/javascript">		
function net(of) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var at = xhttp.responseText;
            alert(at);
        }
    };
    xhttp.open("POST", "./Tool/Connections.php", true);
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
  function myrefresh(){ 
     window.location.reload(); 
  }
  var date = new Date();
  var year = date.getFullYear();
  document.getElementById("footer").innerHTML="Copyright © 2018-"+year+" 爱翻墙的红杏 All Rights Reserved";
  //setTimeout('myrefresh()',20000); 
</script> 

</body>
</html>