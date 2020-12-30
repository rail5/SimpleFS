<?php

/* General Global Include Functions Here */

function contactDB($query, $column) {
    
    // $query: the SQLite query to the database
    // $column: the column you're asking the DB to report back on
    // 1: fileid
    // 2: filepath
    // 3: fileowner
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

?>
