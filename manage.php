<?php

require_once('layout.php');
require_once('functions.global.php');
require_once('simplefs-cron.php');

if ($_SESSION['simplefsvalid'] != true) {
	header('location: login.php');
	die();
}

$currentUser = $_SESSION['simplefsuser'];

/* Obtain list of current user's files */

$myFilesId = contactDB("SELECT * FROM files WHERE fileowner=$currentUser;", 0);
$myFilesName = contactDB("SELECT * FROM files where fileowner=$currentUser;", 1);

$nFiles = count($myFilesId);

$i = 0;

/* If the user elected to delete some files, delete those files & then re-load the list */
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

	unset($myFilesId);
	unset($myFilesName); // Re-loading list after file deletion
	unset($nFiles);

	$myFilesId = contactDB("SELECT * FROM files WHERE fileowner=$currentUser;", 0);
	$myFilesName = contactDB("SELECT * FROM files where fileowner=$currentUser;", 1);
}

/* If the user elected to change her settings, make the change now */
if ($_POST['settings-changed'] == true) {

	$input_auto_delete_enabled = ($_POST['auto_delete_enabled'] == true);

	$final_auto_delete_length = -1; // -1 for 'disabled'

	if ($input_auto_delete_enabled) {
		$input_auto_delete_length = $_POST['auto_delete_length'];
		$input_auto_delete_unit = $_POST['auto_delete_unit'];

		if (is_numeric($input_auto_delete_length)) {
			// Convert chosen unit to seconds:
			if ($input_auto_delete_unit == "minutes") {
				$final_auto_delete_length = ($input_auto_delete_length * 60);
			} else if ($input_auto_delete_unit == "hours") {
				$final_auto_delete_length = ($input_auto_delete_length * 60 * 60);
			} else if ($input_auto_delete_unit == "days") {
				$final_auto_delete_length = ($input_auto_delete_length * 24 * 60 * 60);
			} else if ($input_auto_delete_unit == "weeks") {
				$final_auto_delete_length = ($input_auto_delete_length * 7 * 24 * 60 * 60);
			}
		}

	}

	$update_settings = contactDB("UPDATE users SET auto_delete_files_after=$final_auto_delete_length WHERE user_id=$currentUser", 0);
	$noticeText = "<div align='center'><h1>Settings successfully changed</h1></div><br>".PHP_EOL;

	if ($input_auto_delete_enabled && !is_numeric($input_auto_delete_length)) {
		$notice = "<div align='center'><h1>Error: Non-numeric input for auto-deletion time</h1></div><br>".PHP_EOL;
	}
}

$auto_delete_after_length = contactDB("SELECT auto_delete_files_after FROM users WHERE user_id=$currentUser;", 0);
$auto_delete_after_length = $auto_delete_after_length[0];

$auto_delete_enabled = ($auto_delete_after_length > 0);

// Determine pre-set "placeholder" value for auto_delete_length
$preset_length = 10;
$preset_unit = "minutes";

if ($auto_delete_enabled) {
	$one_minute = 60;
	$one_hour = $one_minute * 60;
	$one_day = $one_hour * 24;
	$one_week = $one_day * 7;

	// Is it evenly divisible by weeks, days, or hours?
	if ($auto_delete_after_length % $one_week == 0) {
		$preset_length = ($auto_delete_after_length / $one_week);
		$preset_unit = "weeks";
	} else if ($auto_delete_after_length % $one_day == 0) {
		$preset_length = ($auto_delete_after_length / $one_day);
		$preset_unit = "days";
	} else if ($auto_delete_after_length % $one_hour == 0) {
		$preset_length = ($auto_delete_after_length / $one_hour);
		$preset_unit = "hours";
	} else {
		$preset_length = (floor($auto_delete_after_length / $one_minute));
		$preset_unit = "minutes";
	}
}

function set_preset_unit($unit) {
	global $preset_unit;
	if ($unit == $preset_unit) {
		return ' selected="selected"';
	}
	return "";
}

$settingsForm = '<form action="manage.php" method="post" id="settings">'.PHP_EOL.'<input type="hidden" name="settings-changed" id="settings-changed" value="true">'.PHP_EOL;
$settingsForm .= '<input type="checkbox" name="auto_delete_enabled" id="auto_delete_enabled"';

if ($auto_delete_enabled) {
	$settingsForm .= ' checked';
}

$settingsForm .= '> <label for="auto_delete_enabled">Auto-delete files after: </label><br>'.PHP_EOL;
$settingsForm .= '<div id="settingsForm"> &nbsp; &nbsp; <input type="number" name="auto_delete_length" value="'.$preset_length.'" style="width: 20% !important;"> <i class="fa"> &nbsp; </i> <select name="auto_delete_unit" form="settings" style="width: 60% !important;">'.PHP_EOL;
$settingsForm .= '<option value="minutes"'.set_preset_unit("minutes").'>Minutes</option>'.PHP_EOL;
$settingsForm .= '<option value="hours"'.set_preset_unit("hours").'>Hours</option>'.PHP_EOL;
$settingsForm .= '<option value="days"'.set_preset_unit("days").'>Days</option>'.PHP_EOL;
$settingsForm .= '<option value="weeks"'.set_preset_unit("weeks").'>Weeks</option>'.PHP_EOL;
$settingsForm .= '</select></div><br><button><i class="fa">Update Settings</i></button></form>';

$nFiles = count($myFilesId);

$outputContents = "";

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

echo deliverMiddle("Manage", $settingsForm.PHP_EOL.'<hr>'.PHP_EOL.'<form action="manage.php" method="post">'.PHP_EOL.'<input type="hidden" name="msubmitted" id="msubmitted" value="true">'.PHP_EOL.$outputContents, '<button><i class="fa">Delete Selected Files</i></button></form><br><br><form action="index.php"><button><i class="fa">Return Home</i></button></form>');

echo deliverBottom();

?>
