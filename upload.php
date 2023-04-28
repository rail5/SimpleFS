<?php


require_once('functions.global.php');
require_once('layout.php');
require_once('simplefs-cron.php');

if ($_SESSION['simplefsvalid'] != true) {
	header('location: login.php');
	die();
}

$currentUser = $_SESSION['simplefsuser'];

echo deliverTop("SimpleFS - Upload");

if ($_POST['fsubmitted'] == "true") {
	
	$target_dir = "files/";

	// If the user is uploading multiple files, we'll ZIP them
	if (count($_FILES["upfile"]["name"]) > 1) {
		$target_file = $target_dir . "SimpleFS_User$currentUser " . date('Y-m-d H_i_s') . ".zip";
	} else {
		$target_file = $target_dir . basename($_FILES["upfile"]["name"][0]);
	}

	$uploadOk = true;
	$fileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));


	if (file_exists($target_file)) {
	echo "<div align='center'><h1>Error: file already exists</h1></div>";
	$uploadOk = false;
	}

	/* *************************** */
	/* Disallow risky file formats */
	/* Delete or comment out this section if you don't care or otherwise protected the uploads folder from public access */
	/* For anyone who's not sure why this is here, say for example somebody uploads a PHP file manager to your site. They've now pwned your server. So that's disallowed here */
	/* It's not the biggest risk though, as sign-in is required, so if you're confident in your password strength, etc, this section can be deleted or commented out */
	/* ************************** */

	if($fileType == "php" || $fileType == "htm" || $fileType == "html" || $fileType == "phtml" || $fileType == "asp" || $fileType == "aspx" || $fileType == "axd" || $fileType == "asx" || $fileType == "asmx" || $fileType == "ashx" || $fileType == "cfm" || $fileType == "xhtml" || $fileType == "jhtml" || $fileType == "pl" || $fileType == "php4" || $fileType == "php3" || $fileType == "php5" || $fileType == "php6" || $fileType == "php7" || $fileType == "rhtml" || $fileType == "shtml") {
	die("Error: File type disallowed by security measure. Edit upload.php if you'd like to allow these types of files; the relevant security section is clearly marked");
	$uploadOk = 0;
	}

	/* End of the aforementioned alterable security section */
	/* **************************************************** */

	// TODO: Replace "sanitization" with prepared statements

	if (strpos($target_file, "'") !== false || strpos($target_file, '"') !== false) {
		echo "<div align='center'><h1>Error: Cannot upload files with apostrophes or quote-marks</h1></div>";
		$uploadOk = false;
	}

	/* Getting a list of all file IDs */

	$fileListId = contactDB("SELECT * FROM files;", 0);

	if ($uploadOk == false) {
		echo "<div align='center'><h1>Error: file was not uploaded</h1></div>";
	} else {

		// If the user is uploading multiple files, we'll ZIP them
		if (count($_FILES["upfile"]["name"]) > 1) {
			$zip_archive = new ZipArchive;
			$zip_archive->open($target_file, ZipArchive::CREATE);

			foreach ($_FILES["upfile"]["tmp_name"] as $key=>$tmp_file_name) {
				$zip_archive->addFile($tmp_file_name, basename($_FILES["upfile"]["name"][$key]));
			}

			$file_upload_complete = $zip_archive->close();
		} else {
			$file_upload_complete = move_uploaded_file($_FILES["upfile"]["tmp_name"][0], $target_file);
		}

		if ($file_upload_complete) {
			
			$newFileId = rand(10000, 99999);
			while (in_array($newFileId, $fileListId)) {
				$newFileId = rand(10000, 99999);
			}

			
			/* Write entry to DB */

			$current_date = time();
			
			$publish = contactDB("INSERT INTO files (fileid, filepath, fileowner, filedate)
			VALUES ($newFileId, '$target_file', $currentUser, $current_date);", 0);
			
			/* Tell the user all is well */
			
			echo "<div align='center'><h1>Uploaded!</h1></div>";

			/* Provide the download link */

			$download_link = get_download_link($newFileId);

			// Javascript is used for the "copy to clipboard" button
			echo "<script type=\"text/javascript\">

function resetCopyButton() {
	document.getElementById(\"copybutton\").innerHTML = \"<strong>Copy Link</strong>\";
}
			    
function copyToClipboard() {
	let downloadlink = \"$download_link\";
	if (navigator.clipboard.writeText(downloadlink)) {
		document.getElementById(\"copybutton\").innerHTML = \"<strong>Copied!</strong>\";
		const resetButtonTimeout = setTimeout(resetCopyButton, 3000);
	} else {
		document.getElementById(\"copybutton\").innerHTML = \"<strong>Could not copy</strong>\";
		const resetButtonTimeout = setTimeout(resetCopyButton, 3000);
	}
}
</script>";
			// Display the link
			// If we're over SSL or on localhost, display a "copy to clipboard" button
			//   (The browser navigator.clipboard API is only available over SSL or localhost)
			//   (This is the answer to the GH bug report #7)
			echo "<div align='center' id='download-link' style='pointer-events: auto !important;'><input type='text' value='$download_link' style='width: 40% !important; cursor: text !important;' disabled>";
			
			$javascript_can_copy_to_clipboard = (isSSL() || isLocalhost());

			if ($javascript_can_copy_to_clipboard) {
				echo "<div style='font-size: 5px !important;'>&nbsp;</div><button id='copybutton' onclick='copyToClipboard()' style='background-color: rgba(255,255,255,0.3) !important; font-size: 18px !important;'><strong>Copy Link</strong></button>";
			}
			
			echo "</div>";
			
		} else {
			echo "<div align='center'><h1>Error uploading file</h1></div>";
		}
	}

}

echo '<script type="text/javascript">
function checkLimit(files) {
	if (files.length > '.ini_get('max_file_uploads').') {
		alert("You may only upload up to '.ini_get('max_file_uploads').' files at once on this server\nFor administrators: this setting can be changed in your php.ini");

		let list = new DataTransfer;

		for (let i=0; i<'.ini_get('max_file_uploads').'; i++) {
			list.items.add(files[i]);
		}

		document.getElementById("upfile").files = list.files;
	}
}
</script>';

echo deliverMiddle("Upload", '<form action="upload.php" method="post" enctype="multipart/form-data"><input type="hidden" name="fsubmitted" id="fsubmitted" value="true"><input type="file" name="upfile[]" id="upfile" onChange="checkLimit(this.files)" multiple>', '<button><i class="fa fa-upload">Upload</i></button></form>');

echo deliverBottom();

?>
