<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, initial-scale=1.0, user-scalable=no" />
    <title>KoolProxy</title>    
    <link rel="stylesheet" href="../css/frozenui.css">
    <link rel="stylesheet" href="../css/style.css">    
</head>
<body ontouchstart onload="checkCookie()">

<?php
require '../Admin/main.class.php';
$pkill = toolbox_check() [1] . ' pkill';
if (!file_exists('koolproxy')) {
    die('程序主文件不见了');
}
if (!file_exists('rules/')) {
    die('程序配置文件夹不见了');
}
$binary_file = sys_get_temp_dir() . '/koolproxy';
if (!is_executable($binary_file) && file_exists('koolproxy')) {
    copy('koolproxy', $binary_file);
    chmod($binary_file, 0700);
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $koolproxy = $_POST['koolproxy'];
    $guolv = $_POST['guolv'];
    $usergz = $_POST['usergz'];
    if ($guolv == 'all') {
        $run_ipt = '80,443,8080';
    } elseif ($guolv == 'http') {
        $run_ipt = '80,8080';
    } elseif ($guolv == 'video') {
        $run_ipt = '80,8080';
    }
}
if (isset($usergz)) {
    file_put_contents('rules/user.txt', $usergz, LOCK_EX);
}
function zx_input($yxfile, $yx) {
    if (file_exists($yxfile) or is_executable($yxfile)) {
        unlink($yxfile);
    }
    file_put_contents($yxfile, $yx, LOCK_EX);
    chmod($yxfile, 0700);
    shell_exec("su -c $yxfile");
}
$yxfile = sys_get_temp_dir() . '/koolproxy.sh';
$jsyx = "$pkill koolproxy" . PHP_EOL . 'iptables -t nat -F koolproxy_forward';
if (binary_status("koolproxy")) {
    $status = true;
} else {
    $status = false;
}
if (isset($guolv)) {
    if ($koolproxy == 'on') {
        if ($guolv == 'video') {
            $e = '-e';
        }
        $yx = $jsyx . PHP_EOL . $binary_file . ' -p 1029 -b ' . __DIR__ . " $e -d" . PHP_EOL . "iptables -t nat -A koolproxy_forward -p tcp -m multiport --dports $run_ipt -j REDIRECT --to-ports 1029";
        zx_input($yxfile, $yx);
    }
    if (empty($koolproxy) and $guolv) {
        zx_input($yxfile, $jsyx);
    }
    sleep(1);
    header('Location: ../Admin/');
}
?>


<section class="ui-container">
        
 <section id="tab">
    <div class="demo-item">
        <p class="demo-desc"><?php echo '版本 '.shell_exec(sys_get_temp_dir().'/koolproxy -v'); ?></p>
        <div class="demo-block">
            <div class="ui-tab">
                <ul style="box-shadow: 7px 7px 3px #888888;" class="ui-tab-nav ui-border-b">
                  <li class="current"><span>首页设置</span></li>
                  <li><span>自定规则</span></li>
                  <li><span>帮助关于</span></li>
                </ul>
                <ul class="ui-tab-content" style="width:300%">
                    <li>
                    <div class="ui-form ui-border-t"><form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST" id="form"><div class="ui-form-item ui-form-item-switch ui-border-b"><p><b>服务开关:</b></p><label class="ui-switch"><input type="checkbox" id="koolproxy" name="koolproxy"></label></div><div class="ui-form-item ui-border-b"><label><b>选择过滤模式:</b></label><div class="ui-select-group"><div class="ui-select"><select id="guolv" name="guolv"><option value="all">全局模式</option><option value="http" selected="">http模式</option><option value="video">视频模式</option></select></div></div></div><div class="ui-btn-wrap"><button onclick="tijiaoCookie()" class="ui-btn-lg ui-btn-primary">提交</button></div></form></div><br><ul class="ui-row"><li class="ui-col ui-col-25"><a onclick="update()"><p class="ui-txt-highlight">规则更新</p></a></li><li class="ui-col ui-col-25"><a href="https://github.com/koolproxy/koolproxy_rules"><p class="ui-txt-highlight">规则反馈</p></a></li><li class="ui-col ui-col-25"><a href="http://110.110.110.110"><p class="ui-txt-highlight">证书下载</p></a></li><li class="ui-col ui-col-25"><a href="http://koolshare.cn/thread-80430-1-1.html" target="_blank"><p class="ui-txt-highlight">过滤教程</p></a></li></ul>
                   </li>
                    <li>
                    <textarea rows="25" style="width:99%" cols="40" name="usergz" form="form_user" placeholder="自定义广告规则"><?php echo file_get_contents('rules/user.txt'); ?></textarea><form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST" id="form_user"><button class="ui-btn-lg ui-btn-primary">保存规则</button><button type="reset" class="ui-btn-lg">重置输入</button></form>
                    </li>
                    <li>
                    <p>1&nbsp;&nbsp;过滤https站点需要为相应设备安装证书，并启用“全局模式" 过滤！</p><p>2&nbsp;&nbsp;在路由器下的设备，不管是电脑，还是移动设备，都可以在浏览器中输入<u><font color="#66CCFF">110.110.110.110</font></u>来下载证书。</p><br><div class="ui-label-list"><label class="ui-label"><a href="//shang.qq.com/wpa/qunwpa?idkey=d6c8af54e6563126004324b5d8c58aa972e21e04ec6f007679458921587db9b0" target="_blank">加入QQ群①</a></label><label class="ui-label"><a href="https://jq.qq.com/?_wv=1027&k=49tpIKb" target="_blank">加入QQ群②&nbsp;&nbsp</a></label><label class="ui-label"><a href="https://t.me/joinchat/AAAAAD-tO7GPvfOU131_vg">加入电报群</a></label></div><br><br><font color="#ffcc00"><a href="http://www.koolshare.cn" target="_blank"></font>koolproxy工作有问题？请来我们的<font color="#ffcc00">论坛www.koolshare.cn</font>反应问题...
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>
 <div class="ui-loading-block show" id="loading" style="display:none">
                <div class="ui-loading-cnt">
                    <i class="ui-loading-bright"></i>
                    <p>请耐心等待...</p>
                </div>
            </div>   
</section>    
<script src="../js/zepto.min.js"></script>    
<script type="text/javascript">
function setCookie(cname,cvalue,exdays){
	var d = new Date();
	d.setTime(d.getTime()+(exdays*24*60*60*1000));
	var expires = "expires="+d.toGMTString();
	document.cookie = cname+"="+cvalue+"; "+expires;
}

function getCookie(cname){
	var name = cname + "=";
	var ca = document.cookie.split(';');
	for(var i=0; i<ca.length; i++) {
		var c = ca[i].trim();
		if (c.indexOf(name)==0) { return c.substring(name.length,c.length); }
	}
	return "";
}

function checkCookie(){
	var guolv=getCookie("guolv");
	if (guolv!=""){
		$("#guolv").val(guolv);
	}
}

function tijiaoCookie() {
var guolv=$("#guolv").val();
  setCookie("guolv",guolv,30);
}

(function() {
    var record = 0;
    var origin_l;
    $('.ui-tab-nav').eq(0).find('li').on('click', function() {
        $(this).parent().find('li').removeClass('current');
        $(this).addClass('current');
        $('.ui-tab-content').eq(0).css({
            'transform': 'translate3d(-' + ($(this).index() * $('.ui-tab-content li').offset().width) + 'px,0,0)',
            'transition': 'transform 0.5s linear'
        })
    });
})(window, undefined)

$('#koolproxy').attr('checked', <?php echo $status; ?>);

function update() {
    if ($("#loading").css("display") == "none") $("#loading").show();
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            $("#loading").hide();
            alert(xhttp.responseText);
        }
    };
    xhttp.open("POST", "update.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("rand=" + Math.random());
}
</script>

</body>
</html>