<?php

require_once('functions.global.php');
require_once('layout.php');
require_once('simplefs-cron.php');

if ($_POST['submitted'] == true) {

	// Verify the username is legit
	$valid_usernames = contactDB("SELECT user_name FROM users", 0);
	$login_username = $_POST['username'];
        if (!in_array($login_username, $valid_usernames)) {
                die('Invalid username or password');
        }

        $relevant_password_hash = contactDB("SELECT user_password FROM users WHERE user_name='$login_username';", 0);
	$relevant_password_hash = $relevant_password_hash[0];

	$relevant_user_id = contactDB("SELECT user_id FROM users WHERE user_name='$login_username';", 0);
	$relevant_user_id = $relevant_user_id[0];
	
	if (password_verify($_POST['password'], $relevant_password_hash)) {
		$_SESSION['simplefsvalid'] = true;
		$_SESSION['simplefsuser'] = ($relevant_user_id);
		header('location: manage.php');
	} else {
		$_SESSION['simplefsvalid'] = false;
		$_SESSION['simplefsuser'] = NULL;
		die('Invalid username or password');
	}
}

echo deliverTop("SimpleFS - Sign in");

echo deliverMiddle("Sign in", '<form action="login.php" method="post"><input type="hidden" name="submitted" id="submitted" value="true"><input type="text" name="username" id="username" placeholder="Username" autofocus><br><input type="password" name="password" id="password" placeholder="Password">', '<button><i class="fa fa-sign-in">Sign in</i></button></form>');

echo deliverBottom();

?>
