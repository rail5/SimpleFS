<!DOCTYPE html>
<html>
<head>
<title>SimpleFS Setup</title>

</head>
<body>
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
    }

    $myfile = fopen("config.global.php", "w") or die("Fatal error: can't open file. Does your webserver have write permissions here?");
    
    $admhash = password_hash($_POST['password'], PASSWORD_DEFAULT);
    if ($_POST['makeuser'] == true) {
        $usrhash = password_hash($_POST['pass2'], PASSWORD_DEFAULT);
    }
    fwrite($myfile, "<?php".PHP_EOL);
    fwrite($myfile, '$adminuser = \''.$_POST['username'].'\';'.PHP_EOL);
    fwrite($myfile, '$adminpass = \''.$admhash.'\';'.PHP_EOL);

    if ($_POST['makeuser'] == true) {
       fwrite($myfile, '$secuser = \''.$_POST['user2'].'\';'.PHP_EOL);
       fwrite($myfile, '$secpass = \''.$usrhash.'\';'.PHP_EOL);
    }
    fwrite($myfile, "?>".PHP_EOL);
    
    echo '<br>User(s) created. I mean, check config.global.php to make sure, but then delete this file ASAP.';
    
    if (!file_exists("./filedb.sqlite")) {
        touch("./filedb.sqlite");
        $initializeDB = contactDB("CREATE TABLE files (
            fileid int NOT NULL PRIMARY KEY,
            filepath varchar(255) NOT NULL,
            fileowner varchar(255) NOT NULL
            );", 0);
                
        echo '<br>Initialized file database';
    }
    
}

?>

    </div>
</body>
</html>
