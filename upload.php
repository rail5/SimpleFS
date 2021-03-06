<?php


require('config.global.php');
require('functions.global.php');
require('layout.php');

if ($_SESSION['simplefsvalid'] != true) {
    header('location: login.php');
    die();
}

$currentUser = $_SESSION['simplefsuser'];

echo deliverTop("SimpleFS - Upload");

if ($_POST['fsubmitted'] == "true") {
    
$target_dir = "files/";
$target_file = $target_dir . basename($_FILES["upfile"]["name"]);
$uploadOk = 1;
$fileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));


if (file_exists($target_file)) {
  echo "<div align='center'><h1>Error: file already exists</h1></div>";
  $uploadOk = 0;
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

/* This following part shouldn't be deleted though, my apologies but without sanitizing these filenames, could break the whole thing */
/* **** */
if (strpos($target_file, "'") !== false) {
    echo "<div align='center'><h1>Error: Cannot upload files with apostrophes or quote-marks</h1></div>";
    $uploadOk = 0;
}

if (strpos($target_file, '"') !== false) {
        echo "<div align='center'><h1>Error: Cannot upload files with apostrophes or quote-marks</h1></div>";
    $uploadOk = 0;
}

/* Getting a list of all file IDs */

$fileListId = contactDB("SELECT * FROM files;", 0);

if ($uploadOk == 0) {
  echo "<div align='center'><h1>Error: file was not uploaded</h1></div>";
} else {
  if (move_uploaded_file($_FILES["upfile"]["tmp_name"], $target_file)) {
    
    $newFileId = rand(10000, 99999);
    while (in_array($newFileId, $fileListId)) {
        $newFileId = rand(10000, 99999);
    }

    
    /* Write entry to DB */
    
    $publish = contactDB("INSERT INTO files (fileid, filepath, fileowner)
    VALUES ($newFileId, '$target_file', '$currentUser');", 0);
    
    /* Tell the user all is well */
    
    echo "<div align='center'><h1>The file ". htmlspecialchars( basename( $_FILES["upfile"]["name"])). " has been uploaded.</h1></div>";

    
    
  } else {
    echo "<div align='center'><h1>Error uploading file</h1></div>";
  }
}

}

echo deliverMiddle("Upload", '<form action="upload.php" method="post" enctype="multipart/form-data"><input type="hidden" name="fsubmitted" id="fsubmitted" value="true"><input type="file" name="upfile" id="upfile">', '<button><i class="fa fa-upload">Upload</i></button></form>');

echo deliverBottom();

?>
