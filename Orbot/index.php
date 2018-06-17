<!DOCTYPE html>
<html>
 <head> 
  <meta charset="utf-8" /> 
  <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, initial-scale=1.0, user-scalable=no" /> 
  <meta name="format-detection" content="telephone=no, email=no" /> 
  <meta name="HandheldFriendly" content="true" /> 
  <title>Tor配置</title> 
  <link rel="stylesheet" href="../css/frozenui.css" /> 
  <link rel="stylesheet" href="../css/style.css" /> 
 </head>
 <body ontouchstart="" onload="checkCookie()">

  <section id="form"> 
   <a href="<?php echo htmlspecialchars($_SERVER['HTTP_REFERER']); ?>"><h1 class="title">Tor配置</h1></a> 
   <div class="demo-item"> 
    <p class="demo-desc">torrc文件配置</p> 
    <div class="demo-block"> 
     <div class="ui-form ui-border-t"> 
      <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="GET"> 

<div class="ui-form-item ui-form-item-switch ui-border-b">服务开关<label class="ui-switch"><input type="checkbox" id="tor" name="tor"></label></div>

<?php
require '../tools/Certified.php';
require "../tools/busybox.php";
require "../tools/token.php";
require "../tools/input.php";

session_start();
if(!isset($_SESSION['token']) || $_SESSION['token']=='') {
  set_token();
}
session_write_close();
if(isset($_GET['token'])){
  if(!valid_token()){
    die("<div class='ui-tooltips ui-tooltips-warn'><div class='ui-tooltips-cnt ui-tooltips-cnt-link ui-border-b'><i></i>请勿重复提交表单!</div></div>");
  }
}

$kill=busybox_check("kill");
$ps=busybox_check("ps");

if (stripos(shell_exec("su -c $ps -A"), " tor".PHP_EOL) !== false) {
    $status = true;
    } else {
    $status = false;
}

$binary_file=sys_get_temp_dir()."/tor";

if (!is_executable($binary_file) and file_exists('tor')) {
    copy('tor', $binary_file);
    chmod($binary_file, 0777);
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $tor = test_input($_GET['tor']);
    $token = test_input($_GET['token']);
}


function zx_input($yxfile,$yx) {
  if (file_exists($yxfile) or is_executable($yxfile)) unlink($yxfile);
        file_put_contents($yxfile, $yx, LOCK_EX);
        chmod($yxfile, 0700);
        shell_exec("su -c sh $yxfile");
}

$yxfile=sys_get_temp_dir()."/tor.sh";

if (isset($token) and $tor == 'on') {
        $data = getUrlKeyValue($_SERVER["QUERY_STRING"]);
        if (file_exists('torrc')) unlink('torrc');
        foreach ($data as $key => $value) {
        $data=test_input($key)." ".test_input($value);
        if ($key != "token") file_put_contents('torrc', $data.PHP_EOL, FILE_APPEND | LOCK_EX);
        if ($key == "TransPort") $tcpport=test_input($value);
        if ($key == "DNSPort") $udpport=test_input($value);
        }
        shell_exec('export HOME='.sys_get_temp_dir().PHP_EOL.$binary_file.' -f '.dirname(__FILE__).'/torrc > /dev/null 2>&1 &');
        sleep(1);
        $yx="iptables -t nat -F out_forward".PHP_EOL."iptables -t nat -A out_forward -p tcp -j REDIRECT --to-ports $tcpport".PHP_EOL."iptables -t nat -A out_forward -p udp --dport 53 -j REDIRECT --to-ports $udpport".PHP_EOL;
        zx_input($yxfile,$yx);
        sleep(1);
        header('Location: ./');
}

if (empty($tor) and $_GET['token']) {
        if (file_exists('tor.pid')) {
        shell_exec("$kill ".file_get_contents(dirname(__FILE__).'/tor.pid'));
        $yx="iptables -t nat -F out_forward".PHP_EOL."iptables -t nat -A out_forward -p tcp -j REDIRECT --to-ports 1024".PHP_EOL."iptables -t nat -A out_forward -p udp --dport 53 -j REDIRECT --to-ports 1053".PHP_EOL;
        zx_input($yxfile,$yx);
        echo "<div class='ui-loading-wrap'><p>等待关闭完成...</p><i class='ui-loading'></i></div>";
        sleep(1);
        exit(header('Refresh:2,Url=./'));
        }
    }


if (file_exists('torrc')) { 
$tor_info = file_get_contents('torrc');
$tor_info = explode(PHP_EOL, $tor_info);
}

foreach ($tor_info as $value) {
$tor_info = explode(' ', $value);
$key = $tor_info[0];
$value = $tor_info[1];
if($key=="GeoIPFile") $value=dirname(__FILE__)."/geoip";
if($key=="PidFile") $value=dirname(__FILE__)."/tor.pid";
if($key!="" and $value!="") {
echo '<div class="ui-form-item ui-border-b"<label>'.$key.'</label><input type="text" id="'.$key.'" name="'.$key.'" value="'.$value.'" class="ui-searchbar-text ui-txt-highlight" /></div>';
}
}

?>

       
       <input type="hidden" name="token" value="<?php echo $_SESSION["token"]?>">
       <div class="ui-btn-wrap"><button class="ui-btn-lg ui-btn-primary">提交</button></div>
       
       
       
       </form> 
     </div> 
    </div> 
   </div> 
  </section>
  
  <div class="ui-btn-wrap color-black" onclick="check_tor()">
                <button class="ui-btn-highlight ui-btn-lg">
                    Tor网络检测
                </button>
  
<script src="../js/zepto.min.js"></script>
<script type="text/javascript">
$('#tor').attr('checked', <?php echo $status; ?>);
</script>
<script type="text/javascript">
function getCookie(c_name)
{
if (document.cookie.length>0)
  {
  c_start=document.cookie.indexOf(c_name + "=")
  if (c_start!=-1)
    { 
    c_start=c_start + c_name.length+1 
    c_end=document.cookie.indexOf(";",c_start)
    if (c_end==-1) c_end=document.cookie.length
    return unescape(document.cookie.substring(c_start,c_end))
    } 
  }
return ""
}

function setCookie(c_name,value,expiredays)
{
var exdate=new Date()
exdate.setDate(exdate.getDate()+expiredays)
document.cookie=c_name+ "=" +escape(value)+
((expiredays==null) ? "" : ";expires="+exdate.toGMTString())
}

function checkCookie() {
    tor_msg = getCookie('tor_msg')
    if (tor_msg == null || tor_msg == "") if (window.confirm('尽量不要修改配置文件，因为已经和其它模块配置绑定了。修改可能会造成不可预知的错误！！！')) {
        //alert("确定");
        setCookie('tor_msg', 'yes', 365)
    } else {
        //alert("取消");
        window.location.href = '../Admin'
    }
}
</script>

<script type="text/javascript">
function check_tor() {
window.open("https://check.torproject.org/?lang=zh_CN")
}
</script>
  
</body>
</html>