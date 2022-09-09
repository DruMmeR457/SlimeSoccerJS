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
$posted = file_get_contents('php://input');
file_put_contents($filename, $posted.PHP_EOL, FILE_APPEND | LOCK_EX);

return "OK"

?>
