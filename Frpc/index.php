<?php
$config = $_POST['config'];
if (isset($config)) {
    @unlink('frpc.ini');
    $file_config=explode(PHP_EOL, $config);
    for($i = 0; $i < count($file_config); $i++) { 
        if (stripos($file_config[$i], '#') === false) { 
            file_put_contents('frpc.ini', $file_config[$i].PHP_EOL, FILE_APPEND);
        }
    }
}
$binary_file = sys_get_temp_dir() . '/frpc';
if (!is_executable($binary_file) && file_exists('frpc')) {
    copy('frpc', $binary_file);
    chmod($binary_file, 0700);
}
function _exec($f,$d,$p) { 
    exec("$f $d > /dev/null 2>&1 &", $output, $return_val);
    if ($return_val == 0) { 
        die("{\"a\": \"$p 成功\",\"b\": 0}");
    } else { 
        die("{\"a\": \"$p 失败！返回值: $return_val\",\"b\": 1}");
    }
}
$x = $_POST['receive'];
if (isset($x)) { 
    if ($x == 'start') { 
        _exec("$binary_file -c ",__DIR__.'/frpc.ini','frpc启动');
    } elseif ($x == 'stop') { 
        _exec('pkill ','frpc','frpc停止');
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, initial-scale=1.0, user-scalable=no" />
    <title>Frp内网穿透</title>    
    <link rel="stylesheet" href="../css/frozenui.css">
    <link rel="stylesheet" href="../css/style.css">    
</head>
<title>关于手机</title>
<style type="text/css">
 li {
    height:100%;
    padding-right: 1px;
    overflow-y: scroll;
 }
</style>
<body ontouchstart>
<section id="tab">
   <div class="demo-item">
       <p class="demo-desc"><?php echo '版本 '.shell_exec("$binary_file -v"); ?></p>
         <div class="demo-block">
            <div class="ui-tab">
                <ul style="box-shadow: 7px 7px 3px #888888;" class="ui-tab-nav ui-border-b">
                  <li class="current"><span>查看配置</span></li>
                  <li><span>粘贴配置</span></li>
                  <li><span>运行日志</span></li>
                  <li><span>帮助关于</span></li>
                </ul>
                <ul class="ui-tab-content" style="width:400%">
                    <li>
                    <?php
                    if (file_exists('frpc.ini')) { 
                        echo "<ul class=\"ui-list ui-list-single ui-list-link ui-border-tb\">";
                        $my_ini = parse_ini_file('frpc.ini', true);
                        foreach ($my_ini as $key => $val) { 
                            if ($key) { 
                                echo "<hr><p class=\"demo-desc\">$key</p>";
                            }
                            if ($val) {
                                foreach ($val as $key2 => $val2) { 
                                    echo "<li class=\"ui-border-t\"><div class=\"ui-list-info\"><h4 class=\"ui-nowrap\">".$key2."</h4><div class=\"ui-txt-info\">$val2</div></div></li>";
                                    if ($key2 == 'log_file' && $val) { 
                                         $log_file=$val2;
                                    }
                                }
                            }
                        }
                    echo "</ul>"; 
                    }
                    ?>
                   </li>
                    <li>
                    <textarea rows="35" style="width:99%" cols="40" name="config" form="form_config" placeholder="自定义"><?php if (file_exists('frpc.ini')) echo file_get_contents('frpc.ini'); ?></textarea><form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST" id="form_config"><button class="ui-btn-lg ui-btn-primary">保存配置</button><button type="reset" class="ui-btn-lg">重置输入</button></form>
                    </li>
                    <li>
                    <?php 
                        if (file_exists($log_file)) { 
                            $ll=explode(PHP_EOL, file_get_contents($log_file));
                            for($i = 0; $i < count($ll); $i++) { 
                                echo $ll[$i].'<br>';
                            }
                        }
                    ?>
                    </li>
                    <li>
                    <a href="https://github.com/fatedier/frp/blob/master/README_zh.md">访问Github</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>
<script src="../js/zepto.min.js"></script>    
<script type="text/javascript">
$('.ui-tab-content').css('height', $(window).height()+'px'); //屏幕高
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

</body>
</html>
            