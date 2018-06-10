<?php
function getUrlKeyValue($url)
{
    $result = array();
    $mr = preg_match_all('/(\?|&)(.+?)=([^&?]*)/i', $url, $matchs);
    if ($mr !== false) {
        for ($i = 0; $i < $mr; $i++) {
            $result[$matchs[2][$i]] = $matchs[3][$i];
        }
    }
    return $result;
}

function test_input($data) {
   $data = trim($data);
   $data = stripslashes($data);
   $data = htmlspecialchars($data);
   $data = urldecode($data);
   return $data;
}
?>