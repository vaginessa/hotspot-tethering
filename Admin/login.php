<?php
if ($_POST['logout']=='logout') {
setcookie("user_name", "", time()-2592000,'/');
setcookie("pass_word", "", time()-2592000,'/');
die;
}
if ($_POST['username'] && $_POST['password']) {
setcookie("user_name", $_POST['username'], time()+2592000,'/');
setcookie("pass_word", $_POST['password'], time()+2592000,'/');
die;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Login Form</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="css/style.css" rel='stylesheet' type='text/css' />
</head>
<body>
	<div class="main">
		<div class="login">
			<h1>管理系统</h1>
			<div class="inset">
				<!--start-main-->
				<!--<form action="" method="post">-->
			         <div>
			         	<h2>管理登录</h2>
						<span><label>用户名</label></span>
						<span><input type="text" class="textbox" id="username" name="username"></span>
					 </div>
					 <div>
						<span><label>密码</label></span>
					    <span><input type="password" class="password" id="password" name="password"></span>
					 </div>
					<div class="sign">
                        <input type="button" value="登录" onclick="login()" class="submit" />
					</div>
<!--					</form> -->
				</div>
			</div>
		<!--//end-main-->
		</div>

<div class="copy-right">
	<a href="mailto:yiguihai@gmail.com" id="footer" style="color: #eee; font-size: 16px;"></a>
</div>
<!--
<div style="text-align:center;">
<p>更多模板：<a href="http://www.mycodes.net/" target="_blank">源码之家</a></p>
</div>
-->
<script type="text/javascript">
function login() {
    var a = document.getElementById("username").value;
    var b = document.getElementById("password").value;
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            window.location.href="./";
        }
    };
    xhttp.open("POST", "", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("username="+a+"&password="+b+"&number="+Math.random());
}
</script>
<script type="text/javascript">
  var date = new Date();
  var year = date.getFullYear();
  document.getElementById("footer").innerHTML="Copyright © 2018-"+year+" 爱翻墙的红杏 All Rights Reserved";
</script> 
</body>
</html>