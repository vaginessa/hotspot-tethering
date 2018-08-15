<?php
require '../Admin/main.class.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $acl = $_POST['acl'];
}
if (isset($acl)) {
  file_put_contents('custom.acl', $acl);
} 
//检查ss进程是否存在
if (binary_status(array('ss-local','ss-redir'))) {
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
  <meta name="Cache-Control" content"no-cache, no-store, must-revalidate" />
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
 .plugin_help {
     margin:5px;
     width: 95%;
     word-wrap:break-word;
     text-align:center;
     color: #ee82ee;
 }
 .server_toast {
     padding-left: 10%;
     text-align:center;
     overflow:hidden;
     white-space:nowrap;
 }
</style>
 <body ontouchstart="">

<div class="ui-tab">
                <ul style="box-shadow: 7px 7px 3px #888888;" class="ui-tab-nav ui-border-b">
                  <li class="current"><span>Shadowsocks</span></li>
                  <li><span>自定义规则</span></li>
                  <li><span id="shared">二维码分享</span></li>
                </ul>
                <ul class="ui-tab-content" style="width:300%">
<!-- Shadowsocks配置开始 -->                
                    <li>
     <div class="ui-form ui-border-t"> 
      <form action="shadowsocks.php" method="GET" autocomplete="on"> 
      
       <div class="ui-form-item ui-form-item-switch ui-border-b"> 
        <p>服务开关</p>
        <div style="padding-left: 25%;font-size: smaller;">
          <p class="ui-txt-warning" id="ts" >服务没有开启!</p>    
        </div>
        <label class="ui-switch"><input type="checkbox" id="shadowsocks" name="shadowsocks" /></label> 
       </div> 
       
       <div class="ui-form-item ui-border-b"> 
        <label>配置名称:</label> 
        <input type="text" placeholder="" id="name" name="name" autofocus required/> 
       </div> 
       
       <div class="ui-form-item ui-border-b"> 
       <label>服务器:</label> 
       <input type="text" id="server" name="server" style="display:none" required/> 
       <div class="server_toast">
       <p class="ui-txt-white" style="background:#000;">点击查看和编辑地址(支持直接输入ss://链接和域名地址解析)</p>
       </div>
       </div> 
       
       <div class="ui-form-item ui-border-b"> 
        <label>远程端口:</label> 
        <input type="number" id="server_port" list="server_port_list" min="1" max="65535" name="server_port" placeholder="" required/> 
        <datalist id="server_port_list" >
        <option value="80">
        <option value="443">
        <option value="8080">
        </datalist>
       </div> 
       
       <div class="ui-form-item ui-border-b"> 
        <label>密码:</label> 
        <input type="password" placeholder="" id="password" name="password" autocomplete="off" required/> 
       </div> 
       
       <div class="ui-form-item ui-border-b"> 
        <label>加密方式</label> 
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
           <label>路由</label> 
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
          <!--
        <div class="ui-form-item ui-border-b"> 
        <label>远程DNS:</label> 
        <input type="text" placeholder="8.8.8.8" id="remote_dns" name="remote_dns" autocomplete="on" pattern="[0-9]{1,3}(\.[0-9]{1,3}){3}" required/> 
       </div> 
          -->
          <div class="ui-form-item ui-form-item-switch ui-border-b"> 
           <p>TCP Fast Open</p>
           <div style="padding-left: 25%;font-size: smaller;">
           <p class="ui-txt-muted" onclick='alert("客户端可能需要Android5.0+\n服务端 Linux kernel > 3.7.0. 并且开启支持。\nTCP快速打开（英语：TCP Fast Open，简称TFO）是对计算机网络中传输控制协议（TCP）连接的一种简化握手手续的拓展，用于提高两端点间连接的打开速度")'>点击我显示介绍</p> 
           </div>
           <label class="ui-switch"><input type="checkbox" id="tcp_fast_open" name="tcp_fast_open" /></label> 
          </div> 
          <div class="ui-form-item ui-form-item-switch ui-border-b"> 
           <p>WIFI放行</p>
           <div style="padding-left: 25%;font-size: smaller;">
           <p class="ui-txt-muted">当使用wifi网络时不走代理</p> 
           </div>
           <label class="ui-switch"><input type="checkbox" id="wifi" name="wifi" /></label> 
          </div> 
          <div class="ui-form-item ui-form-item-switch ui-border-b"> 
           <p>ICMP放行</p>
           <div style="padding-left: 25%;font-size: smaller;">
           <p class="ui-txt-muted">icmp协议是否放行</p> 
           </div>
           <label class="ui-switch"><input type="checkbox" id="icmp" name="icmp" /></label> 
          </div> 
                    
          <div class="ui-form-item ui-border-b"> 
           <label>UDP控制</label> 
           <div class="ui-select-group"> 
            <div class="ui-select"> 
             <select name="udp" id="udp" class="ui-txt-feeds" > 
             <option value="accept" selected="">放行</option> 
             <option value="forward">服务器转发</option>
             <option value="udp_over_tcp">UDP over TCP</option> 
             <option value="drop">禁用</option> 
             </select> 
            </div> 
           </div> 
          </div> 
          
         <gost style="display:none">
          <div class="demo-item"> 
           <p class="demo-desc">gost</p> 
           <div class="demo-block"> 
            <div class="ui-form ui-border-t">
            <h4 class="plugin_help">GO语言实现的安全隧道
            <nav>
            <a href="https://github.com/ginuerzh/gost" target="_blank">项目地址</a> |
            <a href="https://github.com/yiguihai/gost_install" target="_blank">搭建脚本</a>
            <nav>
            </h4>            
             <div class="ui-form-item ui-border-b"> 
              <label>服务器:</label> 
              <input type="text" placeholder="" id="gost_server" name="gost_server" pattern="[0-9]{1,3}(\.[0-9]{1,3}){3}" /> 
             </div> 
             
             <div class="ui-form-item ui-border-b"> 
              <label>远程端口:</label> 
              <input type="number" min="1" max="65535" placeholder="" id="gost_server_port" name="gost_server_port" /> 
             </div> 
             
             <div class="ui-form-item ui-border-b"> 
              <label>用户名:</label> 
              <input type="text" placeholder="可选" id="gost_username" name="gost_username" /> 
             </div> 
             
             <div class="ui-form-item ui-border-b"> 
              <label>密码:</label> 
              <input type="password" placeholder="可选" id="gost_password" name="gost_password" autocomplete="off" /> 
             </div> 
             
            </div> 
           </div> 
          </div>
         </gost>
          
         </div> 
        </div> 
       </div>
          
  <!--- 插件类 -->
          <div class="demo-item"> 
           <p class="demo-desc">插件</p> 
           <div class="demo-block"> 
            <div class="ui-form ui-border-t"> 
            
             <div class="ui-form-item ui-border-b"> 
              <label>插件</label> 
              <div class="ui-select-group"> 
               <div class="ui-select"> 
                <select name="plugin" id="plugin" class="ui-txt-feeds"> 
                <option value="off" selected="">禁用</option> 
                <option value="obfs-local">Simple obfuscation</option>
                <option value="GoQuiet">GoQuiet</option>
                <option value="kcptun">kcptun</option> 
                <option value="proxychains">proxychains</option> 
                </select> 
               </div> 
              </div> 
             </div> 
             
           <plugin style="display:none">
           
            <obfs-local>
            <h4 class="plugin_help"><p>Simple-obfs is a simple obfusacting tool, designed as plugin server of shadowsocks.</p>
             obfs流量混淆插件，搭建选择libev版
            <nav>
            <a href="https://github.com/shadowsocks/simple-obfs-android" target="_blank">项目地址</a> |
            <a href="https://teddysun.com/486.html" target="_blank">搭建教程</a>
            <nav>
            </h4>
             <div class="ui-form-item ui-border-b"> 
              <label>Obfuscation wrapper</label> 
              <div class="ui-select-group"> 
               <div class="ui-select"> 
                <select name="obfs" id="obfs" class="ui-txt-feeds" > 
                <option value="tls" >tls</option> 
                <option value="http" selected="">http</option> </select> 
               </div> 
              </div> 
             </div> 
             <div class="ui-form-item ui-border-b"> 
              <label>Obfuscation hostname:</label> 
              <input type="text" placeholder="wap.10010.com" id="obfs_host" name="obfs_host" /> 
             </div> 
             </obfs-local>
             
             <GoQuiet>
            <h4 class="plugin_help">GoQuiet是一个shadowsocks的混淆插件，基本原理为模拟TLS的流量同时将服务器伪装成一个正常的网站服务器
            <nav>
            <a href="https://github.com/cbeuw/GoQuiet" target="_blank">项目地址</a> |
            <a href="https://github.com/yiguihai/GoQuiet" target="_blank">搭建脚本</a>            
            <nav>
            </h4>
             <div class="ui-form-item ui-border-b"> 
             <label>remoteHost:</label> 
              <input type="text" placeholder="远程服务IP" id="remoteHost" name="remoteHost" pattern="[0-9]{1,3}(\.[0-9]{1,3}){3}" /> 
             </div>
             <div class="ui-form-item ui-border-b"> 
              <label>remotePort:</label> 
              <input type="number" min="1" max="65535" placeholder="远程代理端口，默认443" id="remotePort" name="remotePort" /> 
             </div>
             <div class="ui-form-item ui-border-b"> 
              <label>ServerName:</label> 
              <input type="text" placeholder="你想让GFW认为你在访问的域名" id="ServerName" name="ServerName" /> 
             </div>
             <div class="ui-form-item ui-border-b"> 
              <label>Key:</label> 
              <input type="password" placeholder="密钥" id="Key" name="Key" autocomplete="off" /> 
             </div>
             <div class="ui-form-item ui-border-b"> 
              <label>TicketTimeHint:</label> 
              <input type="text" placeholder="3600 (session ticket过期的时间)" id="TicketTimeHint" name="TicketTimeHint" /> 
             </div>
             <div class="ui-form-item ui-border-b"> 
              <label>Browser</label> 
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
            <h4 class="plugin_help"><p>A Fast & Secure Tunnel Based On KCP with N:M Multiplexing</p> 
            Kcptun 是一个非常简单和快速的，基于 KCP 协议的 UDP 隧道，它可以将 TCP 流转换为 KCP+UDP 流。而 KCP 是一个快速可靠协议，能以比 TCP 浪费10%-20%的带宽的代价，换取平均延迟降低 30%-40%，且最大延迟降低三倍的传输效果。
            <nav>
            <a href="https://github.com/xtaci/kcptun" target="_blank">项目地址</a> |
            <a href="https://blog.kuoruan.com/110.html" target="_blank">搭建教程</a>
            <nav>
            </h4>
            <div class="ui-form-item ui-border-b"> 
             <label>remoteaddr:</label> 
              <input type="text" placeholder="default: vps:29900" id="kcpremoteaddr" name="kcpremoteaddr" pattern="[0-9]{1,3}(\.[0-9]{1,3}){3}\:[0-9]{1,5}"/> 
             </div>
             <div class="ui-form-item ui-border-b"> 
             <label>key:</label> 
              <input type="password" placeholder="default: it's a secrec" id="kcpkey" name="kcpkey" autocomplete="off" /> 
             </div>
             <div class="ui-form-item ui-border-b"> 
              <label>crypt</label> 
              <div class="ui-select-group"> 
               <div class="ui-select"> 
                <select name="kcpcrypt" id="kcpcrypt" class="ui-txt-feeds" > 
                <option value="aes">aes</option> 
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
                <option value="none" selected="">none</option>
                </select> 
               </div> 
              </div> 
             </div> 
             <div class="ui-form-item ui-border-b"> 
              <label>mode</label> 
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
             <label>conn:</label> 
              <input type="number" placeholder="default: 1" id="kcpconn" name="kcpconn" /> 
             </div>
             <div class="ui-form-item ui-border-b"> 
             <label>autoexpire:</label> 
              <input type="number" placeholder="default: 0" id="kcpautoexpire" name="kcpautoexpire" /> 
             </div>
             <div class="ui-form-item ui-border-b"> 
             <label>scavengettl:</label> 
              <input type="number" placeholder="default: 600" id="kcpscavengettl" name="kcpscavengettl" /> 
             </div>
             <div class="ui-form-item ui-border-b"> 
             <label>mtu:</label> 
              <input type="number" placeholder="default: 1350" id="kcpmtu" name="kcpmtu" /> 
             </div>
             <div class="ui-form-item ui-border-b"> 
             <label>sndwnd:</label> 
              <input type="number" placeholder="default: 128" id="kcpsndwnd" name="kcpsndwnd" /> 
             </div>
             <div class="ui-form-item ui-border-b"> 
             <label>rcvwnd:</label> 
              <input type="number" placeholder="default: 512" id="kcprcvwnd" name="kcprcvwnd" /> 
             </div>
             <div class="ui-form-item ui-border-b"> 
             <label>datashard:</label> 
              <input type="number" placeholder="default: 10" id="kcpdatashard" name="kcpdatashard" /> 
             </div>
             <div class="ui-form-item ui-border-b"> 
             <label>parityshard:</label> 
              <input type="number" placeholder="default: 3" id="kcpparityshard" name="kcpparityshard" /> 
             </div>
             <div class="ui-form-item ui-border-b"> 
             <label>dscp:</label> 
              <input type="number" placeholder="default: 0" id="kcpdscp" name="kcpdscp" /> 
             </div>
             <div class="ui-form-item ui-border-b"> 
              <label>数据压缩</label> 
              <div class="ui-select-group"> 
               <div class="ui-select"> 
                <select name="nocomp" id="kcpnocomp" class="ui-txt-feeds" > 
                <option value="" selected="">开</option>
                <option value="--nocomp">关</option>                 
                </select> 
               </div> 
              </div> 
             </div>
            </kcptun>
            
            <proxychains>  
            <h4 class="plugin_help"><p>添加一个前置代理中转Shadowsocks流量。(<span>注意: Shadowsocks端口必须为 <b style="color:red">443</b> 前置代理需要支持https才能联网转发数据</span>)</p>提取自ShadowsocksR安卓版。
            <nav>
            <a href="https://github.com/shadowsocks/proxychains-ng" target="_blank">项目地址</a>
            <nav>
            </h4>
            <div class="ui-form-item ui-border-b"> 
              <label>类型</label> 
              <div class="ui-select-group"> 
               <div class="ui-select"> 
                <select name="proxychains_type" id="proxychains_type" class="ui-txt-feeds" > 
                <option value="http" selected="">http</option> 
                <option value="socks4">socks4</option> 
                <option value="socks5">socks5</option> </select> 
               </div> 
              </div> 
             </div> 
             <div class="ui-form-item ui-border-b"> 
             <label>地址:</label> 
              <input type="text" placeholder="" id="proxychains_address" name="proxychains_address" pattern="[0-9]{1,3}(\.[0-9]{1,3}){3}" /> 
             </div>
             <div class="ui-form-item ui-border-b"> 
              <label>端口:</label> 
              <input type="number" min="1" max="65535" placeholder="" id="proxychains_port" name="proxychains_port" /> 
             </div>
             <div class="ui-form-item ui-border-b"> 
              <label>用户名:</label> 
              <input type="text" placeholder="可选" id="proxychains_username" name="proxychains_username" /> 
             </div>
             <div class="ui-form-item ui-border-b"> 
              <label>密码:</label> 
              <input type="password" placeholder="可选" id="proxychains_password" name="proxychains_password" autocomplete="off" /> 
             </div>
            </proxychains>
                          
           </plugin>
             
            </div> 
           </div> 
          </div>
          
  <!-- 插件类结尾 -->

        <div class="ui-btn-wrap"> 
         <button class="ui-btn-lg ui-btn-primary"> 提交 </button> 
        </div>         
       </form> 
      </div> 
            </li>
<!-- Shadowsocks配置结束 -->     

<!-- 自定义规则编辑 -->            
            <li>
            <textarea rows="35" style="width:99%" cols="40" name="acl" form="acl" placeholder="自定义acl规则"><?php echo file_get_contents('custom.acl'); ?></textarea><form action="" method="POST" id="acl"><button class="ui-btn-lg ui-btn-primary">保存</button><button type="reset" class="ui-btn-lg">重置输入</button></form>
            </li>
<!-- 自定义规则结束 -->                                        

<!-- 二维码分享 -->         
            <li>
            <div id="qrcode" style="margin-top:15px;margin-bottom:25px"></div>
            <div style="width:95%;text-align:left;word-wrap:break-word;"><a id="copylink" href=""></a></div> 
            <br />
            <button class="ui-btn-lg" id="clip">复制到剪辑板</button>
            </li>
<!-- 二维码分享结束 -->                     
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
  if ($("#plugin").val() == "proxychains") { 
    $("proxychains").show();
  } else {
    $("proxychains").hide();
  }
}
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
  $("#proxychains_password").focus(function(){
    $(this).attr('type','text');
  });
  $("#proxychains_password").blur(function(){
    $(this).attr('type','password');
  });
  </script>
  
  <script type="text/javascript">		
 $("#udp").change(function(){
    if($(this).val()=="udp_over_tcp") { 
        $("gost").show();
      } else { 
        $("gost").hide();
      }
  });
  $("#plugin").change(function(){
    setplugin();
  });
  </script>
  
  <script type="text/javascript"> 
  $(".server_toast").tap(function(){
    $(this).remove();
    $("#server").show();
  });
  </script>
  
  <!-- 读取配置显示 -->
  
 <script type="text/javascript">		
   if (<?php echo $status; ?>) { 
     $('#shadowsocks').prop('checked', true); 
     $('#ts').html('<p class="ui-txt-feeds">服务已开启!</p>');
   }
  </script>		
  

<script type="text/javascript">		
  $("#name").val("<?php echo $my_ini['name']; ?>");
  $("#server").val("<?php echo $my_ini['server']; ?>");
  $("#server_port").val("<?php echo $my_ini['server_port']; ?>");
  $("#password").val("<?php echo $my_ini['password']; ?>");
if ("<?php echo $my_ini['method']; ?>" != "") { 
  $("#method").val("<?php echo $my_ini['method']; ?>");
}
if ("<?php echo $my_ini['route']; ?>" != "") { 
  $("#route").val("<?php echo $my_ini['route']; ?>");
}  
if (<?php echo @file_get_contents('/proc/sys/net/ipv4/tcp_fastopen'); ?> > 0) { 
  $('#tcp_fast_open').prop('checked', true); 
}
if ("<?php echo $my_ini['wifi']; ?>" == 1) { 
  $('#wifi').prop('checked', true); 
}
if ("<?php echo $my_ini['icmp']; ?>" == 1) { 
  $('#icmp').prop('checked', true); 
}
var udpkz = "<?php echo $my_ini['udp']; ?>";
if (udpkz!=null && udpkz!="") { 
  if(udpkz=="udp_over_tcp") { 
    $("gost").show();
  }
  $("#udp").val(udpkz);
}
  $("#gost_server").val("<?php echo $my_ini['gost_server']; ?>");
  $("#gost_server_port").val("<?php echo $my_ini['gost_server_port']; ?>");
  $("#gost_username").val("<?php echo $my_ini['gost_username']; ?>");
  $("#gost_password").val("<?php echo $my_ini['gost_password']; ?>");
if ("<?php echo $my_ini['plugin']; ?>" != "") {
  $("#plugin").val("<?php echo $my_ini['plugin']; ?>");
  setplugin();
}
if ("<?php echo $my_ini['obfs']; ?>" != "") { 
  $("#obfs").val("<?php echo $my_ini['obfs']; ?>");
}
  $("#obfs_host").val("<?php echo $my_ini['obfs_host']; ?>");
  $("#remotePort").val("<?php echo $my_ini['remotePort']; ?>");
  $("#remoteHost").val("<?php echo $my_ini['remoteHost']; ?>");
  $("#ServerName").val("<?php echo $my_ini['ServerName']; ?>");
  $("#Key").val("<?php echo $my_ini['Key']; ?>");
  $("#TicketTimeHint").val("<?php echo $my_ini['TicketTimeHint']; ?>");
if ("<?php echo $my_ini['Browser']; ?>" != "") { 
  $("#Browser").val("<?php echo $my_ini['Browser']; ?>");
}
  $("#kcpremoteaddr").val("<?php echo $my_ini['kcpremoteaddr']; ?>");
  $("#kcpkey").val("<?php echo $my_ini['kcpkey']; ?>");
if ("<?php echo $my_ini['kcpcrypt']; ?>" != "") { 
  $("#kcpcrypt").val("<?php echo $my_ini['kcpcrypt']; ?>");
}
if ("<?php echo $my_ini['kcpmode']; ?>" != "") { 
  $("#kcpmode").val("<?php echo $my_ini['kcpmode']; ?>");
}
  $("#kcpconn").val("<?php echo $my_ini['kcpconn']; ?>");
  $("#kcpscavengettl").val("<?php echo $my_ini['kcpscavengettl']; ?>");
  $("#kcpautoexpire").val("<?php echo $my_ini['kcpautoexpire']; ?>");
  $("#kcpmtu").val("<?php echo $my_ini['kcpmtu']; ?>");
  $("#kcpsndwnd").val("<?php echo $my_ini['kcpsndwnd']; ?>");
  $("#kcprcvwnd").val("<?php echo $my_ini['kcprcvwnd']; ?>");
  $("#kcpmtu").val("<?php echo $my_ini['kcpmtu']; ?>");
  $("#kcpdatashard").val("<?php echo $my_ini['kcpdatashard']; ?>");
  $("#kcpparityshard").val("<?php echo $my_ini['kcpparityshard']; ?>");
  $("#kcpdscp").val("<?php echo $my_ini['kcpdscp']; ?>");
  $("#kcpnocomp").val("<?php echo $my_ini['kcpnocomp']; ?>");
if ("<?php echo $my_ini['proxychains_type']; ?>" != "") { 
  $("#proxychains_type").val("<?php echo $my_ini['proxychains_type']; ?>");
}
  $("#proxychains_address").val("<?php echo $my_ini['proxychains_address']; ?>");
  $("#proxychains_port").val("<?php echo $my_ini['proxychains_port']; ?>");
  $("#proxychains_username").val("<?php echo $my_ini['proxychains_username']; ?>");
  $("#proxychains_password").val("<?php echo $my_ini['proxychains_password']; ?>");
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
qrw = $(window).width() - 15;
qrh = $(window).height() / 2;
var qrcode = new QRCode(document.getElementById("qrcode"), {
    width: qrw,
    height: qrh
}); //实例化类
function makeCode(sslink) {
    if (!sslink) {
        alert("ss链接输入为空");
        return;
    }
    qrcode.makeCode(sslink); //
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
$("#shared").tap(function() { //点触分享二维码时
    encodedData = window.btoa($("#method").val() + ':' + $("#password").val());
    sslink = 'ss://' + encodedData + '@' + $("#server").val() + ':' + $("#server_port").val() + '#' + $("#name").val();
    $("#copylink").attr('href', sslink);
    $("#copylink").text(sslink);
    makeCode(sslink);
});
</script>

</body>
</html>