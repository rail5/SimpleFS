<?php

/***
 * simplefs-cron.php:
 * Manages pseudo-cron jobs, including:
 * 	- Auto-deletion of old files according to user-set rules
 */

 require_once 'functions.global.php';

 // Check if any files should be auto-deleted

 $currentTime = time();

 $AutoDeleteSettings = contactDB("SELECT auto_delete_files_after FROM users;", 0);

$i = 0;

while ($i < count($AutoDeleteSettings)) {
	$user_id = $i + 1; // Arrays start from zero, the SQL rows start from 1

	$delete_after_time = $AutoDeleteSettings[$i];

	if (!($delete_after_time > 0)) {
		// If the user doesn't have "auto-delete files" enabled, there's nothing to be done here
		$i = $i + 1;
		continue; // Jump to the next user
	}
	// Otherwise, carry on and delete old files

	$cutoff_point = ($currentTime - $delete_after_time);

	// Get list of files older than the cutoff point
	$FileIDs = contactDB("SELECT fileid FROM files WHERE fileowner=$user_id AND filedate <= $cutoff_point;", 0);

	// Delete files in that list
	foreach ($FileIDs as $file) {
		
		// Get file path
		$file_path = contactDB("SELECT filepath FROM files WHERE fileid=$file;", 0);
		$file_path = $file_path[0];

		unlink($file_path); // Delete file
			
		$dbChange = contactDB("DELETE FROM files WHERE fileid=$file;", 0); // Update database
	}

	$i = $i + 1;
}