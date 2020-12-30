<?php

require('layout.php');

echo deliverTop("SimpleFS - Home");

echo deliverMiddle("File Sharing", '<a href="login.php">Sign In</a> - <a href="logout.php">Sign Out</a>', '<a href="manage.php">Manage Files</a> - <a href="upload.php">Upload Files</a>');

echo deliverBottom();
?>
