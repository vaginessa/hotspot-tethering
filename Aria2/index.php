<?php
require '../Admin/main.class.php';

//magnet:?xt=urn:btih:
//不要谢我！:)

clearstatcache();

if(!is_file('aria2c')) {
  die('程序主文件不见了');
}

if(!is_file('aria2.conf')) {
  die('程序配置文件不见了');
}

if(!is_dir('Download')) {
  mkdir('Download');
}

$mk=array("aria2.log","aria2.session","Cookie","dht.dat");

foreach ($mk as $val) { 
  if(!is_file($val)) {
    touch($val);
  }
}


$receive=htmlspecialchars($_POST["receive"]);
$binary=sys_get_temp_dir()."/aria2c";
$dir = dirname(__FILE__);
$pkill = toolbox_check()[1]." pkill aria2c";
$run = $binary.' --conf-path='.$dir.'/aria2.conf --dir='.$dir.'/Download --log='.$dir.'/aria2.log --input-file='.$dir.'/aria2.session --save-session='.$dir.'/aria2.session --save-cookies='.$dir.'/Cookie --load-cookies='.$dir.'/Cookie --dht-file-path='.$dir.'/dht.dat';

if(!is_executable($binary) and file_exists('aria2c')) {
  copy('aria2c', $binary);
  chmod($binary, 0700);
}

if ($receive=="start") {
   exec($run, $output, $return_val);
   if ($return_val == 0) { 
     die('运行成功！(请手动刷新页面)');
   } else { 
     die('运行失败！返回值: '.$return_val);
   }
}
if ($receive=="stop") {
   exec($pkill, $output, $return_val);
   if ($return_val == 0) { 
     die('停止运行成功！(请手动刷新页面)');
   } else { 
     die('停止运行失败！返回值: '.$return_val);
   }
}
if ($receive=="update") {
   update();
}

function update() {
//https://m.jb51.net/article/26604.htm
  $data = GET("https://raw.githubusercontent.com/ngosang/trackerslist/master/trackers_all.txt");
  if (empty($data)) { 
    die("下载文件失败");
  }
$data = str_replace("announce", "announce,", explode(PHP_EOL, $data));
$tmp_file=tmpfile();
$conf_file="aria2.conf";
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
die('trackers已经更新完成！请重启aria2。');
}

?>