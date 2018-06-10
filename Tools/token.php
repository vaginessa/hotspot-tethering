<?php
function set_token() {
    $_SESSION['token'] = md5(microtime(true));
}
function valid_token() {
    $return = $_REQUEST['token'] === $_SESSION['token'] ? true : false;
    set_token();
    return $return;
}
?>