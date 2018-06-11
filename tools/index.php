<!DOCTYPE html>
<html>
<head> 
<meta charset="utf-8"> 
<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, initial-scale=1.0, user-scalable=no" />
<title>代理检查</title> 
</head>
<body>
<?php

if ($_SERVER["REQUEST_METHOD"] == "GET") {
   $URL = test_input($_GET['URL']);
   $Protocol = test_input($_GET['Protocol']);
   $Host = test_input($_GET['Host']);
   $Port = test_input($_GET['Port']);
   $User = test_input($_GET['User']);
   $Password = test_input($_GET['Password']);
}

function test_input($data) {
   $data = trim($data);
   $data = stripslashes($data);
   $data = htmlspecialchars($data);
   return $data;
}

function proxy_check($URL,$Protocol,$Host,$Port,$User,$Password) { 
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, "$URL"); //设置URL
curl_setopt($curl, CURLOPT_HEADER, 1); //获取Header
curl_setopt($curl, CURLOPT_NOBODY,true); //Body就不要了吧，我们只是需要Head
curl_setopt($curl, CURLOPT_TIMEOUT,5); //检测网络超时
curl_setopt($curl, CURLOPT_PROXY, "$Host");
if ($Port) {
curl_setopt($curl, CURLOPT_PROXYPORT, "$Port");
}
if ($User and $Password) {
curl_setopt($curl, CURLOPT_PROXYUSERPWD, "$User:$Password");
}
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); //数据存到成字符串吧，别给我直接输出到屏幕了
$data = curl_exec($curl); //开始执行啦～
return curl_getinfo($curl, CURLINFO_HTTP_CODE); //我知道HTTPSTAT码哦～
curl_close($curl); //用完记得关掉他
}

if ($URL and $Protocol and $Host and $Port) {
   if (proxy_check($URL,$Protocol,$Host,$Port,$User,$Password) == 200) {
   echo "<script type='text/javascript'>alert('代理可用');</script>";
   } else {
   echo "<script type='text/javascript'>alert('代理失败！');</script>";
   }
}
?>

  <form action="<?php echo $_SERVER["PHP_SELF"];?>" method="get">
  网址: <input type="text" value="wap.baidu.com" id="URL" name="URL"><br>
  Address: <input type="text" id="Host" name="Host"><br>
  Port:<input type="text" id="Port" name="Port"><br>
  用户名: <input type="text" id="User" name="User"><br>
  密码: <input type="text" id="Password" name="Password"><br>
  <select id="Protocol" name="Protocol">
  <option value="CURLPROXY_HTTP">http</option>
  <option value="CURLPROXY_SOCKS4">socks4</option>
  <option value="CURLPROXY_SOCKSA">socks4a</option>
  <option value="CURLPROXY_SOCKS5">socks5</option>
  <option value="CURLPROXY_SOCKS5_HOSTNAME">socks5_hostname</option>
</select>
  <input type="submit" value="提交">
</form>
<script type="text/javascript">
var URL = "<?php echo $URL; ?>";
if (URL != "") document.getElementById('URL').value = URL;
document.getElementById('Host').value = "<?php echo $Host; ?>";
document.getElementById('Port').value = "<?php echo $Port; ?>";
document.getElementById('User').value = "<?php echo $User; ?>";
document.getElementById('Password').value = "<?php echo $Password; ?>";
document.getElementById('Protocol').value = "<?php echo $Protocol; ?>";
</script>
</body>
</html>