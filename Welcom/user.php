<?php
//错误屏蔽
error_reporting(0);
//修正时间
date_default_timezone_set("Asia/Shanghai");
//时间日期
$date=date("Y-m-d H:i:s");
//时间戳
$time=time();

//获取用户文件
$json_string=file_get_contents("user.json");
//json解码
$data=json_decode($json_string, true);
//生成用户名
$user_count=count($data)."_$time";

//创建json文件
if (!is_array($data) or !is_object($data)) {
    file_put_contents("user.json", '[]', LOCK_EX);
}

//用户添加函数
function user_add($data, $user_count, $date, $user_ip, $user_mac) {
    $add_user = array(
        "user_$user_count" => array(
            'ip_address' => "$user_ip",
            'mac_address' => "$user_mac",
            'up_time' => "$date"
        )
    );
    array_push($data, $add_user);
    return $data;
}


//删除用户函数
function user_del($data, $user_count, $user_ip, $user_mac) {
    for ($i = 0; $i < count($data); $i++) {
        foreach ($data[$i] as $user => $info) {
            $ipaddress = $info['ip_address'];
            $macaddress = $info['mac_address'];
            if ($user == "$user_count") {
                unset($data[$i][$user]);
            }
            if ($ipaddress == "$user_ip") {
                unset($data[$i][$user]);
            }
            if ($macaddress == "$user_mac") {
                unset($data[$i][$user]);
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
?>