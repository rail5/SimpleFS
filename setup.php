<!DOCTYPE html>
<html>
<head>
<title>SimpleFS Setup</title>

</head>
<body>
<?php

// Check that file uploads are enabled on the server
if (ini_get('file_uploads') != 1) {
	echo '<div align="center"><h1><font color="FF0000">Warning: Your PHP configuration has disabled file uploads</font><h1><h3>Please check your <b><u>php.ini</u></b> for the line:</h3><i>file_uploads = On</i></div><br><br>';
}

// Check for SQLite and PDO
if (!extension_loaded("pdo_sqlite")) {
	echo '<div align="center"><h1><font color="FF0000">Warning: You do not have the PHP SQLite extension installed.</font></h1><h3>Please install the PHP sqlite3 extension before moving forward</h3></div><br><br>';
}

echo '<div align="center"><b><u>php.ini</u></b> specifies your server\'s <i>maximum upload filesize</i> as:<b> '.ini_get('upload_max_filesize').'</b></div><br>';
?>
	<div align="center">
		<h1>IMPORTANT:</h1>
		<h2>Delete this file <i>immediately</i> after completing set-up</h2><br />
		<form action="setup.php" method="post">
		<input type="hidden" name="formsubmitted" id="formsubmitted" value="true">
Create admin <i>(uploader)</i> account
		<br>
<input type="text" name="username" id="username" placeholder="Admin username" autofocus>
		<br>
<input type="password" name="password" id="password" placeholder="Admin password">
		<br>
<input type="checkbox" name="makeuser" id="makeuser" onchange="seconduser(this)"> Create a second user who can also upload stuff?
		<div id="seconduser">
			<input type="text" name="user2" id="user2" placeholder="Second username">
		<br>
			<input type="password" name="pass2" id="pass2" placeholder="Second password">
		</div>

<script type="text/javascript">
var secform = document.getElementById("seconduser");
secform.style.display = "none";
function seconduser(checkE) {
	if (checkE.checked) {
		secform.style.display = "block";
	} else {
		secform.style.display = "none";
	}
}
</script>
<br>

<input type="submit">
		</form>
		
<?php

require('functions.global.php');

if ($_POST['formsubmitted'] == "true") {
	if (strlen($_POST['username']) < 3 || strlen($_POST['password']) < 3) {
		die('Error: Use a username/password of at least 3 characters');
		exit();
	}
	if ($_POST['makeuser'] == true) {
		if (strlen($_POST['user2']) < 3 || strlen($_POST['pass2']) < 3) {
			die('Error: Use a username/password of at least 3 characters');
			exit();
		}
		if ($_POST['username'] == $_POST['user2']) {
			die('Error: Usernames cannot be identical to each other');
			exit();
		}
		if ($_POST['user2'] == "admin") {
			die('Error: Second username cannot be "admin"');
			exit();
		}
	}
	

	// Create the database
	if (file_exists("./filedb.sqlite")) {
		unlink("./filedb.sqlite");
	}

	touch("./filedb.sqlite");
	$initializeDB = contactDB("CREATE TABLE files (
		fileid int NOT NULL PRIMARY KEY,
		filepath varchar(255) NOT NULL,
		fileowner varchar(255) NOT NULL,
		filedate timestamp NOT NULL
		);", 0);
	
	$add_config_table = contactDB("CREATE TABLE users (
		user_id INTEGER PRIMARY KEY AUTOINCREMENT,
		user_name varchar(255) NOT NULL,
		user_password varchar(255) NOT NULL,
		auto_delete_files_after int NOT NULL
		);", 0);
				
	echo '<br>Initialized SQLite database';

	// Populate the 'users' table
	$admin_password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
	$user_password_hash = password_hash($_POST['pass2'], PASSWORD_DEFAULT);

	$add_user = contactDB("INSERT INTO users (user_name, user_password, auto_delete_files_after)
		VALUES ('".$_POST['username']."', '$admin_password_hash', -1);", 0);

	if ($_POST['makeuser']) {
		$add_user = contactDB("INSERT INTO users (user_name, user_password, auto_delete_files_after)
			VALUES ('".$_POST['user2']."', '$user_password_hash', -1);", 0);
	}
	
	echo '<br>User(s) created.';

	// Delete setup.php
	unlink("./setup.php");
	
	header('location: index.php');
}

?>

	</div>
</body>
</html>
