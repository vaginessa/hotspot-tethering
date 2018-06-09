<?php
function GET($URL) {
    $SSl = substr($URL, 0, 8) == "https://" ? TRUE : FALSE;
    $ch = curl_init();
    $opt = array(
        CURLOPT_URL => $URL,
        CURLOPT_USERAGENT => $_SERVER["HTTP_USER_AGENT"],
        CURLOPT_COOKIE => "",
        CURLOPT_HEADER => false,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
    );
    if ($SSL) {
        $opt[CURLOPT_SSL_VERIFYHOST] = 2;
        $opt[CURLOPT_SSL_VERIFYPEER] = false;
    }
    curl_setopt_array($ch, $opt);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

?>