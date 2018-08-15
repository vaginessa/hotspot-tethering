<?php
//show_source('shadowsocks.php'); 
    $binary = sys_get_temp_dir() . '/pdnsd';
    $config = __DIR__ . '/pdnsd.conf';
    $r_c=file_get_contents($config);
    if (@unlink($config)===true) {  
      foreach (explode(PHP_EOL,$r_c) as $key) {
          $val = explode('=', $key);
          if($val[0]==' cache_dir ') {
             $val[1]=' "'.__DIR__.'"';
           }
           if($val[0]&&$val[1]) {
             echo "$val[0]=$val[1]<br>";
           } else { 
             echo "$val[0]<br>";
           }
       }
    }
?>