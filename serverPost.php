<?php
// SEE https://github.com/nielsbaloe/webrtc-php

//CORS
header('Access-Control-Allow-Origin: *', false);
// A unique identifier (not necessary when working with websockets)
if (!isset($_GET['unique'])) {
    die('no identifier');
}
$unique=$_GET['unique'];
if (strlen($unique)==0 || ctype_digit($unique)===false) {
    die('not a correct identifier');
}

    
// Add the new message to file
$filename = '_file_' /*.$room*/ . $unique;
if (file_exists($filename) && filesize($filename) != 0) {
    file_put_contents($filename, '_MULTIPLEVENTS_', FILE_APPEND | LOCK_EX);
}
$posted = file_get_contents('php://input');
file_put_contents($filename, $posted, FILE_APPEND | LOCK_EX);

?>
