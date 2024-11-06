<?php
session_start(); 

// Unset all session variables
$_SESSION = [];

session_destroy(); 

header("Location: login.php");
exit();
?>
