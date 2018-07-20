<?php 
require 'main.class.php';
$tor=sys_get_temp_dir().'/tor';
$out=binary_status(array('aria2c','ss-local','koolproxy',$tor,'frpc','verysync'));
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
    if ($val=='frpc') {
      $frpc_status='<i class="ui-subscript ui-subscript-green">运行中</i>';
    }
    if ($val=='verysync') {
      $verysync_status='<i class="ui-subscript ui-subscript-green">运行中</i>';
    }
  }
}

function download_image() { 
  $str = GET(urldecode('http://www.bing.com/HPImageArchive.aspx?format=js&idx=0&n=1'));
  if ($str) {
    $array = json_decode($str);
    $imgurl = $array->{"images"}[0]->{"url"};
    $imgurl = 'http://www.bing.com'.$imgurl;
    $format = substr(strrchr($imgurl, '.'), 1);
    $startdate = $array->{"images"}[0]->{"fullstartdate"};
    $name = $startdate.".".$format;
    $data = GET(urldecode($imgurl));
    if ($data) {
      file_put_contents('../background/'.$name, $data, LOCK_EX);
      die("{\"a\": \"下载 $name 图片成功\",\"b\": 0}");
    } else { 
      die("{\"a\": \"图片 $name 下载失败！\",\"b\": 1}");
    }
  } else { 
    die("{\"a\": \"api接口数据获取失败！$imgurl\",\"b\": 1}");
  }
}

function get_image() { 
    $file_arr=glob("../background/*");
    $default='../background/background.jpg';
    $num=count($file_arr);
    for($i = 0; $i < $num; $i++) { 
        $file=$file_arr[$i];
            $format=substr(strrchr($file, '.'), 1);
            if ($format=='jpg' or $format=='png') {
                $last=date ("Y-m-d", filemtime($file));
                $now=date ("Y-m-d", time());
                if ($num>1&&$file==$default) { 
                    unset($file);
                }
                if ($num==1&&$file==$default) { 
                    $data[]=$file;
                }
                if ($last==$now&&$file) {
                    $data[]=$file;
                } else {
                    if (file_exists($file)&&$file!=$default) { 
                        unlink($file);
                    }
                }
            }
        }
   $rand_keys = array_rand($data, 1);
   return $data[$rand_keys];
}
$receive=htmlspecialchars($_POST['receive']);
if ($receive=='change') {
  download_image();
} else { 
  Console($receive);
}
function ping($a,$b,$c) {
    $data=exec("ping -c $a -w $b -n $c", $output, $return_val);
    if ($return_val == 0) {
        return explode('/', end($output))[4];
    }
}
$test=htmlspecialchars($_POST['test']);
switch (true){
   case stristr($test,'ping'):
      $file='../Shadowsocks/config.ini';
      if (file_exists($file)) { 
        $server=parse_ini_file($file)['server'];
        $ms=ping('1','5',$server);
        if ($ms>0) {
          die("{\"a\": \"$ms\",\"b\": 0}");
        } else {
          die("{\"a\": \"ping $server 失败！\",\"b\": 1}");
        }
      } else { 
        die("{\"a\": \"配置文件 $file 不存在！\",\"b\": 1}");
      }
      break;
   case stristr($test,'foreign'):
      $code=http_code('http://www.google.com.tw',8);
      if ($code==200) { 
        die("{\"a\": \"$code\",\"b\": 0}");
      } else { 
        die("{\"a\": \"连接到Google服务器失败！返回状态码: $code\",\"b\": 1}");
      }      
      break;
   case stristr($test,'domestic'):
      $code=http_code('http://www.baidu.com',8);
      if ($code==200) { 
        die("{\"a\": \"$code\",\"b\": 0}");
      } else { 
        die("{\"a\": \"连接到百毒服务器失败！返回状态码: $code\",\"b\": 1}");
      }
      break;
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
<style type="text/css">
body {
  width: 100%;
  height: 100%;
  display: block;
  position: relative;
  background-color: black;
}
body::after {
  content: "";
  background-image: url(<?php echo get_image(); ?>);
  background-size: 100% 100%;
  background-repeat: no-repeat;
  background-position: right top;
  background-attachment: fixed;
  opacity: 0.7;
  filter: alpha(opacity=70); /* For IE8 and earlier */
  top: 0;
  left: 0;
  bottom: 0;
  right: 0;
  position: absolute;
  z-index: -1;   
}
.header { 
	height: 135px;
	display: -webkit-box;
	-webkit-box-orient: vertical;
	-webkit-box-align: center;
	-webkit-box-pack: center
	overflow:hidden;
}
.traffic { 
   background-color:#dec48f;
   text-align:center;
   opacity:0.9;
   margin: 8px;
   padding-top: 4px;
   padding-bottom 4px;
   border-radius:25px;
}
.footer {
   position: fixed;
   left: 0;
   bottom: 0;
   width: 99.5%;
   opacity: 0.9;
   background-color: #f7f7f7;
   text-align: center;
   overflow:hidden;
}

h5 { 
  color: white;
}
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
  width: 99.5%;
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
.app-menu {
  width: 99.5%;
  height: 330px;
  overflow-y: scroll;
}
</style>
<body ontouchstart="">

<div class="demo-block">
            <div class="ui-poptips ui-poptips-info" id="notification" style="top:2px;display:none" onclick="this.parentElement.style.display='none';">
                <div class="ui-poptips-cnt">
                    <i></i><notification2></notification2>
                </div>
            </div>
        </div>
        
<div class="header">
   <a href="https://github.com/yiguihai/hotspot-tethering" target="_blank"><h1 style="color:white"><?php system("getprop ro.product.model"); ?></h1></a>
    <h2 style="color:#eeeeee"><?php system("getprop gsm.network.type"); ?></h2>
</div>

<div class="app-menu">
<div class="ui-grid-icon">
          <ul>
              <li>
                <div class="ui-img-icon">
                  <span style="background-image:url(../img/icon-shadowsocks.png)" onclick='window.location.href="../Shadowsocks/"'><?php echo $ss_status; ?></span>
                </div>
                <h5 onclick='notification("一種基於Socks5代理方式的加密傳輸協定",2100)'>Shadowsocks</h5>

              </li>
              <li>
                <div class="ui-img-icon">
                  <span style="background-image:url(../img/icon-welcom.png)" onclick='window.location.href="../Welcom/"'></span>
                </div>
                <h5 onclick='notification("热点欢迎页设置",2100)'>欢迎页</h5>

              </li>
              <li>
                <div class="ui-img-icon">
                  <span style="background-image:url(../img/icon-frpc.png)" id="frpc"><?php echo $frpc_status; ?></span>
                </div>
                <h5 onclick='notification("可用于内网穿透的高性能的反向代理",2100)'>Frp</h5>

              </li>
          </ul>
        </div>
       <br />
        
<div class="ui-grid-icon">
          <ul>
              <li>
                <div class="ui-img-icon">
                  <span style="background-image:url(../img/icon-tor.png)" id='tor'><?php echo $tor_status; ?></span>
                </div>
                <h5 onclick='notification("请戴“套”翻墻",2100)'>Tor</h5>

              </li>
              <li>
                <div class="ui-img-icon">
                  <span style="background-image:url(../img/icon-koolproxy.png)" onclick='window.location.href="../KoolProxy/"'><?php echo $kool_status; ?></span>
                </div>
                <h5 onclick='notification("用于去除网页静广告和视频广告，并且支持https！",2100)'>KoolProxy</h5>

              </li>
              <li>
                <div class="ui-img-icon">
                  <span style="background-image:url(../img/icon-aria2.png)" id="aria2"><?php echo $aria2_status; ?></span>
                </div>
                <h5 onclick='notification("一个轻量级的多协议和多资源命令行下载工具",2100)'>Aria2</h5>

              </li>
          </ul>
        </div>
        <br />
<div class="ui-grid-icon">
          <ul>
              <li>
                <div class="ui-img-icon">
                  <span style="background-image:url(../img/icon-fileadmin.png)" onclick='window.open("../Fileadmin/")'></span>
                </div>
                <h5 onclick='notification("致力于提供简单、快捷的网站文件管理方案",2100)'>爱特文管</h5>

              </li>
              <li>
                <div class="ui-img-icon">
                  <span style="background-image:url(../img/icon-verysync.jpg" id="verysync"><?php echo $verysync_status; ?></span>
                </div>
                <h5 onclick='notification("一款高效的数据传输工具",2100)'>微力同步</h5>

              </li>
              <li>
                <div class="ui-img-icon">
                  <span style="background-image:url(../img/icon-image.png" onclick='Refresh("","receive=change","change")'></span>
                </div>
                <h5 onclick='notification("更换首页背景，来自Bing每日图片。(超过24小时会自动删除)",2100)'>更换背景</h5>

              </li>
          </ul>
        </div>
        <br />
<div class="ui-grid-icon">
          <ul>
              <li>
                <div class="ui-img-icon">
                  <span style="background-image:url(../img/icon-switch.png)" id="switch"></span>
                </div>
                <h5 onclick='notification("手机数据连接关闭和开启等",2100)'>开关控制</h5>
                <p></p>

              </li>
              <li>
                <div class="ui-img-icon">
                  <span style="background-image:url(../img/icon-mobile.png)" onclick='window.open("./mobile.php")'></span>
                </div>
                <h5 onclick='notification("系统、电量、内存等详细信息",2100)'>关于手机</h5>

              </li>
              <li>
                <div class="ui-img-icon" onclick='if (confirm("要退出登录吗？")==true) Refresh("login.php","logout=logout","logout");'>
                  <span style="background-image:url(../img/icon-logout.png)"></span>
                </div>
                <h5 class="admin"><?php echo U; ?></h5>

              </li>
          </ul>
        </div>
<div class="ui-grid-icon">
          <ul>
              <li>
                <div class="ui-img-icon">
                  <span style="background-image:url(../img/icon-twitter.png" onclick='window.open("https://mobile.twitter.com/QXGFW")'></span>
                </div>
                <h5 onclick='notification("访问我的Twitter",2100)'>Twitter</h5>

              </li>
              <li>
                <div class="ui-img-icon">
                  <span style="background-image:url(../img/icon-feedback.png" onclick='alert("一时冲动的想法，历时2个多月终于成型。\n失去了太多，也得到了许多\n期间修修补补，各种资料查找、功能修复完善。\n当看到进度条动起来的时候就像我赋予了它生命一样，那种成就感满满地！\n")'></span>
                </div>
                <h5>关于</h5>

              </li>
          </ul>
        </div>
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

        <br />
<span style="color: white">CPU使用率: <b id="cpu1"></b></span>
<div class="progress round-conner">
    <div class="curRate round-conner" id="cpu2"></div>
</div>
<span style="color: white">剩余内存(RAM):  <b id="ram1">0</b> / <b id="ram2">0</b>&nbspMB</span>
<div class="progress round-conner">
    <div class="curRate round-conner" id="ram3"></div>
</div>
<span style="color: white">剩余存储:  <b id="storage1"></b></span>
<div class="progress round-conner">
    <div class="curRate round-conner" id="storage2"></div>
</div>
<div class="traffic" onclick='Refresh("server.php","Refresh=refresh","refresh")'>
 <span id="traffic" style="color:white;">网卡: <b style="color:#8558ef;" id="traffic1"></b> 内网: <b style="color:#8558ef;" id="traffic2"></b> 连接数: <b style="color:#8558ef;" id="traffic3"></b><br><span id="download_all">下载</span>: <b style="font-size: 20px;color:#ee82ee;" id="traffic4"></b>&nbsp<unit style="font-size: 18px;color: black;" id="unit1"></unit>&nbsp数据包数量: <b id="traffic5"></b><br><span id="upload_all">上传</span>: <b style="font-size: 20px;color:#66ccff;" id="traffic6"></b>&nbsp<unit style="font-size: 18px;color: black;" id="unit2"></unit>&nbsp数据包数量: <b id="traffic7"></b></span>
</div>
        <br />
    <div class="footer">
      <ul class="ui-row">
       <li class="ui-col ui-col-33" onclick='Refresh("","test=ping","test")'>
       <p class="ui-txt-white" id="ping">测试延迟</p>
       </li>
       <li class="ui-col ui-col-33" onclick='Refresh("","test=foreign","test")'>
       <p class="ui-txt-white" id="foreign">国外连接</p>
       </li>
       <li class="ui-col ui-col-33" onclick='Refresh("","test=domestic","test")'>
       <p class="ui-txt-white" id="domestic">国内连接</p>
       </li>
      </ul>
    </div>

<script src="../js/zepto.min.js"></script>
<script type="text/javascript">
$('body').css('height', $(window).height()+'px'); //屏幕高
function loading(a) { 
    if (a==""||a==null) {
        var a="请稍候…";
    }
    $(".ui-loading-cnt p").text(a);
    if ($("#loading").css("display")=="none"){
        $("#loading").show();
    }
  setTimeout(function(){
      if ($("#loading").css("display")!="none"){
         $("#loading").hide();
         notification("时间超时!",3000);
      }
  },10000);
}

// https://zeit.co/blog/async-and-await
function sleep (time) {
  return new Promise((resolve) => setTimeout(resolve, time));
}

function Refresh(a,b,c) { 
var xhttp=new XMLHttpRequest();
xhttp.onreadystatechange=function() { 
  if(this.readyState==4&&this.status==200) { 
    if (c=='mobile'||c=='aria2'||c=='change'||c=='test') {
      $("#loading").hide();
    }
    if (c=='refresh') { 
      notification("已帮你刷新了流量信息!",2100);
    } else if (c=='logout') { 
      window.location.href="";
    } else if (c=='change') { 
      obj = JSON.parse(xhttp.responseText);
      if (obj.b==0) { 
        notification(obj.a,2100,obj.b);
        // 用法
        sleep(3000).then(() => { 
        // 这里写sleep之后需要去做的事情
          window.location.href="";
        })
      } else { 
        notification(obj.a,4000,obj.b);
      }
    } else if (c=='test') { 
      obj = JSON.parse(xhttp.responseText);
      if(b=='test=ping'&&obj.b==0) {
        $('#ping').html("延时: <i class=\"ui-txt-highlight\">"+obj.a+"</i> 毫秒");
      } else if(b=='test=foreign'&&obj.b==0) {
        $('#foreign').html("国外连接: <i class=\"ui-txt-highlight\">正常</i>");
      } else if(b=='test=domestic'&&obj.b==0) {
        $('#domestic').html("国内连接: <i class=\"ui-txt-highlight\">正常</i>");
      }
      notification(obj.a,2500,obj.b);
    } else {
      obj = JSON.parse(xhttp.responseText);
      notification(obj.a,2100,obj.b);
    }
  }
};
xhttp.open("POST",a,true);
xhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
xhttp.send(b+"&number="+Math.random());
if(c=='aria2'&&b=='receive=update'){ 
   loading('tracker更新中…');
}
if(c=='change'){ 
   loading('正在下载更换背景图…');
}
if(c=='test'){ 
   loading('测试中…');
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
  $(".ui-actionsheet").addClass("show");
}
$("#actionsheet").click(function(){
  $(".ui-actionsheet").removeClass("show");
});

var content="<button onclick='Refresh(\"\",\"receive=on\",\"mobile\")'>打开数据网络</button>\n<button onclick='Refresh(\"\",\"receive=off\",\"mobile\")'>关闭数据连接</button>\n<button onclick='Toast(\"重启\",\"确认重启手机？\",\"restart\")' class=\"ui-actionsheet-del\">重启</button>\n<button onclick='Toast(\"关机\",\"确认关机？\",\"shutdown\")' class=\"ui-actionsheet-del\">关机</button>\n";
$("#switch").click(function(){
  select(content);
});
var content1="\n<h4>可自行添加更多的webui前端界面</h4>\n<button onclick='Refresh(\"../Aria2/\",\"receive=start\",\"aria2\")'>启动aria2</button>\n<button onclick='Refresh(\"../Aria2/\",\"receive=stop\",\"aria2\")' class=\"ui-actionsheet-del\">关闭aria2</button>\n<button onclick='Refresh(\"../Aria2/\",\"receive=update\",\"aria2\")'>更新tracker</button>\n<button onclick='window.open(\"../Aria2/AriaNg/\")'>Aria2Ng</button>\n<button onclick='window.open(\"../Aria2/webui-aria2/\")'>webui-aria2</button>\n";
$("#aria2").click(function(){
  select(content1);
});

var content2="\n<button onclick='Refresh(\"../Orbot/\",\"receive=start\",\"tor\")'>启动tor</button>\n<button onclick='Refresh(\"../Orbot/\",\"receive=stop\",\"tor\")' class=\"ui-actionsheet-del\">关闭tor</button>\n<hr>\n<button onclick='window.open(\"https://check.torproject.org/?lang=zh_CN\")'>网络检测</button>\n";
$("#tor").click(function(){
  select(content2);
});

var content3="\n<button onclick='Refresh(\"../Frpc/\",\"receive=start\",\"frpc\")'>启动frp</button>\n<button onclick='Refresh(\"../Frpc/\",\"receive=stop\",\"frpc\")' class=\"ui-actionsheet-del\">关闭frp</button>\n<hr>\n<button onclick='window.open(\"../Frpc/\")'>编辑/查看配置</button>\n";
$("#frpc").click(function(){
  select(content3);
});

var content4="\n<button onclick='Refresh(\"../Verysync/\",\"receive=start\",\"verysync\")'>启动</button>\n<button onclick='Refresh(\"../Verysync/\",\"receive=stop\",\"verysync\")' class=\"ui-actionsheet-del\">关闭</button>\n<hr>\n<button onclick='window.open(\"<?php echo "http://".$_SERVER['SERVER_ADDR'].":8886" ?>\")'>进入主界面</button>\n";
$("#verysync").click(function(){
  select(content4);
});

function Toast(a,b,c) { 
  $("#dialog .ui-dialog-bd h3").text(a);
  $("#dialog .ui-dialog-bd p").text(b);
  $("#dialog .ui-dialog-ft .btn-recommand").attr("onclick",'Refresh(\"\",\"receive='+c+'\",\"mobile\")');
  $("#dialog").addClass("show");  
}
$("#dialog .ui-dialog-ft .btn-recommand").click(function(){
  $("#dialog").removeClass("show");
});
function notification(text,timeout,ret) { 
    switch (ret) {
    case 0:
        ret = "ui-poptips ui-poptips-success";
        break;
    case 1:
        ret = "ui-poptips ui-poptips-warn";
        break;
    default:
        ret = "ui-poptips ui-poptips-info";
    }
    $("#notification").attr('class', ret);
    $("notification2").text(text);
    if ($("#notification").css("display") == "none"){ 
        $("#notification").show();
    }
    if (timeout > 0){ 
        setTimeout(function(){
            $("#notification").hide('blind', {}, 500);
        },timeout);
    }
}
</script>

<script type="text/javascript">
if(typeof(EventSource) !== "undefined") {
    var source = new EventSource("server.php");
    source.addEventListener("traffic", function (traffic) {
        obj = JSON.parse(traffic.data);
        document.getElementById("traffic1").innerHTML = obj.interface_name;
        document.getElementById("traffic2").innerHTML = obj.local_address;
        document.getElementById("traffic3").innerHTML = obj.tcp_conntrack;
        if(obj.download_speed != "") { 
            var down=obj.download_speed;
            var down_unit=obj.download_speed_unit+"/s";
            var download_all="下载中";
        } else { 
            var down=obj.download;
            var down_unit=obj.download_unit;
            var download_all="下载数据总量";
        }
        if(down != "" && down_unit !="") {
            document.getElementById("traffic4").innerHTML = down;
            document.getElementById("unit1").innerHTML = down_unit;
            document.getElementById("download_all").innerHTML = download_all;
        }
        document.getElementById("traffic5").innerHTML = obj.Receive_packets;
        if(obj.upload_speed != "") { 
            var up=obj.upload_speed;
            var up_unit=obj.upload_speed_unit+"/s";
            var upload_all="上传中";
        } else { 
            var up=obj.upload;
            var up_unit=obj.upload_unit;
            var upload_all="上传数据总量";
        }
        if(up != "" && up_unit !="") {
        document.getElementById("traffic6").innerHTML = up;
        document.getElementById("unit2").innerHTML = up_unit;
        document.getElementById("upload_all").innerHTML = upload_all;
        }
        document.getElementById("traffic7").innerHTML = obj.Transmit_packets;
   });
    source.addEventListener("memory", function (ram) {
        obj = JSON.parse(ram.data);
        document.getElementById("ram1").innerHTML = obj.ram_free;
        document.getElementById("ram2").innerHTML = obj.mem_total;
        document.getElementById("ram3").style.width = obj.ram_rate+"%";
        if (obj.ram_rate>75)  {
            color="blue";
        }
        else if (obj.ram_rate>50)  {
            color="green";
        }
        else if (obj.ram_rate>25)  {
            color="yellow";
        }
        else if (obj.ram_rate>0)  {
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