<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php
session_start();
function set_token() {
  $_SESSION['token'] = md5(microtime(true));
}
function valid_token() {
  $return = $_REQUEST['token'] === $_SESSION['token'] ? true : false;
  set_token();
  return $return;
}
if (!isset($_SESSION['token']) || $_SESSION['token'] == '') {
  set_token();
}
if (isset($_GET['token'])) {
  if (!valid_token()) {
    die('请勿重复提交表单!');
  }
}
session_write_close();
require 'user.php';
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  $user_ip = $_SERVER['REMOTE_ADDR'];
  $user_mac = get_mac($user_ip);
  $token = $_GET['token'];
}
if (empty($user_ip) or empty($user_mac)) {
  die('获取用户信息失败！');
}

if ($user_ip and $user_mac and $token) {
    foreach ($data as $key => $value) {
        foreach ($value as $user => $info) {
            $macaddress = $info['mac_address'];
            $status = $info['status'];
            if ($macaddress == $user_mac) { 
              die('你已经登录过了，如果无法上网请联系网络管理员！');
              $fhts='exist';
            }
            if ($macaddress == $user_mac && $status == 'Block') { 
              die('你被网络管理员禁止登录！');
              $fhts='block';
            }
        }
    }
}

if (empty($fhts) and $user_ip and $user_mac and $token) {
//file_put_contents('user.json', json_encode(user_del($data,'','',$user_mac)), LOCK_EX);
file_put_contents('user.json', json_encode(user_add($data, $user_count, $date, $user_ip, $user_mac)), LOCK_EX);
/*
$command_file=sys_get_temp_dir()."/user.sh";
$command_run="iptables -t nat -D user_portal -p tcp -s $user_ip -m mac --mac-source $user_mac -j RETURN".PHP_EOL."iptables -t nat -I user_portal -p tcp -m tcp -s $user_ip -m mac --mac-source $user_mac -j RETURN";
file_put_contents($command_file, $command_run, LOCK_EX);
if (!is_executable($command_file) and file_exists($command_file)) {
    chmod($command_file, 0700);
}
shell_exec("su -c $command_file");
*/
$command_run="iptables -t nat -I user_portal -p tcp -s $user_ip -m mac --mac-source $user_mac -j RETURN";
shell_exec("su -c $command_run");
unset($_SESSION['from']);
header("Location: http://www.google.com");
}
?>

<title>上网欢迎页（事例）</title>
<style>
    /*此DEMO适合0基础用户使用，只需替换图片及文字即可*/
html,body{
    background-color:#ededed;/*定义背景颜色*/
    height:100%;padding:0;margin:0;
    font-family: "Microsoft Yahei",Helvetica,Arial,sans-serif;
}
p{padding: 0;margin: 0;}
a {text-decoration: none;}
img{display:block;border:none;width: 100%;}

/* logo */
.logo{
    width:150px;/*定义LOGO宽度*/
    padding:10px;
}
/* container */
.container{max-width:720px;margin: auto;}
.main img {width: 100%;}
.btn-bar{
    margin-top:40px;/*设置按钮与主图间隔距离*/
    text-align: center;/*设置按钮排版对齐方式 可选参数 left、center、right*/
    padding:0 10px;
}
/*定义按钮样式*/
.btn{
    background-color: #2eb3e8;/*定义按钮颜色*/
    color:#ffffff;/*定义按钮文字颜色*/
	font-size: 18px;/*定义按钮文字大小*/
	padding:10px 20px;
    border:none;
    display: block;
    border-radius: 3px;
    -webkit-border-radius: 3px;
    -moz-border-radius: 3px;
}
    .text-area {
        font-size: 18px;
        color: #666;
        line-height: 1.5em;
        padding: 10px;
        text-align: center;
    }
    .title {
        font-size: 24px;
        padding-bottom:25px;
        color: #333;
        text-align: center;
    }
</style>
</head>
<body>

<div class="container">
    <div class="logo">
        <img src="logo.png" alt="HiWiFi"><!--src="LOGO文件"-->
    </div>
    <div class="main">
        <img src="main.png" alt="HiWiFi"/><!--主图片宽度建议不超过720px-->
    </div>
    <div class="text-area">
        <p class="title">欢迎光临XXX店</p>
        <p>营业时间：9:00--21:00</p>
        <p>电话：<a href="tel:+86123456789">+86 123456789</a></p>
    </div>
    <div class="btn-bar">
		<a href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])."?token=".$_SESSION["token"];?>" class="btn" >
			点击上网
		</a>
    </div>
</div>

</body>
</html>
