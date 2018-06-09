<html>
<head>
<?php
//https://m.jb51.net/article/26604.htm
function curlGet($surl)  
{  
    $ssl = substr($surl, 0, 8) == "https://" ? TRUE : FALSE;  
    $ch = curl_init();  
    $opt = array(  
            CURLOPT_URL     => $surl,  
            CURLOPT_USERAGENT    => $_SERVER ['HTTP_USER_AGENT'],  
            CURLOPT_COOKIE  => '',
            CURLOPT_HEADER  => false,  
            CURLOPT_RETURNTRANSFER  => true,  
            CURLOPT_TIMEOUT => 30,  
            );  
    if ($ssl)  
    {  
        $opt[CURLOPT_SSL_VERIFYHOST] = 1;  
        $opt[CURLOPT_SSL_VERIFYPEER] = FALSE;  
    }  
    curl_setopt_array($ch, $opt);  
    $data = curl_exec($ch);  
    curl_close($ch);
    return $data;
}
$data = curlGet('https://raw.githubusercontent.com/ngosang/trackerslist/master/trackers_all.txt');
$data = str_replace("announce", 'announce,', explode(PHP_EOL, $data));
$tmp_file=tmpfile();
$conf_file='../aria2.conf';
foreach ($data as $value) fwrite($tmp_file, $value);
    fseek($tmp_file, 0);
    $str=fread($tmp_file, 4096);
    $newstr=substr($str,0,strlen($str)-1); 
    $aria2_conf = parse_ini_file($conf_file);
    unlink($conf_file);
foreach ($aria2_conf as $key => $value) {
    if ($key and $value) { 
     if ($value == 1) $value='true';
      if ($key == "bt-tracker") $value=$newstr;
    file_put_contents($conf_file, "$key=$value".PHP_EOL, FILE_APPEND | LOCK_EX);
    }
  }
fclose($tmp_file);
echo 'trackers已经升级完成！2s 后跳转';
die(header('Refresh:2,Url=../'));
?>
</head>
<body>

</body>
</html>