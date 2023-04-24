<?php

require('layout.php');
require('functions.global.php');

if ($_SESSION['simplefsvalid'] != true) {
	header('location: login.php');
	die();
}

$currentUser = $_SESSION['simplefsuser'];

/* Obtain list of current user's files */

if ($currentUser == "admin") {
	$myFilesId = contactDB("SELECT * FROM files;", 0);
	$myFilesName = contactDB("SELECT * FROM files;", 1);
} else {
	$myFilesId = contactDB("SELECT * FROM files WHERE fileowner='$currentUser';", 0);
	$myFilesName = contactDB("SELECT * FROM files where fileowner='$currentUser';", 1);
}

$nFiles = count($myFilesId);

$i = 0;

$outputContents = "";

if ($_POST['msubmitted'] == true) {
	
	while ($i < $nFiles) {
		if ($_POST["file$myFilesId[$i]"] == "marked") {
			unlink($myFilesName[$i]); // Delete selected file
			
			$dbChange = contactDB("DELETE FROM files WHERE fileid='$myFilesId[$i]';", 0); // Update database
		}
		$i = $i + 1;
	}
	$i = 0; // Reset iteration for next use
	$noticeText = "<div align='center'><h1>Files successfully deleted</h1></div><br>".PHP_EOL;
}

unset($myFilesId);
unset($myFilesName); // Re-loading list after file deletion
unset($nFiles);

if ($currentUser == "admin") {
	$myFilesId = contactDB("SELECT * FROM files;", 0);
	$myFilesName = contactDB("SELECT * FROM files;", 1);
} else {
	$myFilesId = contactDB("SELECT * FROM files WHERE fileowner='$currentUser';", 0);
	$myFilesName = contactDB("SELECT * FROM files where fileowner='$currentUser';", 1);
}
$nFiles = count($myFilesId);

if ($nFiles == 0) {
	$outputContents = "You haven't uploaded any files yet";
} else {
	while ($i < $nFiles) {
		$fileName = str_replace("files/", "", $myFilesName[$i]);
		$outputContents = $outputContents.'<div class="field"> <input type="checkbox" name="file'.$myFilesId[$i].'" id="file'.$myFilesId[$i].'" value="marked"><label for="file'.$myFilesId[$i].'"><a href="download.php?id='.$myFilesId[$i].'">'.$fileName.'</a></label></div>'.PHP_EOL;
		$i = $i + 1;
	}
}

echo deliverTop("SimpleFS - Manage");

if (isset($noticeText)) {
	echo $noticeText;
}

echo deliverMiddle("Manage", '<form action="manage.php" method="post">'.PHP_EOL.'<input type="hidden" name="msubmitted" id="msubmitted" value="true">'.PHP_EOL.$outputContents, '<button><i class="fa">Delete Selected Files</i></button></form><br><br><form action="index.php"><button><i class="fa">Return Home</i></button></form>');

echo deliverBottom();

?>
