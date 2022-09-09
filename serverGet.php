<?php
// SEE https://github.com/nielsbaloe/webrtc-php
// TODO:
// - read all files from the folder, not just the first one
// - look at this one, might demonstrate slightly better: https://shanetully.com/2014/09/a-dead-simple-webrtc-example/

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


//header('Content-Type: text/event-stream');
header('Content-Type: text/plain');
header('Cache-Control: no-cache'); // recommended

function startsWith($haystack, $needle) {
    return (substr($haystack, 0, strlen($needle) ) === $needle);
}

// look up previous pairings
$paired_clients = array();
$this_clients_pair = null;
$handle = fopen("pairings.db", "r");
if ($handle) {
    while (($line = fgets($handle)) !== false && $this_clients_pair === null) {
        // process the line read.
        $pair = preg_split("/[=\s]+/", $line);
        if ($unique == $pair[0]) {
            $this_clients_pair = $pair[1];
        }
        if ($unique == $pair[1]) {
            $this_clients_pair = $pair[0];
        }
        $paired_clients[$pair[0]] = true;
        $paired_clients[$pair[1]] = true;
    }

    fclose($handle);
} else {
    // error opening the file.
    die('Could not read client pairings DB');
}

$paired_filename = null;
if ($this_clients_pair !== null) {
    // This client already has a pair
    $paired_filename = '_file_' . $this_clients_pair;
    if (!file_exists($paired_filename)) {
        $paired_filename = null;
    }
} else {
    // Find a client without a pair (that isn't itself)
    $handle = opendir('../' . basename(dirname(__FILE__)));
    if ($handle !== false) {
        while (false !== ($filename = readdir($handle))) {
	    if (startsWith($filename, '_file_' /* .$room */) && !(startsWith($filename, '_file_' /*.$room*/ . $unique) )) {
                $potential_filename_client_id = preg_split("/_file_/", $filename)[1];
                if (!array_key_exists($potential_filename_client_id, $paired_clients)) {
                    $paired_filename = $filename;
                    break;
                }
	    }
        }
        closedir($handle);
    }
}

if ($paired_filename !== null) {
    $filename = $paired_filename;
    // prevent sending empty files
    if (filesize($filename) == 0) {
        unlink($filename);
    }
    
    #$contents = file_get_contents($filename, 'c+b');
    $handle = fopen($filename, "rw");
    if ($handle) {
        $first_line = fgets($handle);
        $new_contents = '';
        while (($line = fgets($handle)) !== false) {
            $new_contents .= $line;
        }
    } else {
        die('Could not read file '.$filename);
    }
    fclose($handle);
    if ($new_contents === '') {
        unlink($filename);
    } else {
        file_put_contents($filename, $new_contents, LOCK_EX);
    }
    echo $first_line;

    // Pair the two clients
    if ($this_clients_pair === null) {
        file_put_contents('pairings.db', $unique.'='.$potential_filename_client_id.PHP_EOL, FILE_APPEND | LOCK_EX);
    }
} else {
    echo '{"retry": 3000}';
}

?>
