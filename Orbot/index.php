<?php
require '../Admin/main.class.php';
$mk = array(
    'geoip',
    'tor',
    'torrc'
);
$receive = htmlspecialchars($_POST['receive']);
$yxfile = sys_get_temp_dir() . '/tor.sh';
$binary_file = sys_get_temp_dir() . '/tor';
$tor_info = explode(PHP_EOL, file_get_contents('torrc'));
foreach ($mk as $val) {
    if (!file_exists($val)) {
        die("{\"a\": \"缺失 $val 文件！\",\"b\": 1}");
    }
}
if (!is_executable($binary_file) and file_exists('tor')) {
    copy('tor', $binary_file);
    chmod($binary_file, 0777);
}
function zx_input($yxfile, $yx, $lx) {
    if (file_exists($yxfile) or is_executable($yxfile)) {
        unlink($yxfile);
    }
    file_put_contents($yxfile, $yx);
    chmod($yxfile, 0700);
    exec("su -c $yxfile", $output, $return_val);
    if ($return_val != 0) {
        die("{\"a\": \"iptables运行失败！返回值: $return_val\",\"b\": 1}");
    } else {
        die("{\"a\": \"$lx 成功\",\"b\": 0}");
    }
}
if (isset($receive) and $receive == 'start') {
    unlink('torrc');
    foreach ($tor_info as $value) {
        $tor_info = explode(' ', $value);
        $key = $tor_info[0];
        $value = $tor_info[1];
        if ($key == 'GeoIPFile') {
            $value = dirname(__FILE__) . '/geoip';
        }
        if ($key == 'PidFile') {
            $value = dirname(__FILE__) . '/tor.pid';
        }
        if ($key != '' and $value != '') {
            file_put_contents('torrc', "$key $value" . PHP_EOL, FILE_APPEND);
        }
    }
    $run = 'export HOME=' . sys_get_temp_dir() . PHP_EOL . $binary_file . ' -f ' . dirname(__FILE__) . '/torrc';
    exec($run, $output, $return_val);
    if ($return_val != 0) {
        die("{\"a\": \"tor启动失败！返回值: $return_val\",\"b\": 1}");
    } else {
        $yx = 'iptables -t nat -F out_forward' . PHP_EOL . 'iptables -t nat -A out_forward -p tcp -j REDIRECT --to-ports 9040' . PHP_EOL . 'iptables -t nat -A out_forward -p udp --dport 53 -j REDIRECT --to-ports 5400' . PHP_EOL;
        zx_input($yxfile, $yx, 'tor启动');
    }
}
if (isset($receive) and $receive == 'stop') {
    if (file_exists('tor.pid')) {
        $pid = file_get_contents('tor.pid');
        unlink('tor.pid');
    } else {
        die("{\"a\": \"缺少pid文件！\",\"b\": 1}");
    }
    exec("kill $pid", $output, $return_val);
    if ($return_val != 0) {
        die("{\"a\": \"tor停止失败！返回值:  $return_val\",\"b\": 1}");
    } else {
        $yx = 'iptables -t nat -F out_forward' . PHP_EOL . 'iptables -t nat -A out_forward -p tcp -j REDIRECT --to-ports 1024' . PHP_EOL . 'iptables -t nat -A out_forward -p udp --dport 53 -j DNAT --to-destination 1.1.1.1:53' . PHP_EOL;
        zx_input($yxfile, $yx, 'tor停止');
    }
}
?>