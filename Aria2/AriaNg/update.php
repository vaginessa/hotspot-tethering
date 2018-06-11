<html>
<head>
<meta content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no,minimal-ui" name="viewport">
<?php
//https://m.jb51.net/article/26604.htm
require "../../tools/curl.php";
require "../../tools/busybox.php";
$pkill=busybox_check("pkill");
$data = GET("https://raw.githubusercontent.com/ngosang/trackerslist/master/trackers_all.txt");
if (empty($data)) die("下载文件失败");
$data = str_replace("announce", "announce,", explode(PHP_EOL, $data));
$tmp_file=tmpfile();
$conf_file="../aria2.conf";
foreach ($data as $value)  {
    fwrite($tmp_file, $value);
    fseek($tmp_file, 0);
    $str=fread($tmp_file, 4096);
    $newstr=substr($str,0,strlen($str)-1); 
  }
$aria2_conf = parse_ini_file($conf_file);
unlink($conf_file);
foreach ($aria2_conf as $key => $value) {
    if ($key and $value) { 
     if ($value == 1) $value="true";
      if ($key == "bt-tracker") $value=$newstr;
    file_put_contents($conf_file, "$key=$value".PHP_EOL, FILE_APPEND | LOCK_EX);
    }
  }
fclose($tmp_file);
echo "trackers已经升级完成！重启aria2中...";
shell_exec("$pkill aria2c");
die(header("Refresh:2,Url=../"));
?>
</head>
<body>

</body>
</html>