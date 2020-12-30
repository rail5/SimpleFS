<?php

require('config.global.php');
require('functions.global.php');
require('layout.php');

$notRealFile = 0;


if (!isset($_GET['id'])) {
    header('location: index.php'); // user loaded without requesting file by id
}

if (!is_numeric($_GET['id'])) {
    header('location: index.php'); // user requested non-numeric (invalid) file id, damned fuzzers
}

$reqFile = $_GET['id'];

$fetched = contactDB("SELECT * FROM files WHERE fileid='$reqFile';", 1);

if (count($fetched) == 0) {
    $notRealFile = 1; // user requested invalid (unmatched) file id, possibly a deleted file
}

if ($notRealFile == 1) {
    echo deliverTop("SimpleFS - Download");

    echo deliverMiddle("File Not Found", "The file you requested doesn't exist on this server", "");
    
    echo deliverBottom();
} else {
    $fileName = str_replace("files/", "", $fetched[0]);
    
        if ($_GET['dl'] == "true") {

        header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: Binary"); 
        header("Content-disposition: attachment; filename=\"" .$fileName. "\""); 
        readfile($fetched[0]); 
    } else {
        echo deliverTop("SimpleFS - Download");
        echo deliverMiddle("Download", $fileName, '<a href="download.php?id='.$_GET['id'].'&dl=true"><i class="fa fa-download fa-5x"></i></a>');
        echo deliverBottom();

    }

}


?>
