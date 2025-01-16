<?php
session_start();
session_unset(); // Unset all session variables
session_destroy(); // Destroy the session
setcookie(session_name(), '', time() - 3600, '/', '', true, true); // Secure and HttpOnly flags

header("Location: latestlogin.php"); // Redirect to the login page
exit();
?>
