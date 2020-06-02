<?php

    $FILE_PREFIX = '_file_';
    $handle = opendir('../' . basename(dirname(__FILE__)));
    if ($handle !== false) {
        while (false !== ($filename = readdir($handle))) {
            if (substr($filename, 0, strlen($FILE_PREFIX)) === $FILE_PREFIX) {
                unlink($filename);
                echo 'Deleted '.$filename.PHP_EOL;
            }
        }
        closedir($handle);
    } else {
        die('Failed to read directory');
    }

?>
