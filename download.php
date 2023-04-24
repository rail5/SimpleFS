<?php

require('config.global.php');
require('functions.global.php');
require('layout.php');

if (!isset($_GET['id'])) {
	header('location: index.php'); // user loaded without requesting file by id
	die();
}

if (!is_numeric($_GET['id'])) {
	header('location: index.php'); // user requested non-numeric (invalid) file id, damned fuzzers
	die();
}

$reqFile = $_GET['id'];

$fetched = contactDB("SELECT * FROM files WHERE fileid='$reqFile';", 1);

$realFile = (count($fetched) != 0); // Set realFile to true if we found the file id, false if we didn't find it

if (!$realFile) {
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
