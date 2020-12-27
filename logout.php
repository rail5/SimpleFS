<?php
   session_start();
   unset($_SESSION["valid"]);
   unset($_SESSION["user"]);
   
   session_destroy();
   
   echo '<div align="center">Signed out successfully</div>';
   header('Refresh: 2; URL = index.php');
?>