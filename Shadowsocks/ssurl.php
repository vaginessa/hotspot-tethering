<?php

function android_share_input($ss) {
    $ss = str_replace("ss://", '', explode('@', $ss));
    $s_p = explode(':', $ss[1]);
    $server = $s_p[0];
    $s_p = explode('#', $s_p[1]);
    $server_port = $s_p[0];
    $m_p = base64_decode($ss[0]);
    $m_p = explode(':', $m_p);
    $password = $m_p[1];
    $method = $m_p[0];
    if ($server and $server_port and $password and $method) {
        return array(
            $name,
            $server,
            $server_port,
            $password,
            $method
        );
    }
}
function share_input($ss) {
    $ss = str_replace("ss://", '', $ss);
    $ss = base64_decode($ss);
    $m_p = explode('@', $ss);
    $s_p = $m_p[1];
    $s_p = explode(':', $m_p[1]);
    $server = $s_p[0];
    $server_port = $s_p[1];
    $m_p = explode(':', $m_p[0]);
    $method = $m_p[0];
    $password = $m_p[1];
    if ($server and $server_port and $password and $method) {
        return array(
            $server,
            $server_port,
            $password,
            $method
        );
    }
}
?>
