<?php

require('config.global.php');
require('layout.php');

if ($_POST['submitted'] == true) {
	if ($_POST['username'] == $adminuser) {
		if (password_verify($_POST['password'], $adminpass)) {
			$_SESSION['simplefsvalid'] = true;
			$_SESSION['simplefsuser'] = "admin";
			// signed in, redirect
			header('location: manage.php');
		} else {
			$_SESSION['simplefsvalid'] = false;
			$_SESSION['simplefsuser'] = NULL;
			die('Invalid username or password');

		}
	} else if ($_POST['username'] == $secuser) {
		if (password_verify($_POST['password'], $secpass)) {
			$_SESSION['simplefsvalid'] = true;
			$_SESSION['simplefsuser'] = "guest";
			// signed in, redirect
			header('location: manage.php');
		} else {
			$_SESSION['simplefsvalid'] = false;
			$_SESSION['simplefsuser'] = NULL;
			die('Invalid username or password');
		}
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
