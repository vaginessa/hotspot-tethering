<?php
require '../Admin/main.class.php';
//错误屏蔽
error_reporting(0);
//修正时间
date_default_timezone_set("Asia/Shanghai");
//时间日期
$date=date("Y-m-d H:i:s");
//时间戳
$time=time();

$user_file="user.json";
//获取用户文件
$json_string=file_get_contents($user_file);
//json解码
$data=json_decode($json_string, true);
//生成用户名
$user_count=count($data)."_$time";

//创建json文件
if (!is_array($data)) {
    file_put_contents($user_file, '[]', LOCK_EX);
}

//用户状态改变函数
function user_change($data, $status, $user_mac) {
    foreach ($data as $key => $value) {
        foreach ($value as $user => $info) {
            $macaddress = $info['mac_address'];
            if ($macaddress == "$user_mac") {
                $data[$key][$user]['status']="$status";
            }
        }
    }
    return array_filter($data);
}


//用户添加函数
function user_add($data, $user_count, $date, $user_ip, $user_mac) {
    $add_user = array(
        "user_$user_count" => array(
            'ip_address' => "$user_ip",
            'mac_address' => "$user_mac",
            'status' => "OK",
            'up_time' => "$date"
        )
    );
    array_push($data, $add_user);
    return array_filter($data);
}


//删除用户函数
function user_del($data, $user_count, $user_ip, $user_mac) {
    foreach ($data as $key => $value) {
        foreach ($value as $user => $info) {
            $ipaddress = $info['ip_address'];
            $macaddress = $info['mac_address'];
            if ($user == "$user_count") {
                unset($data[$key][$user]);
            }
            if ($ipaddress == "$user_ip") {
                unset($data[$key][$user]);
            }
            if ($macaddress == "$user_mac") {
                unset($data[$key][$user]);
            }
        }
    }
    return array_filter($data);
}

//获取mac地址
function get_mac($user_ip) {
    $arp_file = explode(PHP_EOL, file_get_contents("/proc/net/arp"));
    foreach ($arp_file as $arp) {
        $ip = preg_match_all('/[0-9]{1,3}(\.[0-9]{1,3}){3}/', $arp, $matchs);
        $ip = $matchs[0][0];
        $mac = preg_match_all('/[0-9a-fA-F]{2}(:[0-9a-fA-F]{2}){5}/', $arp, $matchs);
        $mac = $matchs[0][0];
        if ($ip == "$user_ip" and $mac) {
            return $mac;
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $yhdz = $_POST['yhdz'];
    $yhmac = $_POST['yhmac'];
    $number = $_POST['number'];
}
if ($yhdz and $yhmac and $number) {
    if ($yhdz == "activation") {
        file_put_contents($user_file, json_encode(user_change($data, "OK", $yhmac)) , LOCK_EX);
    }
    if ($yhdz == "block") {
        file_put_contents($user_file, json_encode(user_change($data, "Block", $yhmac)) , LOCK_EX);
    }
    if ($yhdz == "deleted") {
        file_put_contents($user_file, json_encode(user_del($data, '', '', $yhmac)) , LOCK_EX);
    }
}
?>