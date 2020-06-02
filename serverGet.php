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

// Get a list of all files that start with '_file_' except the file containing
// messages that this client has sended itsself ('_file_'.$unique).
$all = array ();
$handle = opendir ( '../' . basename(dirname(__FILE__)));
if ($handle !== false) {
    while (false !== ($filename = readdir($handle))) {
	if (startsWith($filename, '_file_' /* .$room */) && !(startsWith($filename, '_file_' /*.$room*/ .$unique) )) {
	    $all [] .= $filename;
	}
    }
    closedir( $handle );
}

$MUTLPLE_EVENT_STRING = '_MULTIPLEVENTS_';
if (count($all)!=0) {

    // show and output the first file that is not empty
    for ($x=0; $x<count($all); $x++) {
	$filename = $all[$x];

	// prevent sending empty files
	if (filesize($filename) == 0) {
	    unlink($filename);
	    continue;
	}

	$contents = file_get_contents($filename, 'c+b');
	$event_partition_index = strpos($contents, $MUTLPLE_EVENT_STRING);
	if ($event_partition_index === false) {
	    echo $contents;
	    unlink($filename);
	} else {
	    echo substr($contents, 0, $event_partition_index);
	    $new_contents = substr($contents, $event_partition_index + strlen($MUTLPLE_EVENT_STRING));
	    file_put_contents($filename, $new_contents, LOCK_EX);
	}
	break;
    }

} else {
	echo '{"retry": 1000}'; // shorten the 3 seconds to 1 sec
}

?>
