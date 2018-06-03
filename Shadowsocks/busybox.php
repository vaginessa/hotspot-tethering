<?php
function busybox_check($binary) { 
 $tools_dir = array("/system/bin/busybox", "/system/xbin/busybox", "/system/bin/toybox", "/system/xbin/toybox");
    foreach ($tools_dir as $value) {
            if(file_exists("$value")) return $file = "$value $binary";
        }
            if(empty($file)) return busybox_copy($binary);
         }

function busybox_copy($binary) { 
   if(file_exists('busybox'))
    { 
      $tmp_file = sys_get_temp_dir()."/busybox";
        if(!is_executable($tmp_file))
            {
                copy('busybox', $tmp_file);
                chmod($tmp_file, 0700);
            }
          return $file = "$tmp_file $binary";
      }
     if(empty($file)) return "$binary";
   }
?>