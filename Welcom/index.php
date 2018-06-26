<?php
session_start();
if (isset($_COOKIE["user_name"]) && isset($_COOKIE["pass_word"]) && $_SESSION['from']!='admin') { 
header("Location: ../Admin/");
die('管理员首次进入没有到过管理页面');
}
if (isset($_COOKIE["user_name"]) && isset($_COOKIE["pass_word"]) && $_SESSION['from']=='admin') { 
require '../tools/Certified.php';
} else {
header("Location: ./login.php");
die('你无权访问本页面');
}
session_write_close();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, initial-scale=1.0, user-scalable=no" />
    <title>欢迎页设置</title>
    <link rel="stylesheet" href="../css/frozenui.css">
    <link rel="stylesheet" href="../css/style.css">    
</head>

<body ontouchstart>

<section class="ui-container"><section id="actionsheet"><a href="<?php echo htmlspecialchars($_SERVER['HTTP_REFERER']); ?>"><h1 class="title">欢迎页设置</h1></a><div class="demo-item">		<p class="demo-desc">菜单</p>		<div class="demo-block">			<div class="ui-actionsheet" id="actionsheet1">				<div class="ui-actionsheet-cnt am-actionsheet-down">					<h4>热点欢迎页iptables规则链设置</h4>					<button onclick="iptables('kq')">开启热点欢迎页</button>					<button onclick="iptables('cz')">写入用户表规则</button>     <button class="ui-actionsheet-del" onclick="iptables('gb')">关闭热点欢迎页</button>					<button onclick="help_about();">帮助关于</button>     <div class="ui-actionsheet-split-line"></div>					<button id="cancel">取消</button>				</div>			</div>		</div>	</div></section><div class="ui-btn-wrap" id="btn1"><button class="ui-btn-lg">设置选择菜单</button></div></section>
<?php
    $user_file="user.json";
    $data=json_decode(file_get_contents($user_file), true);
?>
<section class="ui-container"><section id="table"><div class="demo-item"><p class="demo-desc">用户表: <?php echo "共 <b style=\"color:#ee82ee\">".count($data)."</b> 位用户" ?></p><div class="demo-block"><table class="ui-table ui-border"><thead><tr><th>用户名</th><th>MAC地址</th><th>状态</th></tr></thead><tbody>
<?php
    foreach ($data as $key => $value) {
        foreach ($value as $user => $info) {
            $ipaddress = $info['ip_address'];
            $macaddress = $info['mac_address'];
            $status = $info['status'];
            $uptime = $info['up_time'];
echo "<tr><td><a href=\"javascript:toast('登录IP: $ipaddress 上线时间: $uptime')\">$user</a></td><td>$macaddress<br><span onclick=\"activation('$macaddress')\" style=\"color:green\">激活</span>&nbsp&nbsp&nbsp<a href=\"javascript:block('$macaddress')\" class=\"ui-txt-highlight\">拉黑</a>&nbsp&nbsp&nbsp<a href=\"javascript:deleted('$macaddress')\" class=\"ui-txt-warning\">删除</a></td><td><a href=\"javascript:status('$status')\" class=\"ui-txt-feeds\">$status</a></td></tr>";
        }
    }
?>
</tbody></table></div></div></section></section>

    
<script src="../js/zepto.min.js"></script>    
<script type="text/javascript">
(function (doc, win) {
     $("#btn1").click(function(){
    		$('.ui-actionsheet').addClass('show');
    	});
    	$("#cancel").click(function(){
    		$(".ui-actionsheet").removeClass("show");
    	});
})(document, window);
function iptables(command) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var at = xhttp.responseText;
            //alert(at);
            $(".ui-actionsheet").removeClass("show");
        }
    };
    xhttp.open("POST", "portal.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  if (confirm("确认执行此操作？")==true) { 
    xhttp.send("command="+command+"&number="+Math.random());
  }
}
</script>
<script type="text/javascript">
function toast(tos) { 
alert(tos); 
}
function help_about() { 
alert("iptables流量定向\nhttp端口: 8080 https端口: 4433\n请设置好ksweb的监听端口"); 
}
function status(status) { 
if(status=='OK') alert("状态: 正常"); 
if(status=='Block') alert("状态: 被墙");
}
function activation(user) { 
if (confirm("激活MAC地址: "+user)==true)
  {
  command("activation",user);
  }
}
function block(user) { 
if (confirm("拉黑MAC地址: "+user)==true)
  {
  command("block",user);
  }
}
function deleted(user) { 
if (confirm("删除MAC地址: "+user)==true)
  {
  command("deleted",user);
  }
}
</script> 
<script type="text/javascript">
function command(o,f) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            window.location.href="";
        }
    };
    xhttp.open("POST", "user.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("yhdz="+o+"&yhmac="+f+"&number="+Math.random());
}
</script>

</body>
</html>
