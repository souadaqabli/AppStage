<?php
// Start the session
session_start();

// Destroy the session
session_destroy();

// Unset all session variables
$_SESSION = array();

// Redirect to login page
header("Location: accueil.html");
exit;
?>
