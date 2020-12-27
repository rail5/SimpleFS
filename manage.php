<?php


require('config.global.php');
require('layout.php');

require('filedb.php');

if ($_SESSION['valid'] != true) {
    header('location: login.php');
    die();
}

$nFiles = count($fileListId);

if ($nFiles != count($fileListLocation) || $nFiles != count($fileListOwner)) {
    die('<div align="center"><h1>Error: Array lengths in file database don\'t match</h1><br><h3>Something\'s gone horribly wrong</h3></div>');
}

$i = 0;

$myFiles = array(); // This array will be populated with a list of references to the files belonging to our currently signed-in user
// e.g., if files 1, 2, 4, 6, and 8 belong to user #2, but user #1 is signed in,
// This array will be populated with references to files 3, 5, and 7
// And the inverse if user #2 is signed in

while ($i < $nFiles) {
    if ($fileListOwner[$i] == $_SESSION['user']) {
        array_push($myFiles, $i);
    }
    $i = $i + 1;
}


$nMyFiles = count($myFiles); // Preparing to iterate through the newly populated array

$ni = 0;

$outputContents = "";

if ($_POST['msubmitted'] == true) {
    $nni = 0;
    
    while ($nni < $nMyFiles) {
        if ($_POST["file$nni"] == "marked") {
            unlink($fileListLocation[$myFiles[$nni]]); // Delete the selected file
            
            unset($fileListId[$myFiles[$nni]]); // Remove element from array
            unset($fileListLocation[$myFiles[$nni]]);
            unset($fileListOwner[$myFiles[$nni]]);
            
        }
        $nni = $nni + 1;
    }
    
    $newFileDb = fopen('filedb.php', 'w');

    $newContents = "<?php".PHP_EOL;

    $newContents = $newContents.'$fileListId = array(';

    foreach ($fileListId as &$nvalue) {
        $newContents = $newContents."'$nvalue', ";
    }

    unset($nvalue);

    $newContents = substr($newContents, 0, -2);

    $newContents = $newContents.");".PHP_EOL;

    $newContents = $newContents.'$fileListLocation = array(';

    foreach ($fileListLocation as &$nvalue) {
        $newContents = $newContents."'$nvalue', ";
    }

    unset($nvalue);

    $newContents = substr($newContents, 0, -2);

    $newContents = $newContents.");".PHP_EOL;

    $newContents = $newContents.'$fileListOwner = array(';

    foreach ($fileListOwner as &$nvalue) {
        $newContents = $newContents."'$nvalue', ";
    }

    unset($nvalue);

    $newContents = substr($newContents, 0, -2);

    $newContents = $newContents.");".PHP_EOL."?>";
    
    if ($newContents == '<?php
$fileListId = arra);
$fileListLocation = arra);
$fileListOwner = arra);
?>') {
    $newContents = '<?php
$fileListId = array();
$fileListLocation = array();
$fileListOwner = array();
?>';
}
// I realize that this (directly above) is like duct taping a hole in a ship
// But it's literally the only possible case when substr($newContents, 0, -2) ever presents a problem
// So I don't really think it makes any difference whether I 'patch this hole' or restructure it from bottom-up
// I mean, it may make a difference with future developments/changes/updates/etc but I don't plan to maintain this lol
// Write and publish and forget, that's my motto

    fwrite($newFileDb, $newContents);

    fclose($newFileDb);
    
    $noticeText = "<div align='center'><h1>Files successfully deleted</h1></div><br>".PHP_EOL;
}

if ($nMyFiles == 0) {
    $outputContents = "You haven't uploaded any files yet";
} else {
    while ($ni < $nMyFiles) {
        $fileName = str_replace("files/", "", $fileListLocation[$myFiles[$ni]]);
        $outputContents = $outputContents.'<div class="field"> <input type="checkbox" name="file'.$ni.'" id="file'.$ni.'" value="marked"><label for="file'.$ni.'"><a href="download.php?id='.$fileListId[$myFiles[$ni]].'">'.$fileName.'</a></label></div>'.PHP_EOL;
        $ni = $ni + 1;
    }
}

echo deliverTop("SimpleFS - Manage");

if (isset($noticeText)) {
    echo $noticeText;
}

echo deliverMiddle("Manage", '<form action="manage.php" method="post">'.PHP_EOL.'<input type="hidden" name="msubmitted" id="msubmitted" value="true">'.PHP_EOL.$outputContents, '<button><i class="fa">Delete Selected Files</i></button></form><br><br><form action="index.php"><button><i class="fa">Return Home</i></button></form>');

echo deliverBottom();

?>