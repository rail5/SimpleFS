<?php

/* General Global Include Functions Here */

function contactDB($query, $column) {
	
	// $query: the SQLite query to the database
	// $column: the column you're asking the DB to report back on
	// 1: fileid
	// 2: filepath (link)
	// 3: fileowner (user id)
	// 4: filedate (uploaded date, unix timestamp)
	// ie, $result = contactDB("SELECT * FROM files WHERE fileowner='admin';", 2);
	// populates the $result[] array with the file paths to every file owned by the admin user
	
	$dbresult = array();
	
	$datab = 'sqlite:./filedb.sqlite';
	$dbpdo = new PDO($datab) or die ("Fatal Error: Can't open the database");
	
	foreach ($dbpdo->query($query) as $row) {
		array_push($dbresult, $row[$column]);
}

	$dbpdo = NULL; // Closing connection
	return $dbresult;
}

function isLocalhost() {
	// Returns true if the site is being served through 127.0.0.1
	// This is used by upload.php in determining whether we can access the browser navigator.clipboard API from JavaScript to copy download links to the user's clipboard
	
	return ( ($_SERVER['HTTP_HOST'] == "127.0.0.1") || ($_SERVER['HTTP_HOST'] == "localhost") );
}

function isSSL() {
	// Returns true if on HTTPS, false if on HTTP

        return ( (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443);
}

function get_download_link($file_id) {
	// Returns the full (absolute) link to download.php?id= ($file_id)

        // Check if we're on HTTPS or HTTP
        if (isSSL()) {
                $url = "https://";
        } else {
                $url = "http://";
        }

        // Get the URL of the current page
	// e.g: https://127.0.0.1/some/directory/SimpleFS/upload.php
        $url .= $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];

        // Find the last slash (the directory we're in, rather than the file)
        $position_of_last_slash = strrpos($url, "/");

        // Trim the URL to just that directory
	// e.g: https://127.0.0.1/some/directory/SimpleFS/
        $url = substr($url, 0, $position_of_last_slash + 1);

        // Append download.php?id= (the id supplied)
	// e.g: https://127.0.0.1/some/directory/SimpleFS/download.php?id=12345
        $url .= "download.php?id=$file_id";

        return $url;
}

?>
