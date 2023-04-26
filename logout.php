<?php
require_once('simplefs-cron.php');
   session_start();
   unset($_SESSION["simplefsvalid"]);
   unset($_SESSION["simplefsuser"]);
   
   session_destroy();
   
   echo '<div align="center">Signed out successfully</div>';
   header('Refresh: 2; URL = index.php');
?>
