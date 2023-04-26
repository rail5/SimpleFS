<?php

/***
 * index.php:
 * Deliver main page
 */

 // Check if setup has been completed
 // If the file 'setup.php' still exists, the user should be redirected to it
 if (file_exists("./setup.php")) {
	header('location: ./setup.php');
	die();
}

require_once('layout.php');
require_once('simplefs-cron.php');


echo deliverTop("SimpleFS - Home");

echo deliverMiddle("File Sharing", '<a href="login.php">Sign In</a> - <a href="logout.php">Sign Out</a>', '<a href="manage.php">Manage Files</a> - <a href="upload.php">Upload Files</a>');

echo deliverBottom();
?>
