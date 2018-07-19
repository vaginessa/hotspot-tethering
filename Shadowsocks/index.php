<?php
require '../Admin/main.class.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $acl = $_POST['acl'];
  $hosts = $_POST['hosts'];    
}
if (isset($acl)) {
  file_put_contents('custom.acl', $acl, LOCK_EX);
} 
if (isset($hosts)) {
  file_put_contents('hosts', $hosts, LOCK_EX);
}
//检查ss进程是否存在
if (binary_status('ss-local')) {
  $status = true;
}

//读取ini配置文件
if (file_exists('config.ini')) { 
  $my_ini = parse_ini_file('config.ini');
}
?>

<!DOCTYPE html>
<html>
 <head> 
  <meta charset="utf-8" /> 
  <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, initial-scale=1.0, user-scalable=no" /> 
  <meta name="format-detection" content="telephone=no, email=no" /> 
  <meta name="HandheldFriendly" content="true" /> 
  <title>Shadowsocks</title> 
  <link rel="stylesheet" href="../css/frozenui.css" /> 
  <link rel="stylesheet" href="../css/style.css" /> 
 </head>
<style type="text/css">
 li {
    height:100%;
    padding-right: 1px;
    overflow-y: scroll;
 }
</style>
 <body ontouchstart="">

<div class="ui-tab">
                <ul style="box-shadow: 7px 7px 3px #888888;" class="ui-tab-nav ui-border-b">
                  <li class="current"><span>Shadowsocks</span></li>
                  <li><span>自定义规则</span></li>
                  <li><span>hosts编辑</span></li>
                  <li><span id="shared">二维码分享</span></li>
                </ul>
                <ul class="ui-tab-content" style="width:400%">
<!-- Shadowsocks配置开始 -->                
                    <li>
     <div class="ui-form ui-border-t"> 
      <form action="shadowsocks.php" method="GET" id="usrform"> 
      
       <div class="ui-form-item ui-form-item-switch ui-border-b"> 
        <p><b>服务开关</b></p> 
        <p class="ui-txt-warning ui-reddot" id="ts">&nbsp;&nbsp;服务没有开启!</p>
        <label class="ui-switch"><input type="checkbox" id="shadowsocks" name="shadowsocks" /></label> 
       </div> 
       
       <div class="ui-form-item ui-border-b"> 
        <label> <b>配置名称:</b> </label> 
        <input type="text" placeholder="" id="name" name="name" class="ui-searchbar-text ui-txt-highlight" /> 
       </div> 
       
       <div class="ui-form-item ui-border-b"> 
        <label> <b>服务器:</b> </label> 
        <input type="text" id="server" name="server" style="display:none" class="ui-searchbar-text ui-txt-highlight" /> 
        <p id="server_toast">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp点击查看和编辑地址(支持ss://和域名解析)</p>
       </div> 
       
       <div class="ui-form-item ui-border-b"> 
        <label> <b>远程端口:</b> </label> 
        <input type="text" placeholder="" id="server_port" name="server_port" class="ui-searchbar-text ui-txt-highlight" /> 
       </div> 
       
       <div class="ui-form-item ui-border-b"> 
        <label> <b>密码:</b> </label> 
        <input type="password" placeholder="" id="password" name="password" class="ui-searchbar-text ui-txt-highlight" /> 
       </div> 
       
       <div class="ui-form-item ui-border-b"> 
        <label><b>加密方式</b></label> 
        <div class="ui-select-group"> 
         <div class="ui-select"> 
          <select name="method" id="method" class="ui-txt-feeds"> 
          <option value="rc4-md5">RC4-MD5</option> 
          <option value="aes-128-cfb">AES-128-CFB</option> 
          <option value="aes-192-cfb">AES-192-CFB</option> 
          <option value="aes-256-cfb" selected="">AES-256-CFB</option> 
          <option value="aes-128-ctr">AES-128-CTR</option> 
          <option value="aes-192-ctr">AES-192-CTR</option> 
          <option value="aes-256-ctr">AES-256-CTR</option> 
          <option value="bf-cfb">BF-CFB</option> 
          <option value="camellia-128-cfb">CAMELLIA-128-CFB</option> 
          <option value="camellia-192-cfb">CAMELLIA-192-CFB</option> 
          <option value="camellia-256-cfb">CAMELLIA-256-CFB</option> 
          <option value="salsa20">SALSA20</option> 
          <option value="chacha20">CHACHA20</option> 
          <option value="chacha20-ietf">CHACHA20-IETF</option> 
          <option value="aes-128-gcm">AES-128-GCM</option> 
          <option value="aes-192-gcm">AES-192-GCM</option> 
          <option value="aes-256-gcm">AES-256-GCM</option> 
          <option value="chacha20-ietf-poly1305">CHACHA20-IETF-POLY1305</option> 
          <option value="xchacha20-ietf-poly1305">XCHACHA20-IETF-POLY1305</option> 
          </select> 
         </div> 
        </div> 
       </div> 
       
       <div class="demo-item"> 
        <p class="demo-desc">功能设置</p> 
        <div class="demo-block"> 
         <div class="ui-form ui-border-t"> 
         
          <div class="ui-form-item ui-border-b"> 
           <label><b>路由</b></label> 
           <div class="ui-select-group"> 
            <div class="ui-select"> 
             <select name="route" id="route" class="ui-txt-feeds" > 
             <option value="all" selected="">全局</option> 
             <option value="bypass-lan.acl">绕过局域网</option> 
             <option value="bypass-china.acl">绕过中国大陆地址</option> 
             <option value="bypass-lan-china.acl">绕过局域网及中国大陆地址</option> 
             <option value="gfwlist.acl">GFW列表</option> 
             <option value="china-list.acl">仅代理中国大陆地址</option> 
             <option value="custom.acl">自定义规则</option> 
             </select> 
            </div> 
           </div> 
          </div> 
          
          <udp>
          <div class="ui-form-item ui-form-item-switch ui-border-b"> 
           <p><b>UDP over TCP</b></p>
           <p class="ui-txt-muted">&nbsp;&nbsp;&nbsp;需要配置好gost和内核支持TPROXY</p> 
           <label class="ui-switch"><input type="checkbox" id="udp" name="udp" /></label> 
          </div> 
          </udp>
          
          <div class="demo-item" id="gost" style="display:none"> 
           <p class="demo-desc">gost</p> 
           <div class="demo-block"> 
            <div class="ui-form ui-border-t"> 
            
             <div class="ui-form-item ui-border-b"> 
              <label> <b>服务器:</b> </label> 
              <input type="text" placeholder="" id="gost_server" name="gost_server" class="ui-searchbar-text ui-txt-highlight" /> 
             </div> 
             
             <div class="ui-form-item ui-border-b"> 
              <label> <b>远程端口:</b> </label> 
              <input type="text" placeholder="" id="gost_server_port" name="gost_server_port" class="ui-searchbar-text ui-txt-highlight" /> 
             </div> 
             
             <div class="ui-form-item ui-border-b"> 
              <label> <b>用户名:</b> </label> 
              <input type="text" placeholder="可选" id="gost_username" name="gost_username" class="ui-searchbar-text ui-txt-highlight" /> 
             </div> 
             
             <div class="ui-form-item ui-border-b"> 
              <label> <b>密码:</b> </label> 
              <input type="password" placeholder="可选" id="gost_password" name="gost_password" class="ui-searchbar-text ui-txt-highlight" /> 
             </div> 
             
            </div> 
           </div> 
          </div> 
          
  <!--- 插件类 -->
          <div class="demo-item"> 
           <p class="demo-desc">插件</p> 
           <div class="demo-block"> 
            <div class="ui-form ui-border-t"> 
            
             <div class="ui-form-item ui-border-b"> 
              <label><b>插件</b></label> 
              <div class="ui-select-group"> 
               <div class="ui-select"> 
                <select name="plugin" id="plugin" class="ui-txt-feeds"> 
                <option value="off" selected="">禁用</option> 
                <option value="obfs-local">Simple obfuscation</option>
                <option value="GoQuiet">GoQuiet</option>
                <option value="kcptun">kcptun</option> 
                </select> 
               </div> 
              </div> 
             </div> 
             
           <plugin style="display:none">
           
             <obfs-local>
             <div class="ui-form-item ui-border-b"> 
              <label><b>Obfuscation wrapper</b></label> 
              <div class="ui-select-group"> 
               <div class="ui-select"> 
                <select name="obfs" id="obfs" class="ui-txt-feeds" > 
                <option value="tls" >tls</option> 
                <option value="http" selected="">http</option> </select> 
               </div> 
              </div> 
             </div> 
             <div class="ui-form-item ui-border-b"> 
              <label> <b>Obfuscation hostname:</b> </label> 
              <input type="text" placeholder="wap.10010.com" id="obfs_host" name="obfs_host" class="ui-searchbar-text ui-txt-highlight" /> 
             </div> 
             </obfs-local>
             
             <GoQuiet>
             <div class="ui-form-item ui-border-b"> 
             <label> <b>remoteHost:</b> </label> 
              <input type="text" placeholder="远程服务IP，默认即可" id="remoteHost" name="remoteHost" class="ui-searchbar-text ui-txt-highlight" /> 
             </div>
             <div class="ui-form-item ui-border-b"> 
              <label> <b>remotePort:</b> </label> 
              <input type="text" placeholder="远程代理端口，默认443" id="remotePort" name="remotePort" class="ui-searchbar-text ui-txt-highlight" /> 
             </div>
             <div class="ui-form-item ui-border-b"> 
              <label> <b>ServerName:</b> </label> 
              <input type="text" placeholder="你想让GFW认为你在访问的域名" id="ServerName" name="ServerName" class="ui-searchbar-text ui-txt-highlight" /> 
             </div>
             <div class="ui-form-item ui-border-b"> 
              <label> <b>Key:</b> </label> 
              <input type="password" placeholder="密钥" id="Key" name="Key" class="ui-searchbar-text ui-txt-highlight" /> 
             </div>
             <div class="ui-form-item ui-border-b"> 
              <label> <b>TicketTimeHint:</b> </label> 
              <input type="text" placeholder="3600 (session ticket过期的时间)" id="TicketTimeHint" name="TicketTimeHint" class="ui-searchbar-text ui-txt-highlight" /> 
             </div>
             <div class="ui-form-item ui-border-b"> 
              <label><b>Browser</b></label> 
              <div class="ui-select-group"> 
               <div class="ui-select"> 
                <select name="Browser" id="Browser" class="ui-txt-feeds" > 
                <option value="firefox" >firefox</option> 
                <option value="chrome" selected="">chrome</option> </select> 
               </div> 
              </div> 
             </div> 
            </GoQuiet>
            
            <kcptun>
            <div class="ui-form-item ui-border-b"> 
             <label> <b>remoteaddr:</b> </label> 
              <input type="text" placeholder="default: vps:29900" id="kcpremoteaddr" name="kcpremoteaddr" class="ui-searchbar-text ui-txt-highlight" /> 
             </div>
             <div class="ui-form-item ui-border-b"> 
             <label> <b>key:</b> </label> 
              <input type="password" placeholder="default: it's a secrec" id="kcpkey" name="kcpkey" class="ui-searchbar-text ui-txt-highlight" /> 
             </div>
             <div class="ui-form-item ui-border-b"> 
              <label><b>crypt</b></label> 
              <div class="ui-select-group"> 
               <div class="ui-select"> 
                <select name="kcpcrypt" id="kcpcrypt" class="ui-txt-feeds" > 
                <option value="aes" selected="">aes</option> 
                <option value="aes-128">aes-128</option>
                <option value="aes-192">aes-192</option>
                <option value="salsa20">salsa20</option>
                <option value="blowfish">blowfish</option>
                <option value="twofish">twofish</option>
                <option value="cast5">cast5</option>
                <option value="3des">3des</option>
                <option value="tea">tea</option>
                <option value="xtea">xtea</option>
                <option value="xor">xor</option>
                <option value="sm4">sm4</option>
                <option value="none">none</option>
                </select> 
               </div> 
              </div> 
             </div> 
             <div class="ui-form-item ui-border-b"> 
              <label><b>mode</b></label> 
              <div class="ui-select-group"> 
               <div class="ui-select"> 
                <select name="kcpmode" id="kcpmode" class="ui-txt-feeds" > 
                <option value="fast3">fast3</option> 
                <option value="fast2">fast2</option>
                <option value="fast" selected="">fast</option>
                <option value="normal">normal</option>
                <option value="manual">manual</option>
                </select> 
               </div> 
              </div> 
             </div> 
             <div class="ui-form-item ui-border-b"> 
             <label> <b>conn:</b> </label> 
              <input type="text" placeholder="default: 1" id="kcpconn" name="kcpconn" class="ui-searchbar-text ui-txt-highlight" /> 
             </div>
             <div class="ui-form-item ui-border-b"> 
             <label> <b>autoexpire:</b> </label> 
              <input type="text" placeholder="default: 0" id="kcpautoexpire" name="kcpautoexpire" class="ui-searchbar-text ui-txt-highlight" /> 
             </div>
             <div class="ui-form-item ui-border-b"> 
             <label> <b>scavengettl:</b> </label> 
              <input type="text" placeholder="default: 600" id="kcpscavengettl" name="kcpscavengettl" class="ui-searchbar-text ui-txt-highlight" /> 
             </div>
             <div class="ui-form-item ui-border-b"> 
             <label> <b>mtu:</b> </label> 
              <input type="text" placeholder="default: 1350" id="kcpmtu" name="kcpmtu" class="ui-searchbar-text ui-txt-highlight" /> 
             </div>
             <div class="ui-form-item ui-border-b"> 
             <label> <b>sndwnd:</b> </label> 
              <input type="text" placeholder="default: 128" id="kcpsndwnd" name="kcpsndwnd" class="ui-searchbar-text ui-txt-highlight" /> 
             </div>
             <div class="ui-form-item ui-border-b"> 
             <label> <b>rcvwnd:</b> </label> 
              <input type="text" placeholder="default: 512" id="kcprcvwnd" name="kcprcvwnd" class="ui-searchbar-text ui-txt-highlight" /> 
             </div>
             <div class="ui-form-item ui-border-b"> 
             <label> <b>datashard:</b> </label> 
              <input type="text" placeholder="default: 10" id="kcpdatashard" name="kcpdatashard" class="ui-searchbar-text ui-txt-highlight" /> 
             </div>
             <div class="ui-form-item ui-border-b"> 
             <label> <b>parityshard:</b> </label> 
              <input type="text" placeholder="default: 3" id="kcpparityshard" name="kcpparityshard" class="ui-searchbar-text ui-txt-highlight" /> 
             </div>
             <div class="ui-form-item ui-border-b"> 
             <label> <b>dscp:</b> </label> 
              <input type="text" placeholder="default: 0" id="kcpdscp" name="kcpdscp" class="ui-searchbar-text ui-txt-highlight" /> 
             </div>
            </kcptun>
                          
           </plugin>
             
            </div> 
           </div> 
          </div>
         </div> 
        </div> 
       </div>   
          
  <!-- 插件类结尾 -->

        <div class="ui-btn-wrap"> 
         <button id="todo" class="ui-btn-lg ui-btn-primary"> 提交 </button> 
        </div>         
       </form> 
      </div> 
            </li>
<!-- Shadowsocks配置结束 -->     

<!-- 自定义规则编辑 -->            
            <li>
            <textarea rows="35" style="width:99%" cols="40" name="acl" form="acl" placeholder="自定义acl规则"><?php echo file_get_contents('custom.acl'); ?></textarea><form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST" id="acl"><button class="ui-btn-lg ui-btn-primary">保存</button><button type="reset" class="ui-btn-lg">重置输入</button></form>
            </li>
<!-- 自定义规则结束 -->                        

<!-- 自定义hosts编辑 -->         
            <li>
            <textarea rows="35" style="width:99%" cols="40" name="hosts" form="hosts" placeholder="overture的hosts文件"><?php echo file_get_contents('hosts'); ?></textarea><form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST" id="hosts"><button class="ui-btn-lg ui-btn-primary">保存</button><button type="reset" class="ui-btn-lg">重置输入</button></form>
            </li>
<!-- 自定义hosts结束 -->                     
            <li>
            <div id="qrcode" style="margin-top:15px;margin-bottom:25px"></div>
            <div style="width:95%;text-align:left;word-wrap:break-word;"><a id="copylink" href=""></a></div> 
            <br />
            <button class="ui-btn-lg" id="clip">
                    复制到剪辑板
                </button>
            </li>
            </ul>
           </div>
            
            
  <script src="../js/zepto.min.js"></script>
  <script type="text/javascript" src="../js/qrcode.min.js">/*引入二维码库 https://github.com/davidshimjs/qrcodejs */ </script>
  <script src="../js/clipboard.min.js">/*引入剪辑板库 https://github.com/zenorocha/clipboard.js*/</script>
  
  <script type="text/javascript">		
$('.ui-tab-content').css('height', $(window).height()+'px'); //屏幕高
function setplugin(){
  if ($("#plugin").val() == "off") { 
    $("plugin").hide();
  } else {
    $("plugin").show();
  }
  if ($("#plugin").val() == "obfs-local") { 
    $("obfs-local").show();
  } else {
    $("obfs-local").hide();
  }
  if ($("#plugin").val() == "GoQuiet") { 
    $("GoQuiet").show();
  } else {
    $("GoQuiet").hide();
  }
  if ($("#plugin").val() == "kcptun") { 
    $("kcptun").show();
  } else {
    $("kcptun").hide();
  }
}
  </script>		
  
  <script type="text/javascript">		
  //if ($('#udp').prop('checked')) $("#gost").show();
  $("#udp").change(function(){
  if ($("#gost").css("display")=="none"){
    $("#gost").show();
  } else {
    $("#gost").hide();
  }
  });
  $("#plugin").change(function(){
    setplugin();
  });
  </script>
  
  <script type="text/javascript">
  $("#password").focus(function(){
    $(this).attr('type','text');
  });
  $("#password").blur(function(){
    $(this).attr('type','password');
  });
  $("#gost_password").focus(function(){
    $(this).attr('type','text');
  });
  $("#gost_password").blur(function(){
    $(this).attr('type','password');
  });
  $("#Key").focus(function(){
    $(this).attr('type','text');
  });
  $("#Key").blur(function(){
    $(this).attr('type','password');
  });
  $("#kcpkey").focus(function(){
    $(this).attr('type','text');
  });
  $("#kcpkey").blur(function(){
    $(this).attr('type','password');
  });
  </script>
  
  <script type="text/javascript"> 
  $("#server_toast").tap(function(){
    $(this).hide();
    $("#server").show();
  });
  $("#server").blur(function(){
    $(this).hide();
    $("#server_toast").show();
  });
  </script>
  
  <!-- 读取配置显示 -->
  
 <script type="text/javascript">		
   var jbkg = "<?php echo $status; ?>";
if (jbkg!=null && jbkg!="") { 
   $('#shadowsocks').attr('checked', jbkg); 
   var div = document.getElementById('ts'); 
   div.className = 'demo-desc'; 
   div.innerHTML="&nbsp;&nbsp;&nbsp服务已开启!";
   }
  </script>		
  <script type="text/javascript">		
  $("#name").val("<?php echo $my_ini['name']; ?>");
  $("#server").val("<?php echo $my_ini['server']; ?>");
  $("#server_port").val("<?php echo $my_ini['server_port']; ?>");
  $("#password").val("<?php echo $my_ini['password']; ?>");
if ("<?php echo $my_ini['method']; ?>" != "") $("#method").val("<?php echo $my_ini['method']; ?>");
if ("<?php echo $my_ini['route']; ?>" != "") $("#route").val("<?php echo $my_ini['route']; ?>");
  var udpkg = "<?php echo $my_ini['udp']; ?>";
if (udpkg!=null && udpkg!="") { 
  $('#udp').attr('checked', udp);
  $("#gost").show();
  }
  $("#gost_server").val("<?php echo $my_ini['gost_server']; ?>");
  $("#gost_server_port").val("<?php echo $my_ini['gost_server_port']; ?>");
  $("#gost_username").val("<?php echo $my_ini['gost_username']; ?>");
  $("#gost_password").val("<?php echo $my_ini['gost_password']; ?>");
  var plugin = "<?php echo $my_ini['plugin']; ?>";
if (plugin!=null && plugin!="") {
  $("#plugin").val(plugin);
  $("plugin").show();
  setplugin();
  } else {
  $("#plugin").val("off");
  $("plugin").hide();
  }
if ("<?php echo $my_ini['obfs']; ?>" != "") $("#obfs").val("<?php echo $my_ini['obfs']; ?>");
  $("#obfs_host").val("<?php echo $my_ini['obfs_host']; ?>");
  $("#remotePort").val("<?php echo $my_ini['remotePort']; ?>");
  $("#remoteHost").val("<?php echo $my_ini['remoteHost']; ?>");
  $("#ServerName").val("<?php echo $my_ini['ServerName']; ?>");
  $("#Key").val("<?php echo $my_ini['Key']; ?>");
  $("#TicketTimeHint").val("<?php echo $my_ini['TicketTimeHint']; ?>");
if ("<?php echo $my_ini['Browser']; ?>" != "") $("#Browser").val("<?php echo $my_ini['Browser']; ?>");
  $("#kcpremoteaddr").val("<?php echo $my_ini['kcpremoteaddr']; ?>");
  $("#kcpkey").val("<?php echo $my_ini['kcpkey']; ?>");
  var crypt="<?php echo $my_ini['kcpcrypt']; ?>";
if (crypt != null && crypt != "") { 
  $("#kcpcrypt").val("<?php echo $my_ini['kcpcrypt']; ?>");
  } else {
  $("#kcpcrypt").val('none');
  }
if ("<?php echo $my_ini['kcpmode']; ?>" != "") $("#kcpmode").val("<?php echo $my_ini['kcpmode']; ?>");
  $("#kcpconn").val("<?php echo $my_ini['kcpconn']; ?>");
  $("#kcpscavengettl").val("<?php echo $my_ini['kcpscavengettl']; ?>");
  $("#kcpautoexpire").val("<?php echo $my_ini['kcpautoexpire']; ?>");
  $("#kcpsndwnd").val("<?php echo $my_ini['kcpsndwnd']; ?>");
  $("#kcprcvwnd").val("<?php echo $my_ini['kcprcvwnd']; ?>");
  $("#kcpmtu").val("<?php echo $my_ini['kcpmtu']; ?>");
  $("#kcpdatashard").val("<?php echo $my_ini['kcpdatashard']; ?>");
  $("#kcpparityshard").val("<?php echo $my_ini['kcpparityshard']; ?>");
  $("#kcpdscp").val("<?php echo $my_ini['kcpdscp']; ?>");
  $("#kcpmtu").val("<?php echo $my_ini['kcpmtu']; ?>");
  </script>  

<!-- 读取配置显示结尾 -->
<script type="text/javascript">		
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
</script>		

  <!-- 二维码显示 -->  
  <script type="text/javascript">		
  qrw=$(window).width()-15;
  qrh=$(window).height()/2;
  var qrcode = new QRCode(document.getElementById("qrcode"), {
   	width : qrw,
   	height : qrh
 	}); //实例化类
 	function copyc (sslink) {		
 	if (!sslink) {
  		alert("ss链接输入为空");
  		return;
  	}
 	var clipboard = new ClipboardJS('#clip', {
        text: function() {
            return sslink;
        }
    });
    clipboard.on('success', function(e) {
        alert("复制到剪辑板成功");
    });
    clipboard.on('error', function(e) {
        alert("复制到剪辑板失败！");
    });
  }
 function makeCode (sslink) {		
  	if (!sslink) {
  		alert("ss链接输入为空");
  		return;
  	}
	   qrcode.makeCode(sslink); //
	}
  $("#shared").tap(function(){  //点触分享二维码时
    encodedData = window.btoa($("#method").val()+':'+$("#password").val());
    sslink = 'ss://'+encodedData+'@'+$("#server").val()+':'+$("#server_port").val()+'#'+$("#name").val();
    $("#copylink").attr('href',sslink);
    $("#copylink").text(sslink);
    makeCode(sslink);
  });
  $("#clip").click(function(){
    copyc(sslink);
  });
  </script>

</body>
</html>